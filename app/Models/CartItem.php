<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'cart_item_id';
    
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the cart that owns this item
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class, 'cart_id', 'cart_id');
    }

    /**
     * Get the product for this cart item
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Calculate subtotal for this item
     */
    public function getSubtotalAttribute(): float
    {
        return $this->quantity * $this->price;
    }

    /**
     * Get formatted subtotal
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Check if quantity exceeds available stock
     */
    public function exceedsStock(): bool
    {
        return $this->quantity > $this->product->stock;
    }

    /**
     * Get maximum allowed quantity based on stock
     */
    public function getMaxQuantityAttribute(): int
    {
        return min($this->product->stock, 99); // Max 99 items per product
    }

    /**
     * Update quantity with stock validation
     */
    public function updateQuantity(int $quantity): bool
    {
        if ($quantity <= 0) {
            $this->delete();
            return true;
        }

        if ($quantity > $this->product->stock) {
            return false; // Quantity exceeds stock
        }

        $this->update(['quantity' => $quantity]);
        return true;
    }

    /**
     * Increase quantity by specified amount
     */
    public function increaseQuantity(int $amount = 1): bool
    {
        return $this->updateQuantity($this->quantity + $amount);
    }

    /**
     * Decrease quantity by specified amount
     */
    public function decreaseQuantity(int $amount = 1): bool
    {
        return $this->updateQuantity($this->quantity - $amount);
    }
}