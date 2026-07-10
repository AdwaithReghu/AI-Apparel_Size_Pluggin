<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Garment extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id',
    'name',        
    'brand',       
    'category',    
    'size_label',  
    'brand_id',
    'category_id',
    'status',
    'measurements',
    'garment_type',
    // shirt fields
    'chest', 'waist', 'length', 'shoulder', 'sleeve',
    // pants fields
    'hip', 'thigh', 'knee', 'ankle', 'outseam', 'inseam', 'rise',
    // shoe fields
    'shoe_length', 'shoe_width', 'heel_width',
    'shoe_size_eu', 'shoe_size_uk', 'shoe_size_us',
    'fits_foot_min', 'fits_foot_max',
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