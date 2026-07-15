from fastapi import FastAPI, File, UploadFile, Form
from fastapi.middleware.cors import CORSMiddleware
import cv2
import numpy as np
from PIL import Image
import io

app = FastAPI(title="Garment Measurement Service")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["*"],
    allow_headers=["*"],
)

MAT_WIDTH_CM   = 80.0
MAT_HEIGHT_CM  = 100.0
MARKER_SIZE_CM = 8.0

def load_image_shirt(file_bytes: bytes):
    image = Image.open(io.BytesIO(file_bytes)).convert("RGB")
    return cv2.cvtColor(np.array(image), cv2.COLOR_RGB2BGR)

def load_image_pant(file_bytes: bytes):
    image = Image.open(io.BytesIO(file_bytes)).convert("RGB")
    img   = cv2.cvtColor(np.array(image), cv2.COLOR_RGB2BGR)

    # Resize to max 1200px — reduces processing time significantly
    h, w  = img.shape[:2]
    if max(h, w) > 1200:
        scale = 1200 / max(h, w)
        img   = cv2.resize(img, (int(w * scale), int(h * scale)))

    return img

def to_python_types(obj):
    if isinstance(obj, dict):
        return {k: to_python_types(v) for k, v in obj.items()}
    elif isinstance(obj, (list, tuple)):
        return [to_python_types(v) for v in obj]
    elif isinstance(obj, np.integer):
        return int(obj)
    elif isinstance(obj, np.floating):
        return float(obj)
    elif isinstance(obj, np.ndarray):
        return obj.tolist()
    return obj

def detect_aruco_markers(image):
    gray       = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
    aruco_dict = cv2.aruco.getPredefinedDictionary(cv2.aruco.DICT_4X4_50)
    parameters = cv2.aruco.DetectorParameters()
    detector   = cv2.aruco.ArucoDetector(aruco_dict, parameters)

    corners, ids, _ = detector.detectMarkers(gray)

    if ids is None or len(ids) < 4:
        return None, ids

    id_to_center = {}
    for i, marker_id in enumerate(ids.flatten()):
        if marker_id in [0, 1, 2, 3]:
            c  = corners[i][0]
            cx = int(c[:, 0].mean())
            cy = int(c[:, 1].mean())
            id_to_center[int(marker_id)] = (cx, cy)

    if not all(k in id_to_center for k in [0, 1, 2, 3]):
        return None, ids

    return {
        'top_left':     id_to_center[0],
        'top_right':    id_to_center[1],
        'bottom_left':  id_to_center[2],
        'bottom_right': id_to_center[3],
    }, ids


def perspective_correction(image, corners):
    tl = np.float32(corners['top_left'])
    tr = np.float32(corners['top_right'])
    bl = np.float32(corners['bottom_left'])
    br = np.float32(corners['bottom_right'])

    top_width_px    = np.linalg.norm(tr - tl)
    bottom_width_px = np.linalg.norm(br - bl)
    left_height_px  = np.linalg.norm(bl - tl)
    right_height_px = np.linalg.norm(br - tr)

    avg_width_px  = (top_width_px  + bottom_width_px) / 2
    avg_height_px = (left_height_px + right_height_px) / 2

    px_per_cm_x = avg_width_px  / MAT_WIDTH_CM
    px_per_cm_y = avg_height_px / MAT_HEIGHT_CM
    px_per_cm   = float((px_per_cm_x + px_per_cm_y) / 2)

    out_w = int(avg_width_px)
    out_h = int(avg_height_px)

    src = np.array([tl, tr, br, bl], dtype=np.float32)
    dst = np.array([
        [0,     0    ],
        [out_w, 0    ],
        [out_w, out_h],
        [0,     out_h],
    ], dtype=np.float32)

    M      = cv2.getPerspectiveTransform(src, dst)
    warped = cv2.warpPerspective(image, M, (out_w, out_h))

    return warped, px_per_cm


