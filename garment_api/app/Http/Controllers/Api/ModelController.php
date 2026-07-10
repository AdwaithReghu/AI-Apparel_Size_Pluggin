<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SizingModelRebuilder;
use Illuminate\Http\Request;

class ModelController extends Controller
{
    /**
     * Rebuild the sizing model from this merchant's size charts.
     * Called by the Flutter app / dashboard after scanning or editing charts.
     */
    public function rebuild(Request $request, SizingModelRebuilder $rebuilder)
    {
        $result = $rebuilder->rebuildForUser($request->user());

        return response()->json($result, $result['success'] ? 200 : 422);
    }
}
