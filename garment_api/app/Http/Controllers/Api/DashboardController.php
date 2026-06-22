<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Garment;
use App\Models\Scan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $userId = $request->user()->id;

        $totalGarments = Garment::where('user_id', $userId)->count();
        $pendingGarments = Garment::where('user_id', $userId)
            ->where('status', 'pending')->count();
        $completedGarments = Garment::where('user_id', $userId)
            ->where('status', 'completed')->count();
        $recentGarments = Garment::where('user_id', $userId)
            ->latest()->take(5)->get();

        return response()->json([
            'success' => true,
            'stats'   => [
                'total_garments'     => $totalGarments,
                'pending_garments'   => $pendingGarments,
                'completed_garments' => $completedGarments,
                'recent_garments'    => $recentGarments,
            ],
        ]);
    }
}