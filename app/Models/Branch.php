<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'shop_name',
        'address',
        'schedule',
        'badge',
        'highlights',
        'map_link',
        'icon',
        'logo',
        'status',
        'created_by',
    ];

    public function getPrimaryPhoneAttribute()
    {
        if (empty($this->phone)) {
            return '';
        }
        $parts = preg_split('/[\/,|]+/', $this->phone);
        return trim($parts[0] ?? $this->phone);
    }

    public function getCleanPhoneUrlAttribute()
    {
        $primary = $this->primary_phone;
        return preg_replace('/[^0-9+]/', '', $primary);
    }

    protected static function booted()
    {
        if (env('HIDE_ADMIN_BRANCH') === 'yes') {
            static::addGlobalScope('hideAdminBranch', function ($builder) {
                $hasIdBound = false;
                foreach ($builder->getQuery()->wheres as $where) {
                    if (isset($where['column']) && (
                        $where['column'] === 'id' || 
                        $where['column'] === 'branches.id'
                    )) {
                        $hasIdBound = true;
                        break;
                    }
                }
                if (!$hasIdBound) {
                    $builder->where('branches.id', '!=', 1);
                }
            });
        }
    }
}
