<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warranty extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Relationship with products assigned to this warranty.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'warranty_id');
    }

    /**
     * Helper to format warranty text e.g. "1 Year", "6 Months", "30 Days"
     */
    public function getFormattedPeriodAttribute()
    {
        $unit = $this->period ?: 'Month';
        $duration = (int) $this->duration;
        return $duration . ' ' . $unit . ($duration > 1 ? 's' : '');
    }
}
