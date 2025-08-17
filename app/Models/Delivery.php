<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    use HasFactory;

    protected $table = 'deliveries';
    protected $primaryKey = 'delivery_id';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_id',
        'courier_id',
        'delivery_address',
        'delivery_fee',
        'delivery_type',
        'estimated_delivery',
        'delivered_at',
        'delivery_notes'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'delivery_fee' => 'decimal:2',
        'delivery_address' => 'array',
        'estimated_delivery' => 'datetime',
        'delivered_at' => 'datetime'
    ];

    /**
     * Get the order that owns the delivery.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    /**
     * Get the courier assigned to the delivery.
     */
    public function courier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    /**
     * Get formatted delivery fee.
     */
    public function getFormattedFeeAttribute(): string
    {
        return 'Rp ' . number_format($this->delivery_fee, 0, ',', '.');
    }

    /**
     * Get delivery type label.
     */
    public function getDeliveryTypeLabelAttribute(): string
    {
        return match($this->delivery_type) {
            'standard' => 'Pengiriman Standar',
            'express' => 'Pengiriman Express',
            'same_day' => 'Pengiriman Hari Sama',
            default => ucfirst($this->delivery_type)
        };
    }

    /**
     * Check if delivery is completed.
     */
    public function isDelivered(): bool
    {
        return !is_null($this->delivered_at);
    }

    /**
     * Scope for filtering by courier.
     */
    public function scopeByCourier($query, $courierId)
    {
        return $query->where('courier_id', $courierId);
    }

    /**
     * Scope for pending deliveries.
     */
    public function scopePending($query)
    {
        return $query->whereNull('delivered_at');
    }

    /**
     * Scope for completed deliveries.
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('delivered_at');
    }
}