def segment_garment_shirt(warped):
    """
    Use GrabCut for accurate garment segmentation
    regardless of garment color.
    """
    h, w = warped.shape[:2]

    # Define rectangle where garment likely is (inner 20% margin)
    margin_x = int(w * 0.12)
    margin_y = int(h * 0.12)
    rect = (margin_x, margin_y,
            w - 2 * margin_x,
            h - 2 * margin_y)

    # GrabCut
    mask     = np.zeros((h, w), np.uint8)
    bgd      = np.zeros((1, 65), np.float64)
    fgd      = np.zeros((1, 65), np.float64)

    cv2.grabCut(warped, mask, rect, bgd, fgd,
                5, cv2.GC_INIT_WITH_RECT)

    # 0,2 = background | 1,3 = foreground
    garment_mask = np.where(
        (mask == 2) | (mask == 0), 0, 1
    ).astype('uint8') * 255

    # Cleanup
    kernel       = np.ones((11, 11), np.uint8)
    garment_mask = cv2.morphologyEx(garment_mask, cv2.MORPH_CLOSE, kernel)
    garment_mask = cv2.morphologyEx(garment_mask, cv2.MORPH_OPEN,  kernel)

    contours, _ = cv2.findContours(
        garment_mask, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE
    )
    if not contours:
        return None, None, None

    garment_contour = max(contours, key=cv2.contourArea)
    x, y, cw, ch    = cv2.boundingRect(garment_contour)

    bbox = {'x': x, 'y': y, 'width_px': cw, 'height_px': ch}
    return garment_mask, garment_contour, bbox


def segment_garment_pants(warped):
    """
    Fast segmentation using HSV thresholding only.
    No GrabCut — works in < 1 second.
    """
    h, w = warped.shape[:2]

    # Resize to max 800px for faster processing
    scale  = min(1.0, 800 / max(h, w))
    small  = cv2.resize(warped, (int(w * scale), int(h * scale)))

    hsv   = cv2.cvtColor(small, cv2.COLOR_BGR2HSV)

    # White/light background mask
    lower_white = np.array([0,   0,   180])
    upper_white = np.array([180, 50,  255])
    white_mask  = cv2.inRange(hsv, lower_white, upper_white)

    # Also mask very light grey
    lower_grey = np.array([0,   0,   160])
    upper_grey = np.array([180, 25,  255])
    grey_mask  = cv2.inRange(hsv, lower_grey, upper_grey)

    bg_mask      = cv2.bitwise_or(white_mask, grey_mask)
    garment_mask = cv2.bitwise_not(bg_mask)

    # Morphological cleanup
    kernel       = np.ones((7, 7), np.uint8)
    garment_mask = cv2.morphologyEx(garment_mask, cv2.MORPH_CLOSE, kernel)
    garment_mask = cv2.morphologyEx(garment_mask, cv2.MORPH_OPEN,  kernel)

    # Scale mask back to original size
    garment_mask = cv2.resize(
        garment_mask, (w, h), interpolation=cv2.INTER_NEAREST
    )

    contours, _ = cv2.findContours(
        garment_mask, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE
    )
    if not contours:
        return None, None, None

    garment_contour = max(contours, key=cv2.contourArea)
    x, y, cw, ch    = cv2.boundingRect(garment_contour)

    bbox = {'x': x, 'y': y, 'width_px': cw, 'height_px': ch}
    return garment_mask, garment_contour, {
        'x': x, 'y': y, 'width_px': cw, 'height_px': ch
    }





def measure_from_contour_shirt(contour, bbox, px_per_cm):
    x      = bbox['x']
    y      = bbox['y']
    width  = bbox['width_px']
    height = bbox['height_px']

    h_img  = y + height + 10
    w_img  = x + width  + 10
    mask   = np.zeros((h_img, w_img), dtype=np.uint8)
    cv2.drawContours(mask, [contour], -1, 255, thickness=cv2.FILLED)

    row_widths = []
    for row_y in range(y, y + height):
        if row_y >= mask.shape[0]:
            break
        row     = mask[row_y, x: x + width]
        nonzero = np.where(row > 0)[0]
        if len(nonzero) >= 2:
            row_widths.append(int(nonzero[-1] - nonzero[0]))
        else:
            row_widths.append(0)

    if not row_widths:
        return None

    total_rows = len(row_widths)

    # Shoulder: find first row where width exceeds 85% of chest width
    chest_zone = row_widths[int(total_rows*0.20):int(total_rows*0.45)]
    chest_px   = int(max(chest_zone)) if chest_zone else 0
    threshold  = int(chest_px * 0.79)
    shoulder_px = 0
    for i, w in enumerate(row_widths[:int(total_rows*0.35)]):
        if w >= threshold:
            shoulder_px = w
            break
