<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Garment;
use Illuminate\Http\Request;

class GarmentController extends Controller
{
    // Get all garments
    public function index(Request $request)
    {
        $garments = Garment::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'success'  => true,
            'garments' => $garments,
        ]);
    }

    // Get single garment
    public function show(Request $request, $id)
    {
        $garment = Garment::where('user_id', $request->user()->id)
            ->find($id);

        if (!$garment) {
            return response()->json([
                'success' => false,
                'message' => 'Garment not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'garment' => $garment,
        ]);
    }

    // Create garment
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string',
            'brand'    => 'nullable|string',
            'category' => 'nullable|string',
        ]);

        $garment = Garment::create([
        'user_id'    => $request->user()->id,
        'name'       => $request->name,
        'brand'      => $request->brand,
        'category'   => $request->category,
        'size_label' => $request->size_label,
        'chest'      => $request->chest,      // ← check this exists
        'waist'      => $request->waist,      // ← check this exists
        'length'     => $request->length,     // ← check this exists
        'shoulder'   => $request->shoulder,   // ← check this exists
        'sleeve'     => $request->sleeve,     // ← check this exists
        'status'     => $request->status ?? 'pending',
    ]);

        return response()->json([
            'success' => true,
            'garment' => $garment,
        ], 201);
    }

    // Update garment
    public function update(Request $request, $id)
    {
        $garment = Garment::where('user_id', $request->user()->id)
            ->find($id);

        if (!$garment) {
            return response()->json([
                'success' => false,
                'message' => 'Garment not found',
            ], 404);
        }

        $garment->update($request->only([
            'name',
            'brand',
            'category',
            'size_label',
            'chest',
            'waist',
            'length',
            'shoulder',
            'sleeve',
            'status',
        ]));

        return response()->json([
            'success' => true,
            'garment' => $garment,
        ]);
    }

    // Delete garment
    public function destroy(Request $request, $id)
    {
        $garment = Garment::where('user_id', $request->user()->id)
            ->find($id);

        if (!$garment) {
            return response()->json([
                'success' => false,
                'message' => 'Garment not found',
            ], 404);
        }

        $garment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Garment deleted successfully',
        ]);
    }
}