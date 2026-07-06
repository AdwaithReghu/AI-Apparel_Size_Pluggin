<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Brand;
use App\Models\SizeChart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WidgetController extends Controller
{
    // ── Category name normalizer ───────────────────────
    private function normalizeCategory(string $category): string
    {
        $cat = strtolower(trim($category));

        $map = [
            't-shirt'       => ['t-shirt', 'tshirt', 'tshirts', 'tees', 'tee'],
            'formal-shirt'  => ['shirt', 'shirts', 'formal shirt', 'formal shirts'],
            'jeans'         => ['jeans', 'denim'],
            'hoodie'        => ['hoodie', 'hoodies', 'sweatshirt', 'sweatshirts'],
            'jacket'        => ['jacket', 'jackets'],
            'dress'         => ['dress', 'dresses'],
            'shorts'        => ['shorts'],
        ];

        foreach ($map as $mlCategory => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($cat, $keyword)) {
                    return $mlCategory;
                }
            }
        }

        return 'other';
    }

    // ── Transform size chart row to ML format ──────────
    private function transformSizeChart(SizeChart $chart): array
    {
        $measurements = [];

        $fields = [
            'chest'   => ['chest_min',   'chest_max'],
            'waist'   => ['waist_min',   'waist_max'],
            'length'  => ['length_min',  'length_max'],
            'hip'     => ['hip_min',     'hip_max'],
            'thigh'   => ['thigh_min',   'thigh_max'],
            'inseam'  => ['inseam_min',  'inseam_max'],
        ];

        foreach ($fields as $key => [$minCol, $maxCol]) {
            if (!is_null($chart->$minCol) || !is_null($chart->$maxCol)) {
                $measurements[$key] = [
                    'min' => $chart->$minCol,
                    'max' => $chart->$maxCol,
                ];
            }
        }

        return [
            'size_label'   => $chart->size_label,
            'measurements' => $measurements,
        ];
    }

    // ── Main endpoint ──────────────────────────────────
    public function predictSize(Request $request)
    {
        // Step 1 — Authenticate via X-Widget-Key
        $apiKey  = $request->header('X-Widget-Key');
        $merchant = User::where('api_key', $apiKey)->first();

        if (!$merchant) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key',
            ], 401);
        }

        // Step 2 — Resolve brand
        $brandName = $request->input('brand', '');
        $brand     = Brand::where('user_id', $merchant->id)
            ->whereRaw('LOWER(name) = ?', [strtolower($brandName)])
            ->first();

        $brandId          = $brand?->id ?? 0;
        $sizingPhilosophy = $brand?->sizing_philosophy ?? null;

        // Step 3 — Normalize category
        $rawCategory    = $request->input('category', '');
        $mlCategory     = $this->normalizeCategory($rawCategory);

        // Step 4 — Load size charts
        $query = SizeChart::where('user_id', $merchant->id)
            ->where('is_active', true)
            ->whereRaw('LOWER(category) LIKE ?', ['%' . strtolower($mlCategory) . '%']);

        if ($brandId > 0) {
            $query->where('brand_id', $brandId);
        }

        $sizeCharts = $query->get();

        if ($sizeCharts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No size charts found for this brand and category',
            ], 404);
        }

        $sizeChartRows = $sizeCharts->map(
            fn($chart) => $this->transformSizeChart($chart)
        )->values()->toArray();

        // Step 5 — Call ML service
        $shopper = $request->input('shopper', []);
        $fitType = $request->input('fit_type', 'regular');

        $payload = [
            'brand_id'          => $brandId,
            'brand_name'        => $brandName,
            'apparel_category'  => $mlCategory,
            'gender'            => $shopper['gender'] ?? 'men',
            'fit_type'          => $fitType,
            'fabric_notes'      => null,
            'sizing_philosophy' => $sizingPhilosophy,
            'size_chart_rows'   => $sizeChartRows,
            'shopper'           => $shopper,
        ];

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'X-Service-Secret' => env('PYTHON_API_SECRET'),
                    'Content-Type'     => 'application/json',
                ])
                ->post(env('ML_API_URL') . '/predict-size', $payload);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'ML service error: ' . $response->status(),
                ], 502);
            }

            // Step 6 — Return ML response as-is
            return response()->json($response->json());

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ML service unavailable: ' . $e->getMessage(),
            ], 503);
        }
    }
}