# Fallback
    if shoulder_px == 0:
        shoulder_zone = row_widths[int(total_rows*0.10):int(total_rows*0.20)]
        shoulder_px   = int(np.median(shoulder_zone)) if shoulder_zone else 0

    # Chest: widest in 20-45%
    chest_zone = row_widths[int(total_rows*0.20):int(total_rows*0.45)]
    chest_px   = int(max(chest_zone)) if chest_zone else 0

    # Waist: narrowest in 45-65%
    waist_zone = [w for w in row_widths[int(total_rows*0.45):int(total_rows*0.65)] if w > 0]
    waist_px   = int(min(waist_zone)) if waist_zone else 0

    # Length: topmost to bottommost contour point
    topmost    = tuple(contour[contour[:, :, 1].argmin()][0])
    bottommost = tuple(contour[contour[:, :, 1].argmax()][0])
    length_px  = int(bottommost[1] - topmost[1])

    # Sleeve: diagonal from centre-top to cuff
    arm_zone     = row_widths[int(total_rows*0.15):int(total_rows*0.35)]
    max_width_px = int(max(arm_zone)) if arm_zone else width
    

    # Convert to cm — NO multipliers except length
    shoulder_cm = float(round(shoulder_px / px_per_cm*1.10,        1))
    chest_cm    = float(round(chest_px    / px_per_cm * 1.04, 1))
    waist_cm    = float(round(waist_px    / px_per_cm,        1))
    length_cm   = float(round(length_px   / px_per_cm * 0.99, 1))
    sleeve_cm   = float(round(max_width_px / px_per_cm * 1.045, 1))

    return {
        'chest':     chest_cm,
        'waist':     waist_cm,
        'length':    length_cm,
        'shoulder':  shoulder_cm,
        'sleeve':    sleeve_cm,
        'width_cm':  float(round(width  / px_per_cm, 1)),
        'height_cm': float(round(height / px_per_cm, 1)),
    }

def validate_measurements(m):
    return (
        30  <= m['chest']    <= 90  and
        20  <= m['waist']    <= 80  and
        30  <= m['length']   <= 130 and
        20  <= m['shoulder'] <= 70  and
        10  <= m['sleeve']   <= 120
    )

def measure_pants_from_contour(contour, bbox, px_per_cm):
    print("bbox =", bbox)
    x      = bbox['x']
    y      = bbox['y']
    width  = bbox['width_px']
    height = bbox['height_px']
 
    h_img = y + height + 10
    w_img = x + width  + 10
    mask  = np.zeros((h_img, w_img), dtype=np.uint8)
    cv2.drawContours(mask, [contour], -1, 255, thickness=cv2.FILLED)
 
    row_widths = []
    for row_y in range(y, y + height):
        if row_y >= mask.shape[0]:
            break
        row     = mask[row_y, x: x + width]
        nonzero = np.where(row > 0)[0]
        if len(nonzero) >= 2:
            row_widths.append(int(nonzero[-1] - nonzero[0]))
        else:
            row_widths.append(0)
 
    if not row_widths:
        return None
 
    total_rows = len(row_widths)
 
    # ── Waist: top 5-12% ──────────────────────────
    waist_zone = row_widths[int(total_rows * 0.05):int(total_rows * 0.12)]
    waist_px   = int(np.median(waist_zone)) if waist_zone else 0
 
    # ── Hip: widest point in top 15-35% ───────────
    hip_zone = row_widths[int(total_rows * 0.15):int(total_rows * 0.35)]
    hip_px   = int(max(hip_zone)) if hip_zone else 0
 
    # ── Rise: top to crotch point ─────────────────
    # Crotch = first row after hip where width drops sharply
    # (legs separate / fold narrows the visible width)
    rise_row = 0
    search_start = int(total_rows * 0.20)
    search_end   = int(total_rows * 0.60)
    for i in range(search_start, search_end):
        if i > 0 and row_widths[i] < row_widths[i - 1] * 0.85:
            rise_row = i
            break
    if rise_row == 0:
        rise_row = int(total_rows * 0.40)  # fallback estimate
 
    # ── Thigh: just below crotch ──────────────────
    thigh_start = rise_row + int(total_rows * 0.02)
    thigh_end   = rise_row + int(total_rows * 0.10)
    thigh_zone  = row_widths[thigh_start:thigh_end]
    thigh_px    = int(max(thigh_zone)) if thigh_zone else 0
 
    # ── Knee: middle of leg length ─────────────────
    knee_zone = row_widths[int(total_rows * 0.60):int(total_rows * 0.70)]
    knee_px   = int(np.median(knee_zone)) if knee_zone else 0
 
    # ── Ankle/Hem: bottom 5-10% ────────────────────
    ankle_zone = row_widths[int(total_rows * 0.90):int(total_rows * 0.98)]
    ankle_px   = int(np.median(ankle_zone)) if ankle_zone else 0
 
    # ── Outseam: topmost to bottommost contour point ──
    topmost    = tuple(contour[contour[:, :, 1].argmin()][0])
    bottommost = tuple(contour[contour[:, :, 1].argmax()][0])
    outseam_px = int(bottommost[1] - topmost[1])
 
    # ── Inseam: crotch to hem (approx) ────────────
    inseam_px = int(outseam_px - rise_row)
 
    # ── Convert to cm ──────────────────────────────
    # Folded pants → double the front-layer width for waist/hip/thigh/knee/ankle
    waist_cm   = float(round(waist_px   / px_per_cm * 1.95,    1))
    hip_cm     = float(round(hip_px     / px_per_cm * 2,    1))
    thigh_cm   = float(round(thigh_px   / px_per_cm * 2,    1))
    knee_cm    = float(round(knee_px    / px_per_cm * 2,    1))
    ankle_cm   = float(round(ankle_px   / px_per_cm * 2,    1))
    outseam_cm = float(round(outseam_px / px_per_cm * 1.04, 1))
    rise_cm    = float(round(rise_row   / px_per_cm * 1.04, 1))
    inseam_cm  = float(round(inseam_px  / px_per_cm * 1.04, 1))
 
    return {
    # ── Pants measurements (relevant)
    'chest':     0,
        'waist':     waist_cm,
        'length':    outseam_cm,
        'shoulder':  0,
        'sleeve':    0,
        'width_cm':  float(round(width  / px_per_cm, 1)),
        'height_cm': float(round(height / px_per_cm, 1))
    }

