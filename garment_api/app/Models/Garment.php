<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Garment extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id',
    'brand_id',
    'category_id',
    'status',
    'measurements',
    'garment_type',
    'chest',
    'waist',
    'length',
    'shoulder',
    'hip',
    'thigh',
    'knee',
    'ankle',
    'outseam',
    'inseam',
    'rise',
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scans()
    {
        return $this->hasMany(Scan::class);
    }

    public function brandModel()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}