<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $table = 'payment_methods';
    protected $primaryKey = 'payment_method_id';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'code',
        'name',
        'type',
        'description',
        'config',
        'is_active',
        'sort_order'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Get the payments for this payment method.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'payment_method', 'code');
    }

    /**
     * Scope for active payment methods.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordering by sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get payment methods available for checkout.
     */
    public static function getAvailableForCheckout()
    {
        return static::active()->ordered()->get();
    }

    /**
     * Check if payment method supports pickup orders.
     * All active payment methods support pickup (same as delivery)
     */
    public function supportsPickup(): bool
    {
        return match($this->code) {
            'cod' => true,
            'qris' => true,
            'bank_transfer' => true,
            'e_wallet' => true,

            default => true // Allow all payment methods for both pickup and delivery
        };
    }

    /**
     * Check if payment method supports delivery orders.
     */
    public function supportsDelivery(): bool
    {
        return match($this->code) {
            'cod' => true,
            'qris' => true,
            'bank_transfer' => true,
            'e_wallet' => true,

            default => true // Allow all payment methods for both pickup and delivery
        };
    }

    /**
     * Check if payment method requires verification.
     */
    public function requiresVerification(): bool
    {
        return match($this->code) {
            'bank_transfer' => true,
            'e_wallet' => $this->config['auto_verification'] ?? true ? false : true,
            default => false
        };
    }

    /**
     * Get minimum amount for this payment method.
     */
    public function getMinAmount(): float
    {
        return (float) ($this->config['min_amount'] ?? 0);
    }

    /**
     * Get maximum amount for this payment method.
     */
    public function getMaxAmount(): ?float
    {
        $maxAmount = $this->config['max_amount'] ?? null;
        return $maxAmount ? (float) $maxAmount : null;
    }

    /**
     * Check if amount is valid for this payment method.
     */
    public function isAmountValid(float $amount): bool
    {
        $minAmount = $this->getMinAmount();
        $maxAmount = $this->getMaxAmount();

        if ($amount < $minAmount) {
            return false;
        }

        if ($maxAmount && $amount > $maxAmount) {
            return false;
        }

        return true;
    }

    /**
     * Get icon class based on payment method type
     */
    public function getIconClassAttribute(): string
    {
        return match($this->type) {
            'cash' => 'fas fa-money-bill-wave text-green-500',
            'bank_transfer' => 'fas fa-university text-blue-500',
            'e_wallet' => 'fas fa-mobile-alt text-purple-500',
            'qris' => 'fas fa-qrcode text-indigo-500',
            default => 'fas fa-credit-card text-gray-500'
        };
    }

    /**
     * Get color class based on payment method type
     */
    public function getColorClassAttribute(): string
    {
        return match($this->type) {
            'cash' => 'bg-green-100 text-green-800',
            'bank_transfer' => 'bg-blue-100 text-blue-800',
            'e_wallet' => 'bg-purple-100 text-purple-800',
            'qris' => 'bg-indigo-100 text-indigo-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get min amount as attribute for template access
     */
    public function getMinAmountAttribute(): float
    {
        return $this->getMinAmount();
    }

    /**
     * Get max amount as attribute for template access
     */
    public function getMaxAmountAttribute(): ?float
    {
        return $this->getMaxAmount();
    }

    /**
     * Get supports pickup as attribute for template access
     */
    public function getSupportsPickupAttribute(): bool
    {
        return $this->supportsPickup();
    }

    /**
     * Get supports delivery as attribute for template access
     */
    public function getSupportsDeliveryAttribute(): bool
    {
        return $this->supportsDelivery();
    }
}