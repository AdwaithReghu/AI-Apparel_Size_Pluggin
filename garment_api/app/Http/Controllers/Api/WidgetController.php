<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Brand;
use App\Models\SizeChart;
use App\Support\SizingNormalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WidgetController extends Controller
{
    // ── Transform size chart row to ML format ──────────
    private function transformSizeChart(SizeChart $chart): array
    {
        $measurements = [];

        // Include every measurement the matcher can score on — tops need
        // shoulder & sleeve, bottoms need hip/thigh/inseam. (shoulder & sleeve
        // used to be silently dropped here.)
        $fields = [
            'chest'    => ['chest_min',    'chest_max'],
            'waist'    => ['waist_min',    'waist_max'],
            'length'   => ['length_min',   'length_max'],
            'shoulder' => ['shoulder_min', 'shoulder_max'],
            'sleeve'   => ['sleeve_min',   'sleeve_max'],
            'hip'      => ['hip_min',      'hip_max'],
            'thigh'    => ['thigh_min',    'thigh_max'],
            'inseam'   => ['inseam_min',   'inseam_max'],
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
        $apiKey   = $request->header('X-Widget-Key');
        $merchant = User::where('api_key', $apiKey)->first();

        if (!$merchant) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key',
            ], 401);
        }

        // Step 2 — Resolve brand by CANONICAL KEY (Workstream 0/1).
        // PrestaShop may send "Levi's" while the merchant stored "Levis" — both
        // normalize to "levis". Never 404 on brand alone.
        $rawBrand = (string) $request->input('brand', '');
        $brandKey = SizingNormalizer::brandKey($rawBrand);

        $brand = Brand::where('user_id', $merchant->id)->get()
            ->first(fn($b) => SizingNormalizer::brandKey($b->name) === $brandKey);

        $brandId          = $brand?->id ?? 0;
        $brandName        = $brand?->name ?? $rawBrand;          // resolved display name
        $sizingPhilosophy = $brand?->sizing_philosophy ?? null;

        // Step 3 — Normalize category to a canonical slug.
        $mlCategory = SizingNormalizer::categorySlug((string) $request->input('category', ''));

        // Step 4 — Load charts, then match category by NORMALIZED EQUALITY (not
        // LIKE) so "Hoodies"/"hoodie"/"Sweatshirts" all resolve identically.
        $query = SizeChart::where('user_id', $merchant->id)->where('is_active', true);
        if ($brandId > 0) {
            $query->where('brand_id', $brandId);
        }

        $sizeCharts = $query->get()->filter(
            fn($chart) => SizingNormalizer::categorySlug($chart->category) === $mlCategory
        )->values();

        if ($sizeCharts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No size charts found for this brand and category',
            ], 404);
        }

        $sizeChartRows = $sizeCharts->map(
            fn($chart) => $this->transformSizeChart($chart)
        )->values()->toArray();

        // Step 5 — Call sizing service. Send BOTH the canonical keys (for the sizing model's
        // one-hot) and the display name (for explanations). brand_key/category
        // are produced by the same SizingNormalizer used by the training export,
        // so training and inference keys are byte-identical (Workstream 0).
        $shopper = $request->input('shopper', []);
        $fitType = $request->input('fit_type', 'regular');

        $payload = [
            'brand_id'          => $brandId,
            'brand_name'        => $brandName,
            'brand_key'         => $brandKey,
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
                    'message' => 'sizing service error: ' . $response->status(),
                ], 502);
            }

            // Step 6 — Return ML response as-is
            return response()->json($response->json());

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'sizing service unavailable: ' . $e->getMessage(),
            ], 503);
        }
    }
}
