<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomepageSection extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'status' => 'boolean',
        'is_system' => 'boolean',
        'order' => 'integer',
        'meta' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($section) {
            if (empty($section->key)) {
                $baseKey = \Illuminate\Support\Str::slug($section->name, '_') ?: 'section';
                $key = $baseKey;
                $counter = 1;
                while (static::where('key', $key)->exists()) {
                    $key = $baseKey . '_' . $counter++;
                }
                $section->key = $key;
            }
        });
    }

    /**
     * Active products displayed on website frontend.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'homepage_section_products', 'homepage_section_id', 'product_id')
            ->where('products.is_service', 0)
            ->where('products.show_on_website', 1)
            ->withTimestamps();
    }

    /**
     * All assigned products (including hidden from website) for admin panel.
     */
    public function allProducts()
    {
        return $this->belongsToMany(Product::class, 'homepage_section_products', 'homepage_section_id', 'product_id')
            ->where('products.is_service', 0)
            ->withTimestamps();
    }
}
