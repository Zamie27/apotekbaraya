<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Refund extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'refunds';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_id',
        'payment_id',
        'refund_key',
        'midtrans_transaction_id',
        'refund_amount',
        'original_amount',
        'refund_type',
        'status',
        'reason',
        'midtrans_response',
        'requested_at',
        'processed_at',
        'requested_by',
        'processed_by',
        'admin_notes'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'refund_amount' => 'decimal:2',
        'original_amount' => 'decimal:2',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
        'midtrans_response' => 'array'
    ];

    /**
     * Get the order that owns the refund.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the payment that owns the refund.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    /**
     * Get the user who requested the refund.
     */
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get the admin who processed the refund.
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Scope a query to only include pending refunds.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include completed refunds.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include failed refunds.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Check if refund is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if refund is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if refund is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if refund is full refund.
     */
    public function isFullRefund(): bool
    {
        return $this->refund_type === 'full';
    }

    /**
     * Check if refund is partial refund.
     */
    public function isPartialRefund(): bool
    {
        return $this->refund_type === 'partial';
    }

    /**
     * Get formatted refund amount.
     */
    public function getFormattedRefundAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->refund_amount, 0, ',', '.');
    }

    /**
     * Get formatted original amount.
     */
    public function getFormattedOriginalAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->original_amount, 0, ',', '.');
    }

    /**
     * Get status label with color class.
     */
    public function getStatusLabelAttribute(): array
    {
        $labels = [
            'pending' => ['text' => 'Menunggu', 'class' => 'badge-warning'],
            'processing' => ['text' => 'Diproses', 'class' => 'badge-info'],
            'completed' => ['text' => 'Selesai', 'class' => 'badge-success'],
            'failed' => ['text' => 'Gagal', 'class' => 'badge-error'],
            'cancelled' => ['text' => 'Dibatalkan', 'class' => 'badge-neutral']
        ];

        return $labels[$this->status] ?? ['text' => 'Unknown', 'class' => 'badge-neutral'];
    }
}
