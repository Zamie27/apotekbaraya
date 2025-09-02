<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $primaryKey = 'order_id';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_number',
        'user_id',
        'subtotal',
        'delivery_fee',
        'discount_amount',
        'total_price',
        'status',
        'payment_method_code',
        'payment_reference',
        'payment_link_id',
        'payment_url',
        'payment_expired_at',
        'payment_instructions',
        'shipping_type',
        'shipping_distance',
        'is_free_shipping',
        'shipping_address',
        'notes',
        'confirmed_at',
        'processing_at',
        'receipt_image',
        'confirmation_note',
        'cancelled_at',
        'cancel_reason',
        'cancelled_by',
        'waiting_payment_at',
        'waiting_confirmation_at',
        'ready_to_ship_at',
        'ready_for_pickup_at',
        'shipped_at',
        'picked_up_at',
        'delivered_at',
        'completed_at',
        'pickup_image'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
        'shipping_distance' => 'decimal:2',
        'is_free_shipping' => 'boolean',
        'shipping_address' => 'array',
        'payment_instructions' => 'array',
        'payment_expired_at' => 'datetime',
        'waiting_payment_at' => 'datetime',
        'waiting_confirmation_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'processing_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'ready_to_ship_at' => 'datetime',
        'ready_for_pickup_at' => 'datetime',
        'shipped_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the order items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    /**
     * Get the delivery information for the order.
     */
    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class, 'order_id', 'order_id');
    }

    /**
     * Get the payment information for the order.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'order_id', 'order_id');
    }

    /**
     * Get the user who cancelled the order.
     */
    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by', 'user_id');
    }

    /**
     * Scope for filtering orders by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering orders by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get formatted total price.
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    /**
     * Get formatted subtotal.
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    /**
     * Get formatted delivery fee.
     */
    public function getFormattedDeliveryFeeAttribute(): string
    {
        return 'Rp ' . number_format($this->delivery_fee, 0, ',', '.');
    }

    /**
     * Get formatted discount amount.
     */
    public function getFormattedDiscountAttribute(): string
    {
        return 'Rp ' . number_format($this->discount_amount, 0, ',', '.');
    }

    /**
     * Get total amount (alias for total_price).
     */
    public function getTotalAmountAttribute(): float
    {
        return $this->total_price;
    }

    /**
     * Get shipping cost (alias for delivery_fee).
     */
    public function getShippingCostAttribute(): float
    {
        return $this->delivery_fee ?? 0;
    }

    /**
     * Calculate subtotal from order items (for validation purposes).
     */
    public function calculateSubtotal(): float
    {
        return $this->items->sum(function ($item) {
            return $item->qty * $item->price;
        });
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            'pending' => 'badge-warning',
            'waiting_payment' => 'badge-warning',
            'waiting_confirmation' => 'badge-neutral',
            'confirmed' => 'badge-info',
            'processing' => 'badge-primary',
            'ready_to_ship' => 'badge-secondary',
            'ready_for_pickup' => 'badge-secondary',
            'shipped' => 'badge-accent',
            'picked_up' => 'badge-accent',
            'delivered' => 'badge-success',
            'completed' => 'badge-success',
            'cancelled' => 'badge-error',
            default => 'badge-ghost'
        };
    }







    /**
     * Get human readable status.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pesanan Dibuat',
            'waiting_payment' => 'Menunggu Pembayaran',
            'waiting_confirmation' => 'Menunggu Konfirmasi',
            'confirmed' => 'Dikonfirmasi',
            'processing' => 'Diproses',
            'ready_to_ship' => 'Siap Diantar',
            'ready_for_pickup' => 'Siap Diambil',
            'shipped' => 'Dikirim',
            'picked_up' => 'Diambil',
            'delivered' => 'Selesai',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status)
        };
    }

    /**
     * Check if order can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'waiting_payment', 'waiting_confirmation']);
    }

    /**
     * Check if order is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'delivered';
    }

    /**
     * Get shipping type label.
     */
    public function getShippingTypeLabelAttribute(): string
    {
        return match($this->shipping_type) {
            'pickup' => 'Ambil di Toko',
            'delivery' => 'Kirim ke Alamat',
            default => ucfirst($this->shipping_type)
        };
    }



    /**
     * Check if payment is expired.
     */
    public function isPaymentExpired(): bool
    {
        return $this->payment_expired_at && $this->payment_expired_at->isPast();
    }

    /**
     * Get payment status for display.
     */
    public function getPaymentStatusAttribute(): string
    {
        if ($this->status === 'confirmed') {
            return 'Dibayar';
        } elseif ($this->isPaymentExpired()) {
            return 'Kadaluarsa';
        } else {
            return 'Menunggu Pembayaran';
        }
        
        return match($this->status) {
            'pending' => 'Pesanan Dibuat',
            'waiting_payment' => 'Menunggu Pembayaran',
            'waiting_confirmation' => 'Menunggu Konfirmasi',
            'confirmed' => 'Dibayar',
            'cancelled' => 'Dibatalkan',
            'refunded' => 'Dikembalikan',
            default => 'Tidak Diketahui'
        };
    }

    /**
     * Cancel order with reason and user who cancelled.
     */
    public function cancelOrder(string $reason, int $cancelledBy): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }

        try {
            $midtransCancelled = false;
            $midtransMessage = '';
            
            // Cancel transaction in Midtrans first if order has payment
            if ($this->payment && $this->payment->snap_token) {
                $midtransService = new \App\Services\MidtransService();
                $cancelResult = $midtransService->cancelTransaction($this->order_number);
                
                if ($cancelResult['success']) {
                    $midtransCancelled = true;
                    \Log::info('Midtrans transaction cancelled successfully', [
                        'order_id' => $this->order_id,
                        'order_number' => $this->order_number
                    ]);
                } else {
                    $midtransMessage = $cancelResult['message'] ?? 'Unknown error';
                    $currentStatus = $cancelResult['current_status'] ?? 'unknown';
                    
                    \Log::warning('Failed to cancel Midtrans transaction, proceeding with local cancellation', [
                        'order_id' => $this->order_id,
                        'order_number' => $this->order_number,
                        'midtrans_error' => $midtransMessage,
                        'current_status' => $currentStatus
                    ]);
                    
                    // If transaction is already expired or settled, it's acceptable to proceed
                    if (in_array($currentStatus, ['expire', 'settlement', 'capture'])) {
                        \Log::info('Transaction already in final state, proceeding with local cancellation', [
                            'order_id' => $this->order_id,
                            'current_status' => $currentStatus
                        ]);
                    }
                }
            }

            // Update order status to cancelled
            $this->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancel_reason' => $reason,
                'cancelled_by' => $cancelledBy
            ]);

            // Update payment status if exists
            if ($this->payment) {
                $this->payment->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now()
                ]);
            }

            return true;
        } catch (\Exception $e) {
            \Log::error('Error cancelling order', [
                'order_id' => $this->order_id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Confirm order by apoteker.
     */
    public function confirmOrder(int $confirmedBy, ?string $note = null): bool
    {
        if (!$this->canBeConfirmed()) {
            return false;
        }

        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
            'confirmation_note' => $note
        ]);

        return true;
    }

    /**
     * Mark order as processing.
     */
    public function markAsProcessing(): bool
    {
        if ($this->status !== 'confirmed') {
            return false;
        }

        $this->update([
            'status' => 'processing',
            'processing_at' => now()
        ]);

        return true;
    }

    /**
     * Mark order as ready to ship with receipt upload and courier assignment.
     */
    public function markAsReadyToShip(string $receiptImagePath, ?int $courierId = null): bool
    {
        if ($this->status !== 'processing') {
            return false;
        }

        // Determine status based on shipping type
        $newStatus = $this->shipping_type === 'pickup' ? 'ready_for_pickup' : 'ready_to_ship';
        $timestampField = $this->shipping_type === 'pickup' ? 'ready_for_pickup_at' : 'ready_to_ship_at';

        $this->update([
            'status' => $newStatus,
            'receipt_image' => $receiptImagePath,
            $timestampField => now()
        ]);

        // Create or update delivery record if shipping type is delivery
        if ($this->shipping_type === 'delivery') {
            $deliveryData = [
                'delivery_address' => $this->shipping_address,
                'delivery_fee' => $this->delivery_fee,
                'delivery_type' => 'standard',
                'delivery_status' => 'ready_to_ship',
                'estimated_delivery' => now()->addDays(1)
            ];

            // Add courier_id if provided
            if ($courierId) {
                $deliveryData['courier_id'] = $courierId;
            }

            if (!$this->delivery) {
                $this->delivery()->create($deliveryData);
            } else {
                // Update existing delivery record with courier assignment
                $this->delivery->update($deliveryData);
            }
        }

        return true;
    }

    /**
     * Mark order as ready for pickup (alias for markAsReadyToShip for pickup orders).
     */
    public function markAsReadyForPickup(string $receiptImagePath): bool
    {
        if ($this->status !== 'processing' || $this->shipping_type !== 'pickup') {
            return false;
        }

        return $this->markAsReadyToShip($receiptImagePath);
    }

    /**
     * Mark order as picked up by customer.
     */
    public function markAsPickedUp(string $pickupImagePath): bool
    {
        if ($this->status !== 'ready_for_pickup') {
            return false;
        }

        $this->update([
            'status' => 'picked_up',
            'pickup_image' => $pickupImagePath,
            'picked_up_at' => now()
        ]);

        // Automatically mark as completed for pickup orders
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        return true;
    }

    /**
     * Mark order as shipped.
     */
    public function markAsShipped(): bool
    {
        if ($this->status !== 'ready_to_ship') {
            return false;
        }

        $this->update([
            'status' => 'shipped',
            'shipped_at' => now()
        ]);

        return true;
    }

    /**
     * Mark order as delivered.
     */
    public function markAsDelivered(): bool
    {
        if ($this->status !== 'shipped') {
            return false;
        }

        $this->update([
            'status' => 'delivered',
            'delivered_at' => now()
        ]);

        // Automatically mark as completed for delivery orders
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        return true;
    }

    /**
     * Check if order can be confirmed.
     */
    public function canBeConfirmed(): bool
    {
        return in_array($this->status, ['pending', 'waiting_confirmation']);
    }

    /**
     * Check if order can be processed.
     */
    public function canBeProcessed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check if order can be marked as ready to ship.
     */
    public function canBeReadyToShip(): bool
    {
        return $this->status === 'processing';
    }

    /**
     * Check if order can be shipped.
     */
    public function canBeShipped(): bool
    {
        return $this->status === 'ready_to_ship';
    }

    /**
     * Check if order can be picked up.
     */
    public function canBePickedUp(): bool
    {
        return $this->status === 'ready_for_pickup';
    }

    /**
     * Check if order can be marked as delivered.
     */
    public function canBeDelivered(): bool
    {
        return $this->status === 'shipped';
    }

    /**
     * Check if order is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Get order status workflow steps.
     */
    public function getStatusWorkflowAttribute(): array
    {
        // Define all possible statuses for both delivery and pickup
        $allDeliveryStatuses = ['waiting_payment', 'waiting_confirmation', 'confirmed', 'processing', 'ready_to_ship', 'shipped', 'delivered', 'completed'];
        $allPickupStatuses = ['waiting_payment', 'waiting_confirmation', 'confirmed', 'processing', 'ready_for_pickup', 'picked_up', 'completed'];
        
        $steps = [
            [
                'status' => 'pending',
                'label' => 'Pesanan Dibuat',
                'description' => 'Pesanan telah dibuat dan menunggu pembayaran',
                'completed' => $this->status === 'pending' || ($this->shipping_type === 'delivery' ? 
                    in_array($this->status, $allDeliveryStatuses) : 
                    in_array($this->status, $allPickupStatuses)),
                'current' => $this->status === 'pending',
                'date' => $this->created_at
            ],
            [
                'status' => 'waiting_payment',
                'label' => 'Menunggu Pembayaran',
                'description' => 'Pesanan menunggu pembayaran dari pelanggan',
                'completed' => $this->status === 'waiting_payment' || ($this->shipping_type === 'delivery' ? 
                    in_array($this->status, ['waiting_confirmation', 'confirmed', 'processing', 'ready_to_ship', 'shipped', 'delivered', 'completed']) : 
                    in_array($this->status, ['waiting_confirmation', 'confirmed', 'processing', 'ready_for_pickup', 'picked_up', 'completed'])),
                'current' => $this->status === 'waiting_payment',
                'date' => $this->waiting_payment_at
            ],
            [
                'status' => 'waiting_confirmation',
                'label' => 'Menunggu Konfirmasi',
                'description' => 'Pesanan telah dibayar dan menunggu konfirmasi dari apoteker',
                'completed' => $this->status === 'waiting_confirmation' || ($this->shipping_type === 'delivery' ? 
                    in_array($this->status, ['confirmed', 'processing', 'ready_to_ship', 'shipped', 'delivered', 'completed']) : 
                    in_array($this->status, ['confirmed', 'processing', 'ready_for_pickup', 'picked_up', 'completed'])),
                'current' => $this->status === 'waiting_confirmation',
                'date' => $this->waiting_confirmation_at
            ],
            [
                'status' => 'confirmed',
                'label' => 'Dikonfirmasi',
                'description' => 'Pesanan telah dikonfirmasi dan akan diproses',
                'completed' => $this->status === 'confirmed' || ($this->shipping_type === 'delivery' ? 
                    in_array($this->status, ['processing', 'ready_to_ship', 'shipped', 'delivered', 'completed']) : 
                    in_array($this->status, ['processing', 'ready_for_pickup', 'picked_up', 'completed'])),
                'current' => $this->status === 'confirmed',
                'date' => $this->confirmed_at
            ],
            [
                'status' => 'processing',
                'label' => 'Diproses',
                'description' => 'Pesanan sedang disiapkan',
                'completed' => $this->status === 'processing' || ($this->shipping_type === 'delivery' ? 
                    in_array($this->status, ['ready_to_ship', 'shipped', 'delivered', 'completed']) : 
                    in_array($this->status, ['ready_for_pickup', 'picked_up', 'completed'])),
                'current' => $this->status === 'processing',
                'date' => $this->processing_at
            ]
        ];

        // Delivery specific steps
        if ($this->shipping_type === 'delivery') {
            $steps[] = [
                'status' => 'ready_to_ship',
                'label' => 'Siap Diantar',
                'description' => 'Pesanan siap untuk dikirim ke kurir',
                'completed' => $this->status === 'ready_to_ship' || in_array($this->status, ['shipped', 'delivered', 'completed']),
                'current' => $this->status === 'ready_to_ship',
                'date' => $this->ready_to_ship_at
            ];
            
            $steps[] = [
                'status' => 'shipped',
                'label' => 'Sedang Diantar',
                'description' => 'Pesanan sedang dalam perjalanan',
                'completed' => $this->status === 'shipped' || in_array($this->status, ['delivered', 'completed']),
                'current' => $this->status === 'shipped',
                'date' => $this->shipped_at
            ];
            
            $steps[] = [
                'status' => 'delivered',
                'label' => 'Sampai Tujuan',
                'description' => 'Pesanan telah sampai di tujuan',
                'completed' => $this->status === 'delivered' || $this->status === 'completed',
                'current' => $this->status === 'delivered',
                'date' => $this->delivered_at
            ];
        } else {
            // Add pickup-specific steps
            $steps[] = [
                'status' => 'ready_for_pickup',
                'label' => 'Siap Diambil',
                'description' => 'Pesanan telah siap dan dapat diambil di toko',
                'completed' => $this->status === 'ready_for_pickup' || in_array($this->status, ['picked_up', 'completed']),
                'current' => $this->status === 'ready_for_pickup',
                'date' => $this->ready_for_pickup_at ?? $this->ready_to_ship_at
            ];
            
            $steps[] = [
                'status' => 'picked_up',
                'label' => 'Diambil',
                'description' => 'Pesanan telah diambil pelanggan',
                'completed' => $this->status === 'picked_up' || $this->status === 'completed',
                'current' => $this->status === 'picked_up',
                'date' => $this->picked_up_at
            ];
        }

        // Add completed step for both delivery and pickup
        if (!$this->isCancelled()) {
            $steps[] = [
                'status' => 'completed',
                'label' => 'Selesai',
                'description' => 'Pesanan telah selesai',
                'completed' => $this->status === 'completed',
                'current' => $this->status === 'completed',
                'date' => $this->completed_at
            ];
        }

        // Add cancellation step if order is cancelled
        if ($this->isCancelled()) {
            $steps[] = [
                'status' => 'cancelled',
                'label' => 'Dibatalkan',
                'description' => 'Pesanan dibatalkan: ' . ($this->cancel_reason ?? 'Tidak ada keterangan'),
                'completed' => true,
                'current' => true,
                'date' => $this->cancelled_at
            ];
        }

        return $steps;
    }

    /**
     * Get next possible status transitions.
     */
    public function getNextStatusOptionsAttribute(): array
    {
        return match($this->status) {
            'pending' => ['waiting_payment', 'cancelled'],
            'waiting_payment' => ['waiting_confirmation', 'cancelled'],
            'waiting_confirmation' => ['confirmed', 'cancelled'],
            'confirmed' => ['processing', 'cancelled'],
            'processing' => ['ready_to_ship', 'cancelled'],
            'ready_to_ship' => ['shipped'],
            'shipped' => ['delivered'],
            default => []
        };
    }

    /**
     * Scope for orders that can be cancelled.
     */
    public function scopeCancellable($query)
    {
        return $query->whereIn('status', ['pending', 'waiting_payment', 'waiting_confirmation']);
    }

    /**
     * Scope for active orders (not cancelled or delivered).
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['cancelled', 'delivered']);
    }

    /**
     * Scope for completed orders.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'delivered');
    }
}