def validate_pants_measurements(m):
    return (
        25 <= m['waist']   <= 120 and
        # 40 <= m['hip']     <= 130 and
        # 10 <= m['thigh']   <= 70 and
        # 10 <= m['knee']    <= 60 and
        # 8  <= m['ankle']   <= 50 and
        40 <= m['length'] <= 140 
        # 40 <= m['inseam']  <= 120 and
        # 8  <= m['rise']    <= 60
    )

def measure_shoe_from_contour(contour, bbox, px_per_cm):
    """
    Measure physical shoe dimensions from top-down photo.
    Shoe laid flat, sole down, toe pointing up.
    """
    x      = bbox['x']
    y      = bbox['y']
    width  = bbox['width_px']
    height = bbox['height_px']

    h_img = y + height + 10
    w_img = x + width  + 10
    mask  = np.zeros((h_img, w_img), dtype=np.uint8)
    cv2.drawContours(mask, [contour], -1, 255, thickness=cv2.FILLED)

    row_widths = []
    for row_y in range(y, y + height):
        if row_y >= mask.shape[0]:
            break
        row     = mask[row_y, x: x + width]
        nonzero = np.where(row > 0)[0]
        if len(nonzero) >= 2:
            row_widths.append(int(nonzero[-1] - nonzero[0]))
        else:
            row_widths.append(0)

    if not row_widths:
        return None

    total_rows = len(row_widths)

    # Shoe length = topmost to bottommost contour point (toe to heel)
    topmost    = tuple(contour[contour[:, :, 1].argmin()][0])
    bottommost = tuple(contour[contour[:, :, 1].argmax()][0])
    length_px  = int(bottommost[1] - topmost[1])

    # Shoe width = widest point in middle 30-70% (ball of shoe)
    ball_zone = row_widths[int(total_rows*0.30):int(total_rows*0.70)]
    width_px  = int(max(ball_zone)) if ball_zone else width

    # Heel width = bottom 15% of shoe
    heel_zone = row_widths[int(total_rows*0.85):int(total_rows*0.98)]
    heel_px   = int(np.median(heel_zone)) if heel_zone else 0

    # Convert to cm
    shoe_length_cm = float(round(length_px / px_per_cm *0.996, 1))
    shoe_width_cm  = float(round(width_px  / px_per_cm *0.727, 1))
    heel_width_cm  = float(round(heel_px   / px_per_cm , 1))

    # Convert shoe length to sizes
    eu_size = shoe_length_to_eu(shoe_length_cm)
    uk_size = shoe_length_to_uk(shoe_length_cm)
    us_size = shoe_length_to_us(shoe_length_cm)

    # Foot length range this shoe fits
    foot_min = float(round(shoe_length_cm - 1.5, 1))
    foot_max = float(round(shoe_length_cm - 0.5, 1))

    return {
        'shoe_length':  shoe_length_cm,
        'shoe_width':   shoe_width_cm,
        'heel_width':   heel_width_cm,
        'shoe_size_eu': eu_size,
        'shoe_size_uk': uk_size,
        'shoe_size_us': us_size,
        'fits_foot_min': foot_min,
        'fits_foot_max': foot_max,
        # Set garment fields to 0 for consistency
        'chest':    0.0,
        'waist':    0.0,
        'length':   shoe_length_cm,
        'shoulder': 0.0,
        'sleeve':   0.0,
    }

