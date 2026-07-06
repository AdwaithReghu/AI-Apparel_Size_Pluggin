<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GarmentController;
use App\Http\Controllers\Api\ScanController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\WidgetController;

// ─── PUBLIC ROUTES (no auth needed) ───────────────────
Route::post('/login', [AuthController::class, 'login']);

// Widget route — public, uses API key instead of token
Route::post('/widget/predict-size', [WidgetController::class, 'predictSize']);

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

    // Get merchant categories
Route::get('/categories', function(\Illuminate\Http\Request $request) {
    $defaultCategories = [
        'Shirt', 'T-Shirt', 'Jacket', 'Trousers',
        'Dress', 'Skirt', 'Shorts', 'Sweater', 'Coat', 'Other'
    ];

    $dbCategories = \App\Models\Category::where('user_id', $request->user()->id)
        ->where('is_active', true)
        ->pluck('name')
        ->toArray();

    $allCategories = array_unique(
        array_merge($defaultCategories, $dbCategories)
    );

    return response()->json([
        'success'    => true,
        'categories' => array_values($allCategories),
    ]);
});

    Route::post('/scans/process', [ScanController::class, 'process']);
    Route::post('/scan/extract', [ScanController::class, 'process']);
    Route::get('/scans', [ScanController::class, 'index']);
    Route::get('/scans/{id}', [ScanController::class, 'show']);

    Route::post('/size/predict', [ScanController::class, 'predictSize']);
    Route::post('/model/retrain', [ScanController::class, 'retrain']);

    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
});