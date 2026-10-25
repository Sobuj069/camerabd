<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];
    protected $appends = ['image_url'];

    use HasFactory;

    protected static function booted()
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('mega_menu_categories_v2');
            \Illuminate\Support\Facades\Cache::forget('frontend_homepage_sections_v2');
        });
        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('mega_menu_categories_v2');
            \Illuminate\Support\Facades\Cache::forget('frontend_homepage_sections_v2');
        });
    }

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class, 'category_id');
    }

    public function getImageUrlAttribute()
    {
        if (!empty($this->image)) {
            if (filter_var($this->image, FILTER_VALIDATE_URL)) {
                return $this->image;
            }
            if (file_exists(public_path('uploads/categories/' . $this->image))) {
                return asset('uploads/categories/' . $this->image);
            }
        }
        return null;
    }
}

