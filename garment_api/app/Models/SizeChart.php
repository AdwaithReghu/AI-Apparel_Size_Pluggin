<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SizeChart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'brand_id',
        'category',
        'size_label',
        // merchant-declared target-shopper attributes (Workstream 2)
        'target_gender',
        'weight_min',
        'weight_max',
        'age_min',
        'age_max',
        'body_types',
        // ArUco / merchant garment measurements
        'chest_min',
        'chest_max',
        'waist_min',
        'waist_max',
        'length_min',
        'length_max',
        'shoulder_min',
        'shoulder_max',
        'sleeve_min',
        'sleeve_max',
        'hip_min',
        'hip_max',
        'thigh_min',
        'thigh_max',
        'inseam_min',
        'inseam_max',
        'is_active',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'body_types'   => 'array',
        'weight_min'   => 'float',
        'weight_max'   => 'float',
        'age_min'      => 'integer',
        'age_max'      => 'integer',
        'chest_min'    => 'float',
        'chest_max'    => 'float',
        'waist_min'    => 'float',
        'waist_max'    => 'float',
        'length_min'   => 'float',
        'length_max'   => 'float',
        'shoulder_min' => 'float',
        'shoulder_max' => 'float',
        'sleeve_min'   => 'float',
        'sleeve_max'   => 'float',
        'hip_min'      => 'float',
        'hip_max'      => 'float',
        'thigh_min'    => 'float',
        'thigh_max'    => 'float',
        'inseam_min'   => 'float',
        'inseam_max'   => 'float',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Check if measurements match this size
    public function matchesMeasurements(array $measurements): bool
    {
        $chest = $measurements['chest'] ?? null;
        $waist = $measurements['waist'] ?? null;

        if ($chest && $this->chest_min && $this->chest_max) {
            if ($chest < $this->chest_min || $chest > $this->chest_max) {
                return false;
            }
        }

        if ($waist && $this->waist_min && $this->waist_max) {
            if ($waist < $this->waist_min || $waist > $this->waist_max) {
                return false;
            }
        }

        return true;
    }
}