<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Scan;
use App\Models\SizeChart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ScanController extends Controller
{
    private string $pythonServiceUrl = 'http://127.0.0.1:8001';

    // ── Process scan ───────────────────────────────────
    public function process(Request $request)
    {
        $request->validate([
            'image'    => 'required|image|max:10240',
            'category' => 'nullable|string|max:100',
            'garment_type' => 'required|string|in:shirt,pants',
        ]);

        $imagePath = null;

        try {
            // Step 1 — Save image
            $imagePath = $request->file('image')->store('scans', 'public');

            // Step 2 — Send to Python service
            $response = Http::timeout(30)->attach(
                'file',
                file_get_contents(storage_path('app/public/' . $imagePath)),
                'garment.jpg'
            )->post($this->pythonServiceUrl . '/measure',[
                'garment_type' => $request->input('garment_type'),
            ]);

            if (!$response->successful()) {
                $this->cleanupImage($imagePath);
                return response()->json([
                    'success' => false,
                    'message' => 'Measurement service unavailable. Please try again.',
                ], 503);
            }

            $pythonResult = $response->json();

            // Step 3 — Check Python result
            if (!($pythonResult['success'] ?? false)) {
                $this->cleanupImage($imagePath);
                return response()->json([
                    'success'        => false,
                    'mat_detected'   => $pythonResult['mat_detected']  ?? false,
                    'markers_found'  => $pythonResult['markers_found'] ?? 0,
                    'message'        => $pythonResult['message'] ?? 'Measurement failed',
                ], 422);
            }

            $measurements = $pythonResult['measurements'];

            // Step 4 — Save scan
            $scan = Scan::create([
                'user_id'      => $request->user()->id,
                'image_path'   => $imagePath,
                'measurements' => $measurements,
                'status'       => 'completed',
            ]);

            // Step 5 — Suggest size
            $suggestion = $this->suggestSize(
                $measurements,
                $request->input('category', '')
            );

            return response()->json([
                'success'         => true,
                'scan_id'         => $scan->id,
                'mat_detected'    => $pythonResult['mat_detected']   ?? true,
                'markers_found'   => $pythonResult['markers_found']  ?? 4,
                'garment_ratio'   => $pythonResult['garment_ratio']  ?? null,
                'measurements'    => $measurements,
                'size_suggestion' => $suggestion,
                'image_url'       => Storage::url($imagePath),
            ]);

        } catch (\Exception $e) {
            $this->cleanupImage($imagePath);
            return response()->json([
                'success' => false,
                'message' => 'Processing error: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ── Get all scans ──────────────────────────────────
    public function index(Request $request)
    {
        $scans = Scan::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'scans'   => $scans,
        ]);
    }

    // ── Get single scan ────────────────────────────────
    public function show(Request $request, $id)
    {
        $scan = Scan::where('user_id', $request->user()->id)->find($id);

        if (!$scan) {
            return response()->json([
                'success' => false,
                'message' => 'Scan not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'scan'    => $scan,
        ]);
    }

    // ── Predict size ───────────────────────────────────
    public function predictSize(Request $request)
    {
        $request->validate([
            'shopper'     => 'required|array',
            'size_charts' => 'required|array',
        ]);

        try {
            $response = Http::timeout(15)->post(
                $this->pythonServiceUrl . '/predict-size',
                [
                    'shopper'     => $request->shopper,
                    'size_charts' => $request->size_charts,
                ]
            );

            $result = $response->json();

            if ($result['success'] ?? false) {
                return response()->json([
                    'success'     => true,
                    'size'        => $result['size'],
                    'confidence'  => $result['confidence'],
                    'explanation' => $result['explanation'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Prediction failed',
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Service error: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ── Retrain model ──────────────────────────────────
    public function retrain(Request $request)
    {
        try {
            $response = Http::timeout(30)->post(
                $this->pythonServiceUrl . '/retrain',
                ['feedback' => $request->feedback ?? []]
            );

            return response()->json($response->json());

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Retrain error: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ── Private helpers ────────────────────────────────

    private function suggestSize(array $measurements, string $category = ''): ?array
    {
        $chest  = floatval($measurements['chest']  ?? 0);
        $waist  = floatval($measurements['waist']  ?? 0);
        $length = floatval($measurements['length'] ?? 0);

        $query = SizeChart::where('is_active', true);

        if ($category) {
            $query->whereRaw('LOWER(category) = ?', [strtolower($category)]);
        }

        $sizeCharts = $query->get();

        $bestMatch = null;
        $bestScore = -1;

        foreach ($sizeCharts as $chart) {
            $score = 0;

            if ($chest > 0 && $chart->chest_min && $chart->chest_max) {
                if ($chest >= $chart->chest_min && $chest <= $chart->chest_max) {
                    $score += 3;
                } else {
                    continue; // chest is mandatory — skip if doesn't fit
                }
            }

            if ($waist > 0 && $chart->waist_min && $chart->waist_max) {
                if ($waist >= $chart->waist_min && $waist <= $chart->waist_max) {
                    $score += 2;
                }
            }

            if ($length > 0 && $chart->length_min && $chart->length_max) {
                if ($length >= $chart->length_min && $length <= $chart->length_max) {
                    $score += 1;
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $chart;
            }
        }

        if (!$bestMatch) {
            return null;
        }

        return [
            'size'     => $bestMatch->size_label,
            'brand'    => $bestMatch->brand?->name ?? 'Unknown',
            'category' => $bestMatch->category,
            'chart_id' => $bestMatch->id,
        ];
    }

    private function cleanupImage(?string $imagePath): void
    {
        if ($imagePath && Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
    }
}