<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteOrder extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'note_updated_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::saving(function ($order) {
            // Keep status and order_status synchronized
            if (!empty($order->status) && empty($order->order_status)) {
                $order->order_status = ucfirst($order->status);
            } elseif (!empty($order->order_status) && empty($order->status)) {
                $order->status = strtolower($order->order_status);
            } elseif (!empty($order->status) && !empty($order->order_status)) {
                if ($order->isDirty('status') && !$order->isDirty('order_status')) {
                    $order->order_status = ucfirst($order->status);
                } elseif ($order->isDirty('order_status') && !$order->isDirty('status')) {
                    $order->status = strtolower($order->order_status);
                }
            }

            // Sync other alias fields
            if (!empty($order->phone) && empty($order->customer_phone)) {
                $order->customer_phone = $order->phone;
            } elseif (!empty($order->customer_phone) && empty($order->phone)) {
                $order->phone = $order->customer_phone;
            }

            if (!empty($order->delivery_address) && empty($order->customer_address)) {
                $order->customer_address = $order->delivery_address;
            } elseif (!empty($order->customer_address) && empty($order->delivery_address)) {
                $order->delivery_address = $order->customer_address;
            }

            if (empty($order->courier_service)) {
                if (!empty($order->delivery_method) && str_contains(strtolower($order->delivery_method), 'pathao')) {
                    $order->courier_service = 'Pathao Courier';
                } else {
                    $order->courier_service = 'Steadfast Courier';
                }
            } elseif (str_contains(strtolower($order->courier_service), 'outside') || str_contains(strtolower($order->courier_service), 'inside') || str_contains(strtolower($order->courier_service), 'delivery')) {
                if (str_contains(strtolower($order->courier_service), 'pathao')) {
                    $order->courier_service = 'Pathao Courier';
                } else {
                    $order->courier_service = 'Steadfast Courier';
                }
            }

            if (!empty($order->order_notes) && empty($order->note)) {
                $order->note = $order->order_notes;
            }

            if (isset($order->shipping_cost) && !isset($order->shipping_fee)) {
                $order->shipping_fee = $order->shipping_cost;
            }
        });

        static::saved(function ($order) {
            if (($order->wasChanged('order_status') || $order->wasChanged('status')) && !empty($order->invoice_id)) {
                $invoice = Invoice::find($order->invoice_id);
                $st = ucfirst($order->status ?: $order->order_status);
                if ($invoice && $invoice->order_status !== $st) {
                    $invoice->order_status = $st;
                    $invoice->saveQuietly();
                }
            }
        });
    }

    public function items()
    {
        return $this->hasMany(WebsiteOrderItem::class, 'website_order_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    /**
     * Formatted currency total.
     */
    public function getFormattedTotalAttribute()
    {
        return '৳ ' . number_format($this->total_amount, 2);
    }

    /**
     * Status Badge HTML.
     */
    public function getStatusBadgeAttribute()
    {
        $status = strtolower($this->order_status ?? 'pending');

        return match ($status) {
            'pending' => '<span class="badge badge-warning text-dark font-weight-bold px-2 py-1"><i class="feather icon-clock mr-1"></i>Pending</span>',
            'confirmed', 'approved' => '<span class="badge badge-success font-weight-bold px-2 py-1"><i class="feather icon-check mr-1"></i>Approved</span>',
            'processing' => '<span class="badge badge-info font-weight-bold px-2 py-1"><i class="feather icon-package mr-1"></i>Processing</span>',
            'in_courier', 'shipped' => '<span class="badge badge-secondary font-weight-bold px-2 py-1"><i class="feather icon-truck mr-1"></i>Shipped</span>',
            'delivered' => '<span class="badge badge-success font-weight-bold px-2 py-1"><i class="feather icon-check-circle mr-1"></i>Delivered</span>',
            'cancelled' => '<span class="badge badge-danger font-weight-bold px-2 py-1"><i class="feather icon-x-circle mr-1"></i>Cancelled</span>',
            default => '<span class="badge badge-light text-dark font-weight-bold px-2 py-1">' . ucfirst($status) . '</span>',
        };
    }

    public function getCustomerPhoneAttribute($value)
    {
        return $value ?: ($this->phone ?? '');
    }

    public function getCustomerAddressAttribute($value)
    {
        return $value ?: ($this->delivery_address ?? '');
    }

    public function getCourierServiceAttribute($value)
    {
        return $value ?: ($this->delivery_method ?? '');
    }

    public function getNoteAttribute($value)
    {
        return $value ?: ($this->order_notes ?? '');
    }

    public function getShippingFeeAttribute($value)
    {
        return $value !== null ? $value : ($this->shipping_cost ?? 0);
    }
}
