<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('frontend_homepage_sections_v2');
        });
        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('frontend_homepage_sections_v2');
        });
    }

    public function product_variations()
    {
        return $this->hasMany(ProductVariation::class);
    }
    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function size()
    {
        return $this->belongsTo('App\Models\ProductSize','size_id','id');
    }

    public function color()
    {
        return $this->belongsTo('App\Models\ProductColor','color_id','id');
    }

    public function product_variation()
    {
        return $this->belongsTo(ProductVariation::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }
    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warranty()
    {
        return $this->belongsTo(Warranty::class, 'warranty_id');
    }

    public function homepageSections()
    {
        return $this->belongsToMany(HomepageSection::class, 'homepage_section_products', 'product_id', 'homepage_section_id')->withTimestamps();
    }

    public function hasTransactions()
    {
        return \App\Models\PurchaseItem::where('product_id', $this->id)->exists() ||
               \App\Models\InvoiceItem::where('product_id', $this->id)->exists() ||
               \App\Models\TransferItem::where('product_id', $this->id)->exists() ||
               \App\Models\AdjustStockItem::where('product_id', $this->id)->exists() ||
               \App\Models\ReturnPurchaseItem::where('product_id', $this->id)->exists() ||
               \App\Models\ReturnItem::where('product_id', $this->id)->exists() ||
               \App\Models\UsedPurchaseItem::where('product_id', $this->id)->exists() ||
               \App\Models\UsedItem::where('product_id', $this->id)->exists() ||
               \App\Models\DamageItem::where('product_id', $this->id)->exists();
    }

    /**
     * Get product slug or auto-generate from name
     */
    public function getSlugAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
        return !empty($this->name) ? \Illuminate\Support\Str::slug($this->name) : (string)$this->id;
    }

    /**
     * Set sanitized slug attribute
     */
    public function setSlugAttribute($value)
    {
        $clean = !empty($value) ? \Illuminate\Support\Str::slug($value) : (!empty($this->name) ? \Illuminate\Support\Str::slug($this->name) : null);
        $this->attributes['slug'] = $clean;
    }

    /**
     * Get array of raw stored images (filenames or URLs).
     */
    public function getRawImagesAttribute()
    {
        if (empty($this->images)) {
            return [];
        }

        $raw = $this->images;
        $decoded = json_decode($raw, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values(array_filter($decoded));
        }

        if (str_contains($raw, ',') && !str_starts_with($raw, 'http')) {
            return array_values(array_filter(array_map('trim', explode(',', $raw))));
        }

        return [$raw];
    }

    /**
     * Format a single image filename/URL to absolute URL.
     */
    public function formatImageUrl($img)
    {
        if (empty($img) || !is_string($img)) {
            return asset('backend/images/no_images.png');
        }
        if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) {
            return $img;
        }
        return asset('uploads/products/' . $img);
    }

    /**
     * Get thumbnail URL (primary cover image).
     */
    public function getThumbnailUrlAttribute()
    {
        if (!empty($this->thumbnail)) {
            return $this->formatImageUrl($this->thumbnail);
        }
        $raw = $this->raw_images;
        if (!empty($raw) && !empty($raw[0])) {
            return $this->formatImageUrl($raw[0]);
        }
        return asset('backend/images/no_images.png');
    }

    /**
     * Check if product has a genuine uploaded or URL image.
     */
    public function getHasRealImageAttribute()
    {
        if (!empty($this->thumbnail) && is_string($this->thumbnail)) {
            if (str_starts_with($this->thumbnail, 'http://') || str_starts_with($this->thumbnail, 'https://')) {
                return true;
            }
            return file_exists(public_path('uploads/products/' . $this->thumbnail));
        }
        $raw = $this->raw_images;
        if (!empty($raw) && !empty($raw[0]) && is_string($raw[0])) {
            if (str_starts_with($raw[0], 'http://') || str_starts_with($raw[0], 'https://')) {
                return true;
            }
            return file_exists(public_path('uploads/products/' . $raw[0]));
        }
        return false;
    }

    /**
     * Get primary/featured image URL.
     */
    public function getFeaturedImageAttribute()
    {
        return $this->thumbnail_url;
    }

    /**
     * Get primary image URL (alias).
     */
    public function getImageUrlAttribute()
    {
        return $this->thumbnail_url;
    }

    /**
     * Get all gallery image URLs as an array.
     */
    public function getGalleryImagesAttribute()
    {
        $all = [];
        if (!empty($this->thumbnail)) {
            $all[] = $this->formatImageUrl($this->thumbnail);
        }
        $raw = $this->raw_images;
        foreach ($raw as $img) {
            $url = $this->formatImageUrl($img);
            if (!in_array($url, $all)) {
                $all[] = $url;
            }
        }
        if (empty($all)) {
            return [asset('backend/images/no_images.png')];
        }
        return $all;
    }
}
