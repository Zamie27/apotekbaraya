<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $primaryKey = 'cart_id';
    
    protected $fillable = [
        'user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the cart
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get all cart items for this cart
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class, 'cart_id', 'cart_id');
    }

    /**
     * Get cart items with product details
     */
    public function items(): HasMany
    {
        return $this->cartItems()->with(['product', 'product.category', 'product.images']);
    }

    /**
     * Calculate total items in cart
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->cartItems()->sum('quantity');
    }

    /**
     * Calculate subtotal of cart (using final prices with discounts)
     */
    public function getSubtotalAttribute(): float
    {
        return $this->cartItems()->get()->sum(function ($item) {
            return $item->quantity * $item->product->final_price;
        });
    }

    /**
     * Calculate original total (without discounts)
     */
    public function getOriginalTotalAttribute(): float
    {
        return $this->cartItems()->get()->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });
    }

    /**
     * Calculate total discount amount
     */
    public function getTotalDiscountAttribute(): float
    {
        return $this->original_total - $this->subtotal;
    }

    /**
     * Get formatted original total
     */
    public function getFormattedOriginalTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->original_total, 0, ',', '.');
    }

    /**
     * Get formatted total discount
     */
    public function getFormattedTotalDiscountAttribute(): string
    {
        return 'Rp ' . number_format($this->total_discount, 0, ',', '.');
    }

    /**
     * Get formatted subtotal
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    /**
     * Check if cart is empty
     */
    public function isEmpty(): bool
    {
        return $this->cartItems()->count() === 0;
    }

    /**
     * Clear all items from cart
     */
    public function clearCart(): void
    {
        $this->cartItems()->delete();
    }

    /**
     * Get or create cart for user
     */
    public static function getOrCreateForUser(int $userId): self
    {
        return self::firstOrCreate(['user_id' => $userId]);
    }
}