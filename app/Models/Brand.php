<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $guarded = [];
    protected $appends = ['image_url'];

    use HasFactory;

    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id');
    }

    public function getImageUrlAttribute()
    {
        if (!empty($this->image)) {
            if (filter_var($this->image, FILTER_VALIDATE_URL)) {
                return $this->image;
            }
            if (file_exists(public_path('uploads/brands/' . $this->image))) {
                return asset('uploads/brands/' . $this->image);
            }
        }
        return null;
    }
}
