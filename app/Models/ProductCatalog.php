<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCatalog extends Model
{
    use HasFactory;

    protected $table = 'product_catalog';

    protected $fillable = [
        'product_no',
        'name',
        'rate',
        'size',
        'work',
        'design',
        'material',
        'colours',
        'image_path',
        'description',
        'is_active'
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // Accessor for formatted rate
    public function getFormattedRateAttribute()
    {
        return '₹' . number_format($this->rate, 2);
    }
}