def shoe_length_to_eu(length_cm):
    # Calibrated for outer shoe length measurements
    # UK 9 = EU 43, outer shoe ~32cm
    chart = [
        (23.5, 36), (24.0, 37), (24.5, 37),
        (25.0, 38), (25.5, 38), (26.0, 39),
        (26.5, 39), (27.0, 40), (27.5, 40),
        (28.0, 41), (28.5, 41), (29.0, 42),
        (29.5, 42), (30.0, 43), (30.5, 43),
        (31.0, 43), (31.5, 43), (32.0, 43),
        (32.5, 44), (33.0, 44), (33.5, 45),
        (34.0, 45), (34.5, 46), (35.0, 46),
    ]
    for min_len, size in chart:
        if length_cm <= min_len + 0.4:
            return size
    return 47


def shoe_length_to_uk(length_cm):
    eu = shoe_length_to_eu(length_cm)
    return float(round(eu - 34, 1))

def shoe_length_to_us(length_cm):
    uk = shoe_length_to_uk(length_cm)
    return float(round(uk + 1, 1))


def validate_shoe_measurements(m):
    return (
        5.0 <= m['shoe_length'] <= 50.0 and
        2.0  <= m['shoe_width']  <= 25.0
    )

# --EndPoints--
@app.get("/")
def root():
    return {"status": "Garment Measurement Service running"}


@app.post("/measure")
async def measure_garment(
    file: UploadFile = File(...),
    garment_type: str = Form(default="shirt"),
    shoe_background: str = Form(default="white"),
):
    print("ENTERED PYTHON")
    print("GARMENT =", garment_type)

    try:
        file_bytes = await file.read()

        # Step 1 — Load image using garment-specific loader
        if garment_type == "shirt":
            image = load_image_shirt(file_bytes)
        elif garment_type == "pants":
            image = load_image_pant(file_bytes)
        elif garment_type == "shoe":
            image = load_image_pant(file_bytes)
        else:
            image = load_image_shirt(file_bytes)

        # Step 2 — Detect ArUco markers
        corners, ids = detect_aruco_markers(image)
        markers_found = 0 if ids is None else int(len(ids))

        if corners is None:
            return {
                "success":       False,
                "mat_detected":  False,
                "markers_found": markers_found,
                "message":       (
                    "Measurement mat not detected. "
                    "Ensure all 4 corner markers are visible and well lit."
                ),
            }

        # Step 3 — Perspective correction
        warped, px_per_cm = perspective_correction(image, corners)

        # Step 4 — Segment garment using garment-specific segmentation
        if garment_type == "shirt":
            _, contour, bbox = segment_garment_shirt(warped)
        elif garment_type == "pants":
            _, contour, bbox = segment_garment_pants(warped)
        elif garment_type == "shoe":
            _, contour, bbox = segment_garment_pants(warped)
        else:
            _, contour, bbox = segment_garment_shirt(warped)

        if contour is None or bbox is None:
            return {
                "success":      False,
                "mat_detected": True,
                "message":      "No garment detected. Place garment flat inside the markers.",
            }

        # Step 5 — Garment size sanity check
        garment_ratio = float(
            (bbox['width_px'] * bbox['height_px']) /
            (warped.shape[1]  * warped.shape[0])
        )

        if garment_ratio < 0.05:
            return {
                "success":      False,
                "mat_detected": True,
                "message":      "Garment too small in frame. Move camera closer.",
            }
        if garment_ratio > 1.0:
            return {
                "success":      False,
                "mat_detected": True,
                "message":      "Garment too large in frame. Move camera further away.",
            }

        # Step 6 — Measure using garment-specific function
        if garment_type == "pants":
            measurements = measure_pants_from_contour(contour, bbox, px_per_cm)
            is_valid     = measurements is not None and validate_pants_measurements(measurements)
        elif garment_type == "shoe":
            measurements = measure_shoe_from_contour(contour, bbox, px_per_cm)
            is_valid     = measurements is not None and validate_shoe_measurements(measurements)
        else:
            measurements = measure_from_contour_shirt(contour, bbox, px_per_cm)
            is_valid     = measurements is not None and validate_measurements(measurements)

        if measurements is None:
            return {
                "success":      False,
                "mat_detected": True,
                "message":      "Could not extract measurements. Retake photo.",
            }

        measurements = to_python_types(measurements)

        if not is_valid:
            return {
                "success":      False,
                "mat_detected": True,
                "garment_type": garment_type,
                "message":      (
                    f"Measurements seem off for {garment_type}. "
                    "Please retake photo straight down with good lighting."
                ),
            }

        print("bbox =", bbox)
        print("Measurements:", measurements)

        return {
            "success":       True,
            "mat_detected":  True,
            "garment_type":  garment_type,
            "markers_found": markers_found,
            "garment_ratio": float(round(garment_ratio, 3)),
            "measurements":  measurements,
        }

    except Exception as e:
        return {"success": False, "message": f"Processing error: {str(e)}"}
    


