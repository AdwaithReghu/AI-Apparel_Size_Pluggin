<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GarmentController;
use App\Http\Controllers\Api\ScanController;
use App\Http\Controllers\Api\DashboardController;

// ─── PUBLIC ROUTES (no auth needed) ───────────────────
Route::post('/login', [AuthController::class, 'login']);

// Widget route — public, uses API key instead of token
Route::post('/widget/predict-size', function(\Illuminate\Http\Request $request) {
    $apiKey = $request->header('X-Widget-Key');
    $merchant = \App\Models\User::where('api_key', $apiKey)->first();

    if (!$merchant) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid API key'
        ], 401);
    }

    $sizeCharts = \App\Models\SizeChart::where('user_id', $merchant->id)
        ->where('is_active', true)
        ->when($request->category, function($q) use ($request) {
            $q->where('category', $request->category);
        })
        ->get()
        ->map(function($chart) {
            return [
                'size_label'   => $chart->size_label,
                'chest_min'    => $chart->chest_min,
                'chest_max'    => $chart->chest_max,
                'waist_min'    => $chart->waist_min,
                'waist_max'    => $chart->waist_max,
                'length_min'   => $chart->length_min,
                'length_max'   => $chart->length_max,
                'shoulder_min' => $chart->shoulder_min,
                'shoulder_max' => $chart->shoulder_max,
            ];
        })
        ->toArray();

    if (empty($sizeCharts)) {
        return response()->json([
            'success' => false,
            'message' => 'No size charts found'
        ], 404);
    }

    $response = \Illuminate\Support\Facades\Http::post(
        'http://127.0.0.1:8001/predict-size',
        [
            'shopper'     => $request->shopper,
            'size_charts' => $sizeCharts,
        ]
    );

    $merchant->increment('api_calls_today');
    $merchant->increment('api_calls_month');

    return response()->json($response->json());
});

// Handle preflight
Route::options('/widget/predict-size', function() {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'POST, OPTIONS')
        ->header('Access-Control-Allow-Headers', '*');
});

// ─── PROTECTED ROUTES (need token) ────────────────────
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/garments', [GarmentController::class, 'index']);
    Route::get('/garments/{id}', [GarmentController::class, 'show']);
    Route::post('/garments', [GarmentController::class, 'store']);
    Route::put('/garments/{id}', [GarmentController::class, 'update']);
    Route::delete('/garments/{id}', [GarmentController::class, 'destroy']);

    Route::post('/scans/process', [ScanController::class, 'process']);
    Route::post('/scan/extract', [ScanController::class, 'process']);
    Route::get('/scans', [ScanController::class, 'index']);
    Route::get('/scans/{id}', [ScanController::class, 'show']);

    Route::post('/size/predict', [ScanController::class, 'predictSize']);
    Route::post('/model/retrain', [ScanController::class, 'retrain']);

    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
});