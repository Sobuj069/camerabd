<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'link',
        'position',
        'order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Scope for active banners
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope for banner position
     */
    public function scopePosition($query, $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Get accessible image URL with fallback
     */
    public function getImageUrlAttribute()
    {
        if (empty($this->image)) {
            return asset('frontend/images/banner-placeholder.png');
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        return asset('uploads/banners/' . $this->image);
    }
}
