<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteOrderItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['image_url'];

    public function order()
    {
        return $this->belongsTo(WebsiteOrder::class, 'website_order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }

    public function getImageUrlAttribute()
    {
        if (!empty($this->product_image)) {
            return $this->product_image;
        }
        if ($this->product && !empty($this->product->thumbnail_url)) {
            return $this->product->thumbnail_url;
        }
        return asset('backend/images/no_images.png');
    }

    public function getSubtotalAttribute($value)
    {
        if ($value !== null && (float)$value > 0) {
            return (float)$value;
        }
        return (float)($this->total_price ?: (($this->unit_price ?: 0) * ($this->quantity ?: 1)));
    }

    public function getProductCodeAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
        return $this->product_barcode ?: ($this->product?->barcode ?: 'N/A');
    }

    protected static function booted()
    {
        static::saving(function ($item) {
            if (isset($item->unit_price) && isset($item->quantity)) {
                $item->total_price = $item->total_price ?: ($item->unit_price * $item->quantity);
                $item->subtotal = $item->subtotal ?: $item->total_price;
            }
            if (!empty($item->product_barcode) && empty($item->product_code)) {
                $item->product_code = $item->product_barcode;
            }
        });
    }
}