@app.post("/extract-dimensions")
async def extract_dimensions(file: UploadFile = File(...)):
    return await measure_garment(file)


@app.post("/measure/manual")
async def manual_measurements(data: dict):
    return {
        "success":      True,
        "message":      "Manual measurements received",
        "measurements": data,
    }

 
@app.post("/predict-size")
async def predict_size(data: dict):
    try:
        shopper     = data.get('shopper', {})
        chest       = float(shopper.get('chest',  0))
        waist       = float(shopper.get('waist',  0))
        length      = float(shopper.get('length', 0))
        size_charts = data.get('size_charts', [])

        if not size_charts:
            return {'success': False, 'message': 'No size chart data provided'}

        best_match = None
        best_score = -999

        for chart in size_charts:
            score   = 0
            reasons = []

            if chest > 0:
                cmin = float(chart.get('chest_min',  0))
                cmax = float(chart.get('chest_max',  999))
                if cmin <= chest <= cmax:
                    score += 3
                    reasons.append(f"Chest {chest}cm fits {cmin}-{cmax}cm")
                else:
                    score -= min(abs(chest - cmin), abs(chest - cmax)) * 0.1

            if waist > 0:
                wmin = float(chart.get('waist_min', 0))
                wmax = float(chart.get('waist_max', 999))
                if wmin <= waist <= wmax:
                    score += 2
                    reasons.append(f"Waist {waist}cm fits {wmin}-{wmax}cm")

            if length > 0:
                lmin = float(chart.get('length_min', 0))
                lmax = float(chart.get('length_max', 999))
                if lmin <= length <= lmax:
                    score += 1
                    reasons.append(f"Length {length}cm fits {lmin}-{lmax}cm")

            if score > best_score:
                best_score = score
                best_match = {'chart': chart, 'score': score, 'reasons': reasons}

        size        = best_match['chart'].get('size_label', 'Unknown')
        confidence  = min(100, int(best_match['score'] * 20))
        explanation = (
            f"We recommend size {size} because: " + ", ".join(best_match['reasons'])
            if best_match['reasons']
            else f"Based on your measurements, size {size} is recommended"
        )

        return {
            'success':     True,
            'size':        size,
            'confidence':  confidence,
            'explanation': explanation,
            'shopper':     shopper,
        }

    except Exception as e:
        return {'success': False, 'message': f'Prediction error: {str(e)}'}


@app.post("/retrain")
async def retrain_model(data: dict):
    try:
        feedback       = data.get('feedback', [])
        feedback_count = len(feedback)
        print(f"Received {feedback_count} feedback records for retraining")
        return {
            'success':        True,
            'message':        f'Retraining triggered with {feedback_count} records',
            'feedback_count': feedback_count,
            'status':         'queued',
        }
    except Exception as e:
        return {'success': False, 'message': f'Retrain error: {str(e)}'} 