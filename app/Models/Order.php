<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Refund;

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
        'confirmed_by',
        'processing_at',
        'receipt_image',
        'confirmation_note',
        'receipt_photo',
        'receipt_photo_uploaded_by',
        'receipt_photo_uploaded_at',
        'delivery_photo',
        'delivery_photo_uploaded_by',
        'delivery_photo_uploaded_at',
        'cancelled_at',
        'cancellation_reason',
        'cancelled_by',
        'refund_status',
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
        'completed_at' => 'datetime',
        'receipt_photo_uploaded_at' => 'datetime',
        'delivery_photo_uploaded_at' => 'datetime'
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
     * Get the order items for the order (alias for items).
     */
    public function orderItems(): HasMany
    {
        return $this->items();
    }

    /**
     * Get the courier who failed the delivery.
     */
    public function failedByCourier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'failed_by_courier_id', 'user_id');
    }

    /**
     * Get the refunds for the order.
     */
    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class, 'order_id', 'order_id');
    }

    /**
     * Get the latest refund for the order.
     */
    public function latestRefund(): HasOne
    {
        return $this->hasOne(Refund::class, 'order_id', 'order_id')
                    ->latest('created_at');
    }

    /**
     * Get the user who cancelled the order.
     */
    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by', 'user_id');
    }

    /**
     * Get the user who confirmed the order.
     */
    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by', 'user_id');
    }

    /**
     * Get the user who uploaded the receipt photo.
     */
    public function receiptPhotoUploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receipt_photo_uploaded_by', 'user_id');
    }

    /**
     * Get the user who uploaded the delivery photo.
     */
    public function deliveryPhotoUploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delivery_photo_uploaded_by', 'user_id');
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
        // Check if payment is expired for waiting_payment status
        if ($this->status === 'waiting_payment' && $this->isPaymentExpired()) {
            return 'badge-error';
        }

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
        // Check if payment is expired for waiting_payment status
        if ($this->status === 'waiting_payment' && $this->isPaymentExpired()) {
            return 'Pesanan Expired';
        }

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
     * Confirm order by apoteker.
     */
    public function confirmOrder(int $confirmedBy, ?string $note = null): bool
    {
        try {
            if (!$this->canBeConfirmed()) {
                \Log::warning('Order cannot be confirmed', [
                    'order_id' => $this->order_id,
                    'current_status' => $this->status,
                    'confirmed_by' => $confirmedBy
                ]);
                return false;
            }

            $updateData = [
                'status' => 'confirmed',
                'confirmed_at' => now(),
                'confirmed_by' => $confirmedBy
            ];
            
            // Only add confirmation_note if it's provided
            if ($note !== null && $note !== '') {
                $updateData['confirmation_note'] = $note;
            }

            $this->update($updateData);

            \Log::info('Order confirmed successfully', [
                'order_id' => $this->order_id,
                'confirmed_by' => $confirmedBy,
                'confirmation_note' => $note
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Error confirming order', [
                'order_id' => $this->order_id,
                'confirmed_by' => $confirmedBy,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Mark order as processing.
     */
    public function markAsProcessing(): bool
    {
        try {
            if ($this->status !== 'confirmed') {
                \Log::warning('Order cannot be marked as processing', [
                    'order_id' => $this->order_id,
                    'current_status' => $this->status,
                    'required_status' => 'confirmed'
                ]);
                return false;
            }

            $this->update([
                'status' => 'processing',
                'processing_at' => now()
            ]);

            \Log::info('Order marked as processing successfully', [
                'order_id' => $this->order_id,
                'processing_at' => now()
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Error marking order as processing', [
                'order_id' => $this->order_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Mark order as ready to ship with receipt upload and courier assignment.
     */
    public function markAsReadyToShip(string $receiptPhotoPath, ?int $courierId = null): bool
    {
        if ($this->status !== 'processing') {
            return false;
        }

        // Determine status based on shipping type
        $newStatus = $this->shipping_type === 'pickup' ? 'ready_for_pickup' : 'ready_to_ship';
        $timestampField = $this->shipping_type === 'pickup' ? 'ready_for_pickup_at' : 'ready_to_ship_at';

        $this->update([
            'status' => $newStatus,
            'receipt_photo' => $receiptPhotoPath,
            'receipt_photo_uploaded_by' => auth()->id(),
            'receipt_photo_uploaded_at' => now(),
            $timestampField => now()
        ]);

        // Create or update delivery record if shipping type is delivery
        if ($this->shipping_type === 'delivery') {
            $deliveryData = [
                'delivery_address' => $this->shipping_address,
                'delivery_fee' => $this->delivery_fee,
                'delivery_type' => 'standard',
                'status' => 'ready_to_ship',
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
    public function markAsReadyForPickup(string $receiptPhotoPath): bool
    {
        if ($this->status !== 'processing' || $this->shipping_type !== 'pickup') {
            return false;
        }

        return $this->markAsReadyToShip($receiptPhotoPath);
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
     *
     * @param string|null $imagePath Optional delivery proof image path
     * @return bool
     */
    public function markAsDelivered(string $imagePath = null): bool
    {
        if ($this->status !== 'shipped') {
            return false;
        }

        // Update to delivered status first
        $updateData = [
            'status' => 'delivered',
            'delivered_at' => now()
        ];

        // Add delivery proof image if provided
        if ($imagePath) {
            $updateData['delivery_proof'] = $imagePath;
        }

        $this->update($updateData);

        // Update delivery record if exists
        if ($this->delivery) {
            $deliveryUpdateData = [
                'status' => 'delivered',
                'delivered_at' => now(),
                'delivery_notes' => 'Pesanan telah berhasil diantar'
            ];

            if ($imagePath) {
                $deliveryUpdateData['delivery_photo'] = $imagePath;
            }

            $this->delivery->update($deliveryUpdateData);
        }

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
     * Check if order is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
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
                'description' => 'Pesanan dibatalkan: ' . ($this->cancellation_reason ?? 'Tidak ada keterangan'),
                'completed' => true,
                'current' => true,
                'date' => $this->cancelled_at
            ];
        }

        // Add failed step if order is failed
        if ($this->isFailed()) {
            $steps[] = [
                'status' => 'failed',
                'label' => 'Gagal Diantar',
                'description' => 'Pengiriman gagal: ' . ($this->failed_reason ?? 'Tidak ada keterangan'),
                'completed' => true,
                'current' => true,
                'date' => $this->failed_at,
                'failed_reason' => $this->failed_reason,
                'failed_by_courier' => $this->failedByCourier ? $this->failedByCourier->name : null,
                'failed_by_courier_phone' => $this->failedByCourier ? $this->failedByCourier->phone : null
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

    /**
     * Cancel order with optional refund processing.
     * 
     * @param string $reason Cancellation reason
     * @param int $cancelledBy User ID who cancelled the order
     * @param bool $processRefund Whether to process refund automatically
     * @return bool
     */
    public function cancelOrder(string $reason, int $cancelledBy, bool $processRefund = true): bool
    {
        // Check if order can be cancelled
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
                    
                    // Always proceed with local cancellation regardless of Midtrans status
                    // This handles cases where transaction doesn't exist in Midtrans (404)
                    // or is in any other state that prevents cancellation
                    \Log::info('Proceeding with local cancellation despite Midtrans failure', [
                        'order_id' => $this->order_id,
                        'current_status' => $currentStatus,
                        'reason' => 'Midtrans cancellation failed but local cancellation should proceed'
                    ]);
                }
            }

            // Check if payment was paid before cancellation (for refund processing)
            $wasPaymentPaid = $this->payment && $this->payment->status === 'paid';
            
            // Update order status to cancelled
            $updateData = [
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $reason
            ];
            
            // Set refund status to pending if payment was paid
            if ($wasPaymentPaid) {
                $updateData['refund_status'] = 'pending';
            }
            
            $this->update($updateData);

            // Update payment status if exists
            if ($this->payment) {
                $this->payment->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now()
                ]);
            }

            // Process refund if payment was paid and processRefund is true
            if ($processRefund && $wasPaymentPaid) {
                $this->processRefund($cancelledBy, $reason);
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
     * Process refund for cancelled order.
     * 
     * @param int $requestedBy User ID who requested the refund
     * @param string|null $reason Refund reason
     * @return Refund|null
     */
    public function processRefund(int $requestedBy, ?string $reason = null): ?Refund
    {
        // Check if payment exists and is paid
        if (!$this->payment || $this->payment->status !== 'paid') {
            return null;
        }

        // Check if refund already exists
        if ($this->refunds()->exists()) {
            return $this->latestRefund;
        }

        // Create refund record
        $refund = new Refund([
            'order_id' => $this->order_id,
            'payment_id' => $this->payment->payment_id,
            'refund_key' => 'REF-' . $this->order_id . '-' . time(),
            'refund_amount' => $this->payment->amount,
            'reason' => $reason ?? 'Order cancellation',
            'status' => 'pending',
            'requested_by' => $requestedBy,
            'requested_at' => now()
        ]);

        if ($refund->save()) {
            // Try to process refund with Midtrans
            try {
                $midtransService = app(\App\Services\MidtransService::class);
                $result = $midtransService->processRefundOrCancel(
                    $this->payment->transaction_id,
                    $refund->refund_key,
                    $refund->refund_amount,
                    $refund->reason
                );

                if ($result['success']) {
                    $refund->status = 'completed';
                    $refund->processed_at = now();
                    $refund->midtrans_response = json_encode($result['data']);
                    
                    // Update payment status
                    $this->payment->status = 'refunded';
                    $this->payment->save();
                } else {
                    $refund->status = 'failed';
                    $refund->failure_reason = $result['message'];
                }
                
                $refund->save();
            } catch (\Exception $e) {
                $refund->status = 'failed';
                $refund->failure_reason = $e->getMessage();
                $refund->save();
            }

            return $refund;
        }

        return null;
    }



    /**
     * Check if order can be refunded.
     * 
     * @return bool
     */
    public function canBeRefunded(): bool
    {
        return $this->payment && 
               $this->payment->status === 'paid' && 
               in_array($this->status, ['cancelled', 'failed']) &&
               !$this->refunds()->where('status', 'completed')->exists();
    }

    /**
     * Get refund status from latest refund relation.
     * 
     * @return string|null
     */
    public function getLatestRefundStatus(): ?string
    {
        $latestRefund = $this->latestRefund;
        return $latestRefund ? $latestRefund->status : null;
    }

    /**
     * Check if order has pending refund.
     * 
     * @return bool
     */
    public function hasPendingRefund(): bool
    {
        return $this->refunds()->where('status', 'pending')->exists();
    }

    /**
     * Check if order has completed refund.
     * 
     * @return bool
     */
    public function hasCompletedRefund(): bool
    {
        return $this->refunds()->where('status', 'completed')->exists();
    }

    /**
     * Check if order needs refund (cancelled with paid payment).
     * 
     * @return bool
     */
    public function needsRefund(): bool
    {
        return $this->status === 'cancelled' && 
               $this->payment && 
               $this->payment->status === 'paid' && 
               $this->refund_status === 'pending';
    }

    /**
     * Mark refund as completed.
     * 
     * @return bool
     */
    public function markRefundCompleted(): bool
    {
        return $this->update(['refund_status' => 'completed']);
    }

    /**
     * Get refund status label.
     * 
     * @return string|null
     */
    public function getRefundStatusLabel(): ?string
    {
        if (!$this->refund_status) {
            return null;
        }

        return match($this->refund_status) {
            'pending' => 'Menunggu Refund',
            'completed' => 'Sukses Refund',
            default => null
        };
    }

    /**
     * Delete order permanently from database.
     * This will also delete related records (items, payment, delivery, refunds).
     * 
     * @param string $reason Deletion reason
     * @param int $deletedBy User ID who deleted the order
     * @return bool
     */
    public function deleteOrder(string $reason, int $deletedBy): bool
    {
        try {
            \DB::beginTransaction();

            // Log the deletion activity
            \Log::info('Order deletion initiated', [
                'order_id' => $this->order_id,
                'order_number' => $this->order_number,
                'deleted_by' => $deletedBy,
                'reason' => $reason
            ]);

            // Cancel Midtrans transaction if exists
            if ($this->payment && $this->payment->snap_token) {
                try {
                    $midtransService = new \App\Services\MidtransService();
                    $cancelResult = $midtransService->cancelTransaction($this->order_number);
                    
                    \Log::info('Midtrans transaction cancellation attempt', [
                        'order_id' => $this->order_id,
                        'success' => $cancelResult['success'],
                        'message' => $cancelResult['message'] ?? 'No message'
                    ]);
                } catch (\Exception $e) {
                    \Log::warning('Failed to cancel Midtrans transaction during deletion', [
                        'order_id' => $this->order_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Delete related records in proper order
            // Delete order items first
            $this->items()->delete();
            
            // Delete delivery record if exists
            if ($this->delivery) {
                $this->delivery->delete();
            }
            
            // Delete refunds if exists
            $this->refunds()->delete();
            
            // Delete payment record if exists
            if ($this->payment) {
                $this->payment->delete();
            }
            
            // Finally delete the order itself
            $deleted = $this->delete();
            
            if ($deleted) {
                \DB::commit();
                
                \Log::info('Order deleted successfully', [
                    'order_id' => $this->order_id,
                    'order_number' => $this->order_number,
                    'deleted_by' => $deletedBy
                ]);
                
                return true;
            }
            
            \DB::rollBack();
            return false;
            
        } catch (\Exception $e) {
            \DB::rollBack();
            
            \Log::error('Error deleting order', [
                'order_id' => $this->order_id,
                'order_number' => $this->order_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }

    /**
     * Check if order can be deleted.
     * Orders can only be deleted if they are in early stages.
     * 
     * @return bool
     */
    public function canBeDeleted(): bool
    {
        return in_array($this->status, ['pending', 'waiting_payment', 'waiting_confirmation', 'cancelled']);
    }

    /**
     * Check if order is eligible for automatic cancellation
     * 
     * @return bool
     */
    public function isEligibleForAutoCancellation(): bool
    {
        return $this->status === 'waiting_payment' 
            && $this->isPaymentExpired()
            && !$this->hasSuccessfulPayment();
    }

    /**
     * Check if order has successful payment
     * 
     * @return bool
     */
    public function hasSuccessfulPayment(): bool
    {
        return $this->payment && in_array($this->payment->status, ['settlement', 'capture', 'success']);
    }

    /**
     * Cancel order automatically due to expiration
     * 
     * @return bool
     */
    public function cancelDueToExpiration(): bool
    {
        if (!$this->isEligibleForAutoCancellation()) {
            return false;
        }

        try {
            \DB::beginTransaction();

            // Update order status
            $this->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => 'Pesanan Expired - Tidak dibayar dalam 1 hari',
                'cancelled_by' => 'system'
            ]);

            // Restore product stock
            $this->restoreProductStock();

            \DB::commit();

            \Log::info('Order automatically cancelled due to expiration', [
                'order_id' => $this->order_id,
                'order_number' => $this->order_number,
                'total_amount' => $this->total_price,
                'created_at' => $this->created_at,
                'cancelled_at' => now(),
            ]);

            return true;

        } catch (\Exception $e) {
            \DB::rollBack();

            \Log::error('Failed to cancel expired order', [
                'order_id' => $this->order_id,
                'order_number' => $this->order_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }

    /**
     * Restore product stock for cancelled order
     * 
     * @return void
     */
    private function restoreProductStock(): void
    {
        foreach ($this->items as $item) {
            if ($item->product) {
                $item->product->increment('stock_quantity', $item->qty);

                \Log::info('Stock restored for product', [
                    'product_id' => $item->product->product_id,
                    'product_name' => $item->product->name,
                    'quantity_restored' => $item->qty,
                    'new_stock' => $item->product->fresh()->stock_quantity,
                    'order_number' => $this->order_number
                ]);
            }
        }
    }

    /**
     * Scope to find orders eligible for automatic cancellation
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $expireDays
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEligibleForAutoCancellation($query, int $expireDays = 1)
    {
        $expireDate = now()->subDays($expireDays);
        
        return $query->where('status', 'waiting_payment')
            ->where('created_at', '<=', $expireDate)
            ->whereDoesntHave('payment', function ($paymentQuery) {
                $paymentQuery->whereIn('status', ['settlement', 'capture', 'success']);
            });
    }
}