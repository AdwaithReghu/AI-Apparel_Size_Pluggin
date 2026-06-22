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
        'is_active',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
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