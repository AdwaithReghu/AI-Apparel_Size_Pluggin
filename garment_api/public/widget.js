(function () {
  // ─── Configuration ───────────────────────────────────
  const script = document.currentScript;
  const API_KEY = script.getAttribute('data-key') || '';
  console.log('API_KEY:', API_KEY);
  const BRAND = script.getAttribute('data-brand') || '';
  const CATEGORY = script.getAttribute('data-category') || '';
  const API_URL = script.getAttribute('data-api-url') ||
    'http://127.0.0.1:8000/api';

  // ─── Styles ──────────────────────────────────────────
  const styles = `
    #nytt-widget-btn {
      background: #6C63FF;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 14px;
      font-weight: 600;
      margin: 8px 0;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }
    #nytt-widget-btn:hover {
      background: #5a52d5;
    }
    #nytt-overlay {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 99999;
      justify-content: center;
      align-items: center;
    }
    #nytt-overlay.active {
      display: flex;
    }
    #nytt-panel {
      background: white;
      border-radius: 16px;
      width: 90%;
      max-width: 420px;
      padding: 24px;
      position: relative;
      max-height: 90vh;
      overflow-y: auto;
      font-family: -apple-system, sans-serif;
    }
    #nytt-close {
      position: absolute;
      top: 16px; right: 16px;
      background: none;
      border: none;
      font-size: 20px;
      cursor: pointer;
      color: #666;
    }
    #nytt-title {
      font-size: 20px;
      font-weight: 700;
      color: #333;
      margin: 0 0 4px 0;
    }
    #nytt-subtitle {
      font-size: 13px;
      color: #666;
      margin: 0 0 20px 0;
    }
    .nytt-input-group {
      margin-bottom: 16px;
    }
    .nytt-label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      color: #444;
      margin-bottom: 6px;
    }
    .nytt-input {
      width: 100%;
      border: 1.5px solid #ddd;
      border-radius: 8px;
      padding: 10px 14px;
      font-size: 15px;
      box-sizing: border-box;
      outline: none;
      transition: border-color 0.2s;
    }
    .nytt-input:focus {
      border-color: #6C63FF;
    }
    .nytt-input-row {
      display: flex;
      gap: 8px;
    }
    .nytt-input-row .nytt-input-group {
      flex: 1;
    }
    #nytt-submit {
      width: 100%;
      background: #6C63FF;
      color: white;
      border: none;
      padding: 14px;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      margin-top: 8px;
    }
    #nytt-submit:hover {
      background: #5a52d5;
    }
    #nytt-submit:disabled {
      background: #aaa;
      cursor: not-allowed;
    }
    #nytt-result {
      display: none;
      margin-top: 16px;
      padding: 16px;
      border-radius: 12px;
      background: #f0fdf4;
      border: 1.5px solid #86efac;
    }
    #nytt-result.error {
      background: #fef2f2;
      border-color: #fca5a5;
    }
    #nytt-result-size {
      font-size: 28px;
      font-weight: 800;
      color: #16a34a;
    }
    #nytt-result-title {
      font-size: 14px;
      font-weight: 600;
      color: #15803d;
      margin-bottom: 4px;
    }
    #nytt-result-explanation {
      font-size: 12px;
      color: #166534;
      margin-top: 4px;
    }
    #nytt-select-btn {
      width: 100%;
      background: #16a34a;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      margin-top: 12px;
    }
    #nytt-select-btn:hover {
      background: #15803d;
    }
    .nytt-loading {
      text-align: center;
      padding: 8px;
      color: #6C63FF;
      font-size: 14px;
    }
    .nytt-brand-info {
      background: #f5f3ff;
      border-radius: 8px;
      padding: 10px 14px;
      margin-bottom: 16px;
      font-size: 13px;
      color: #5b21b6;
    }
  `;

  // ─── Inject Styles ───────────────────────────────────
  const styleEl = document.createElement('style');
  styleEl.textContent = styles;
  document.head.appendChild(styleEl);

  // ─── Create Button ───────────────────────────────────
  const btn = document.createElement('button');
  btn.id = 'nytt-widget-btn';
  btn.innerHTML = '📏 Find My Size';
  script.parentNode.insertBefore(btn, script.nextSibling);

  // ─── Create Overlay ──────────────────────────────────
  const overlay = document.createElement('div');
  overlay.id = 'nytt-overlay';
  overlay.innerHTML = `
    <div id="nytt-panel">
      <button id="nytt-close">✕</button>

      <h2 id="nytt-title">Find Your Perfect Size</h2>
      <p id="nytt-subtitle">
        Enter your measurements to get a size recommendation
      </p>

      ${BRAND ? `
        <div class="nytt-brand-info">
          🏷️ Brand: <strong>${BRAND}</strong>
          ${CATEGORY ? ` &nbsp;|&nbsp; 👕 ${CATEGORY}` : ''}
        </div>
      ` : ''}

      <div class="nytt-input-row">
        <div class="nytt-input-group">
          <label class="nytt-label">Chest (cm)</label>
          <input class="nytt-input" id="nytt-chest"
            type="number" placeholder="e.g. 94" min="40" max="200" />
        </div>
        <div class="nytt-input-group">
          <label class="nytt-label">Waist (cm)</label>
          <input class="nytt-input" id="nytt-waist"
            type="number" placeholder="e.g. 82" min="40" max="200" />
        </div>
      </div>

      <div class="nytt-input-row">
        <div class="nytt-input-group">
          <label class="nytt-label">Length (cm)</label>
          <input class="nytt-input" id="nytt-length"
            type="number" placeholder="e.g. 70" min="40" max="200" />
        </div>
        <div class="nytt-input-group">
          <label class="nytt-label">Shoulder (cm)</label>
          <input class="nytt-input" id="nytt-shoulder"
            type="number" placeholder="e.g. 45" min="20" max="100" />
        </div>
      </div>

      <button id="nytt-submit">Get My Size →</button>

      <div id="nytt-result">
        <div id="nytt-result-title">Recommended Size</div>
        <div style="display:flex; align-items:center; gap:12px;">
          <div id="nytt-result-size">M</div>
          <div>
            <div id="nytt-confidence"
              style="font-size:13px; color:#15803d;">
            </div>
            <div id="nytt-result-explanation"></div>
          </div>
        </div>
        <button id="nytt-select-btn">✓ Select This Size</button>
      </div>

    </div>
  `;
  document.body.appendChild(overlay);

  // ─── Event Listeners ─────────────────────────────────

  // Open widget
  btn.addEventListener('click', function () {
    overlay.classList.add('active');
  });

  // Close widget
  document.getElementById('nytt-close')
    .addEventListener('click', function () {
      overlay.classList.remove('active');
    });

  // Close on outside click
  overlay.addEventListener('click', function (e) {
    if (e.target === overlay) {
      overlay.classList.remove('active');
    }
  });

  // Submit measurements
  document.getElementById('nytt-submit')
    .addEventListener('click', async function () {
      const chest    = parseFloat(document.getElementById('nytt-chest').value);
      const waist    = parseFloat(document.getElementById('nytt-waist').value);
      const length   = parseFloat(document.getElementById('nytt-length').value);
      const shoulder = parseFloat(document.getElementById('nytt-shoulder').value);

      // Validate
      if (!chest && !waist) {
        alert('Please enter at least chest or waist measurement');
        return;
      }

      const submitBtn = document.getElementById('nytt-submit');
      const resultDiv = document.getElementById('nytt-result');

      // Show loading
      submitBtn.disabled = true;
      submitBtn.textContent = 'Calculating...';
      resultDiv.style.display = 'none';

      try {
        console.log('Calling URL:', `${API_URL}/widget/predict-size`);
        console.log('API Key:', API_KEY);
        // Call Laravel API
        const response = await fetch(`${API_URL}/widget/predict-size`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Widget-Key': API_KEY,
          },
          body: JSON.stringify({
            shopper: {
              chest:    chest    || 0,
              waist:    waist    || 0,
              length:   length   || 0,
              shoulder: shoulder || 0,
            },
            brand:    BRAND,
            category: CATEGORY,
          }),
        });

        const data = await response.json();

        if (data.success) {
          // Show result
          document.getElementById('nytt-result-size').textContent =
            data.size;
          document.getElementById('nytt-confidence').textContent =
            `Confidence: ${data.confidence}%`;
          document.getElementById('nytt-result-explanation').textContent =
            data.explanation;

          resultDiv.classList.remove('error');
          resultDiv.style.display = 'block';
        } else {
          // Show error
          document.getElementById('nytt-result-size').textContent = '?';
          document.getElementById('nytt-confidence').textContent = '';
          document.getElementById('nytt-result-explanation').textContent =
            data.message || 'Could not determine size';
          resultDiv.classList.add('error');
          resultDiv.style.display = 'block';
        }

      } catch (error) {
        document.getElementById('nytt-result-size').textContent = '!';
        document.getElementById('nytt-result-explanation').textContent =
          'Connection error. Please try again.';
        resultDiv.classList.add('error');
        resultDiv.style.display = 'block';
      }

      // Reset button
      submitBtn.disabled = false;
      submitBtn.textContent = 'Get My Size →';
    });

  // Select size button
  document.getElementById('nytt-select-btn')
    .addEventListener('click', function () {
      const size = document.getElementById('nytt-result-size').textContent;

      // Try to select size on the page
      // Works with most e-commerce platforms
      const sizeButtons = document.querySelectorAll(
        '[data-size], .size-option, .size-btn, ' +
        'input[name="size"], select[name="size"]'
      );

      let selected = false;
      sizeButtons.forEach(function (el) {
        const elSize = (
          el.getAttribute('data-size') ||
          el.value ||
          el.textContent
        ).trim().toUpperCase();

        if (elSize === size.toUpperCase()) {
          el.click();
          selected = true;
        }
      });

      if (selected) {
        alert(`Size ${size} selected!`);
      } else {
        alert(
          `Your recommended size is ${size}. ` +
          `Please select it manually from the size options.`
        );
      }

      overlay.classList.remove('active');
    });

})();