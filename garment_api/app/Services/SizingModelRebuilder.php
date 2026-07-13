<?php

namespace App\Services;

use App\Models\SizeChart;
use App\Models\User;
use App\Support\SizingNormalizer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Workstream 2 — gather a merchant's size charts into per-brand bundles and ask
 * the sizing service to rebuild the sizing model so every scanned brand becomes a known brand.
 *
 * Uses the SAME SizingNormalizer as WidgetController, so the brand_key/category
 * used to TRAIN match the ones used at INFERENCE (Workstream 0 — Join B).
 */
class SizingModelRebuilder
{
    /** Build bundles for this merchant and trigger the ML rebuild. */
    public function rebuildForUser(User $user): array
    {
        $charts = SizeChart::where('user_id', $user->id)
            ->where('is_active', true)
            ->with('brand')
            ->get();

        $bundles = $this->buildBundles($charts);

        if (empty($bundles)) {
            return [
                'success' => false,
                'message' => 'No brand-linked, categorized size charts to train on. '
                    . 'Add charts with a brand + category first.',
            ];
        }

        try {
            // Retraining can run well past PHP's default 30s web request limit
            // (unlike CLI/tinker, which has no limit). Raise it just for this
            // slow, explicit, user-triggered action.
            set_time_limit(300);

            $response = Http::timeout(300) // rebuild retrains the whole model
                ->withHeaders(['X-Service-Secret' => env('PYTHON_API_SECRET')])
                ->post(env('ML_API_URL') . '/rebuild-model', ['bundles' => $bundles]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'Sizing update failed: HTTP ' . $response->status(),
                    'detail'  => $response->json(),
                ];
            }

            $result = $response->json();
            Log::info('Sizing model rebuilt', ['user_id' => $user->id, 'result' => $result]);

            return [
                'success'        => true,
                'message'        => 'Sizing model rebuilt.',
                'brands_trained' => $result['brands_trained'] ?? [],
                'rows_generated' => $result['rows_generated'] ?? 0,
                'accuracy'       => $result['accuracy'] ?? null,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'sizing service unavailable: ' . $e->getMessage(),
            ];
        }
    }

    /** Group active charts into (brand_key, category_slug) bundles. */
    private function buildBundles($charts): array
    {
        $groups = [];

        foreach ($charts as $chart) {
            $brand = $chart->brand;
            if (!$brand) {
                continue; // need a brand to train a brand feature
            }

            $bkey = SizingNormalizer::brandKey($brand->name);
            $slug = SizingNormalizer::categorySlug($chart->category);
            if ($bkey === '' || $slug === 'other') {
                continue;
            }

            $key = $bkey . '|' . $slug;
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'brand_key'         => $bkey,
                    'brand_name'        => $brand->name,
                    'category_slug'     => $slug,
                    'gender'            => $chart->target_gender ?: 'men',
                    'sizing_philosophy' => $brand->sizing_philosophy,
                    'size_rows'         => [],
                ];
            }

            $groups[$key]['size_rows'][] = [
                'size_label'    => $chart->size_label,
                'target_gender' => $chart->target_gender,
                'weight_min'    => $chart->weight_min,
                'weight_max'    => $chart->weight_max,
                'age_min'       => $chart->age_min,
                'age_max'       => $chart->age_max,
                'body_types'    => $chart->body_types,
                'chest_min'     => $chart->chest_min,
                'chest_max'     => $chart->chest_max,
                'waist_min'     => $chart->waist_min,
                'waist_max'     => $chart->waist_max,
                'shoulder_min'  => $chart->shoulder_min,
                'shoulder_max'  => $chart->shoulder_max,
                'sleeve_min'    => $chart->sleeve_min,
                'sleeve_max'    => $chart->sleeve_max,
                'length_min'    => $chart->length_min,
                'length_max'    => $chart->length_max,
                'hip_min'       => $chart->hip_min,
                'hip_max'       => $chart->hip_max,
                'thigh_min'     => $chart->thigh_min,
                'thigh_max'     => $chart->thigh_max,
                'inseam_min'    => $chart->inseam_min,
                'inseam_max'    => $chart->inseam_max,
            ];
        }

        return array_values($groups);
    }
}
