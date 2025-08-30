<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';
    protected $primaryKey = 'order_item_id';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'qty',
        'price'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'qty' => 'integer',
        'price' => 'decimal:2'
    ];

    /**
     * Get the order that owns the order item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    /**
     * Get the product that belongs to the order item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the total price for this order item.
     */
    public function getTotalPriceAttribute(): float
    {
        return $this->qty * $this->price;
    }

    /**
     * Get formatted total price.
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    /**
     * Get formatted unit price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Get quantity (alias for qty).
     */
    public function getQuantityAttribute(): int
    {
        return $this->qty;
    }

    /**
     * Get subtotal (alias for total_price).
     */
    public function getSubtotalAttribute(): float
    {
        return $this->total_price;
    }
}