<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'order_payments';
    protected $primaryKey = 'payment_id';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_id',
        'payment_method_id',
        'amount',
        'status',
        'payment_proof',
        'notes',
        'paid_at',
        'transaction_id',
        'payment_type',
        'snap_token'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime'
    ];

    /**
     * Get the order that owns the payment.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    /**
     * Get the user who verified the payment.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the payment method that owns the payment.
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method', 'code');
    }

    /**
     * Get formatted payment amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Get payment method label.
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        // Try to get from PaymentMethod model first
        if ($this->paymentMethod) {
            return $this->paymentMethod->name;
        }

        // Fallback to manual mapping
        return match($this->payment_method) {
            'cod' => 'Cash on Delivery (COD)',
            'qris' => 'QRIS',
            'bank_transfer' => 'Transfer Bank',
            'e_wallet' => 'Dompet Digital',
            'cash' => 'Tunai',
            'credit_card' => 'Kartu Kredit',
            'debit_card' => 'Kartu Debit',
            default => ucfirst(str_replace('_', ' ', $this->payment_method))
        };
    }

    /**
     * Get payment status label.
     */
    public function getPaymentStatusLabelAttribute(): string
    {
        // Handle null or empty status
        if (empty($this->status)) {
            return 'Status Tidak Diketahui';
        }
        
        return match($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'paid' => 'Sudah Dibayar',
            'verified' => 'Terverifikasi',
            'failed' => 'Gagal',
            'refunded' => 'Dikembalikan',
            'cancelled' => 'Dibatalkan',
            // Midtrans specific statuses
            'settlement' => 'Pembayaran Berhasil',
            'capture' => 'Pembayaran Berhasil',
            'challenge' => 'Perlu Verifikasi',
            'deny' => 'Pembayaran Ditolak',
            'expire' => 'Pembayaran Kedaluwarsa',
            'cancel' => 'Pembayaran Dibatalkan',
            default => ucfirst($this->status) ?: 'Status Tidak Diketahui'
        };
    }

    /**
     * Get payment status badge color
     */
    public function getStatusBadgeColorAttribute()
    {
        // Handle null or empty status
        if (empty($this->status)) {
            return 'badge-neutral';
        }
        
        return match($this->status) {
            'pending' => 'badge-warning',
            'verified' => 'badge-success',
            'paid' => 'badge-success',
            'failed' => 'badge-error',
            'cancelled' => 'badge-ghost',
            'refunded' => 'badge-info',
            // Midtrans specific statuses
            'settlement' => 'badge-success',
            'capture' => 'badge-success',
            'challenge' => 'badge-warning',
            'deny' => 'badge-error',
            'expire' => 'badge-error',
            'cancel' => 'badge-ghost',
            default => 'badge-neutral'
        };
    }

    /**
     * Get payment method icon class.
     */
    public function getPaymentMethodIconAttribute(): string
    {
        if ($this->paymentMethod) {
            return $this->paymentMethod->getIconClass();
        }

        return match($this->payment_method) {
            'cod' => 'fas fa-money-bill-wave',
            'qris' => 'fas fa-qrcode',
            'bank_transfer' => 'fas fa-university',
            'e_wallet' => 'fas fa-mobile-alt',
            default => 'fas fa-credit-card'
        };
    }

    /**
     * Get payment method color class.
     */
    public function getPaymentMethodColorAttribute(): string
    {
        if ($this->paymentMethod) {
            return $this->paymentMethod->getColorClass();
        }

        return match($this->payment_method) {
            'cod' => 'text-green-600',
            'qris' => 'text-blue-600',
            'bank_transfer' => 'text-purple-600',
            'e_wallet' => 'text-orange-600',
            default => 'text-gray-600'
        };
    }



    /**
     * Check if payment is verified.
     */
    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    /**
     * Check if payment is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment is paid.
     */
    public function isPaid(): bool
    {
        return in_array($this->status, ['paid', 'settlement', 'capture']);
    }

    /**
     * Check if payment is failed.
     */
    public function isFailed(): bool
    {
        return in_array($this->status, ['failed', 'deny', 'expire']);
    }

    /**
     * Check if payment is cancelled.
     */
    public function isCancelled(): bool
    {
        return in_array($this->status, ['cancelled', 'cancel']);
    }

    /**
     * Scope for filtering by payment status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for verified payments.
     */
    public function scopeVerified($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope for pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}