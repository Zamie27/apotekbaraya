<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.admin')]
#[Title('Detail Pesanan')]
class OrderDetail extends Component
{
    public $orderId;
    public $order;

    /**
     * Mount the component with order ID.
     */
    public function mount($orderId)
    {
        $this->orderId = $orderId;
        $this->loadOrder();
    }

    /**
     * Load order data with all necessary relationships.
     */
    public function loadOrder()
    {
        $this->order = Order::with([
            'items.product',
            'payment',
            'delivery.courier',
            'user',
            'cancelledBy',
            'refunds'
        ])->findOrFail($this->orderId);
    }

    /**
     * Get formatted shipping address.
     */
    public function getShippingAddressProperty()
    {
        if (!$this->order->shipping_address) {
            return null;
        }

        // shipping_address is already cast as array in Order model
        $address = $this->order->shipping_address;
        
        return [
            'name' => $address['name'] ?? '',
            'phone' => $address['phone'] ?? '',
            'address' => $address['address'] ?? '',
            'city' => $address['city'] ?? '',
            'postal_code' => $address['postal_code'] ?? '',
            'notes' => $address['notes'] ?? ''
        ];
    }

    /**
     * Get order timeline for tracking.
     */
    public function getTimelineProperty()
    {
        $timeline = [];

        // Handle cancelled orders
        if ($this->order->status === 'cancelled') {
            // 1. Order placed
            $timeline[] = [
                'label' => 'Pesanan Dibuat',
                'completed' => true,
                'date' => $this->order->created_at,
                'icon' => 'shopping-cart'
            ];

            // 2. Payment status - show if payment exists and was paid
            if ($this->order->payment && $this->order->payment->status === 'paid') {
                $timeline[] = [
                    'label' => 'Pembayaran Berhasil',
                    'completed' => true,
                    'date' => $this->order->payment->paid_at,
                    'icon' => 'credit-card'
                ];
            }

            // 3. Cancelled status
            $timeline[] = [
                'label' => 'Dibatalkan',
                'completed' => true,
                'date' => $this->order->cancelled_at ?? $this->order->updated_at,
                'icon' => 'x-circle',
                'is_cancelled' => true
            ];

            return $timeline;
        }

        // Normal flow for non-cancelled orders
        // 1. Order placed
        $timeline[] = [
            'label' => 'Pesanan Dibuat',
            'completed' => true,
            'date' => $this->order->created_at,
            'icon' => 'shopping-cart'
        ];

        // 2. Waiting for payment (only if payment exists and not paid yet)
        if ($this->order->payment && $this->order->payment->status !== 'paid') {
            $timeline[] = [
                'label' => 'Menunggu Pembayaran',
                'completed' => false,
                'date' => null,
                'icon' => 'clock'
            ];
        }

        // 3. Payment completed
        if ($this->order->payment && $this->order->payment->status === 'paid') {
            $timeline[] = [
                'label' => 'Pembayaran Berhasil',
                'completed' => true,
                'date' => $this->order->payment->paid_at,
                'icon' => 'credit-card'
            ];
        }

        // 4. Waiting for confirmation (only if payment is completed but order not confirmed yet)
        if ($this->order->payment && $this->order->payment->status === 'paid' && $this->order->status === 'pending') {
            $timeline[] = [
                'label' => 'Menunggu Konfirmasi',
                'completed' => false,
                'date' => null,
                'icon' => 'clock'
            ];
        }

        // 5. Confirmed
        if (in_array($this->order->status, ['confirmed', 'processing', 'shipped', 'delivered'])) {
            $timeline[] = [
                'label' => 'Dikonfirmasi',
                'completed' => true,
                'date' => $this->order->confirmed_at,
                'icon' => 'check-circle'
            ];
        }

        // 6. Processing
        if (in_array($this->order->status, ['processing', 'ready_to_ship', 'ready_for_pickup', 'shipped', 'delivered', 'picked_up', 'completed'])) {
            $timeline[] = [
                'label' => 'Diproses',
                'completed' => true,
                'date' => $this->order->processing_at ?? $this->order->confirmed_at,
                'icon' => 'cog'
            ];
        }

        // 7. Ready to ship/pickup
        if (in_array($this->order->status, ['ready_to_ship', 'ready_for_pickup', 'shipped', 'delivered', 'picked_up', 'completed'])) {
            $timeline[] = [
                'label' => $this->order->shipping_type === 'delivery' ? 'Siap Diantar' : 'Siap Diambil',
                'completed' => true,
                'date' => $this->order->shipping_type === 'delivery' ? $this->order->ready_to_ship_at : $this->order->ready_for_pickup_at,
                'icon' => $this->order->shipping_type === 'delivery' ? 'package' : 'package-check'
            ];
        }

        // 8. Shipped
        if ($this->order->shipping_type === 'delivery' && in_array($this->order->status, ['shipped', 'delivered'])) {
            $timeline[] = [
                'label' => 'Dikirim',
                'completed' => true,
                'date' => $this->order->shipped_at,
                'icon' => 'truck'
            ];
        }

        // 9. Picked up (for pickup orders)
        if ($this->order->shipping_type === 'pickup' && in_array($this->order->status, ['picked_up', 'completed'])) {
            $timeline[] = [
                'label' => 'Diambil',
                'completed' => true,
                'date' => $this->order->picked_up_at,
                'icon' => 'check'
            ];
        }

        // 10. Delivered/Completed
        if ($this->order->status === 'delivered' || $this->order->status === 'completed') {
            $timeline[] = [
                'label' => $this->order->shipping_type === 'pickup' ? 'Selesai' : 'Diterima',
                'completed' => true,
                'date' => $this->order->shipping_type === 'pickup' ? $this->order->completed_at : $this->order->delivered_at,
                'icon' => 'check'
            ];
        }

        return $timeline;
    }

    /**
     * Delete order permanently from database.
     */
    public function deleteOrder($reason)
    {
        try {
            // Validate reason
            if (empty($reason) || strlen(trim($reason)) < 10) {
                session()->flash('error', 'Alasan penghapusan harus minimal 10 karakter.');
                return;
            }

            // Check if order can be deleted
            if (!$this->order->canBeDeleted()) {
                session()->flash('error', 'Pesanan dengan status ini tidak dapat dihapus.');
                return;
            }

            // Log the deletion activity
            \Log::info('Order deletion initiated', [
                'order_id' => $this->order->id,
                'order_number' => $this->order->order_number,
                'deleted_by' => auth()->id(),
                'reason' => $reason,
                'timestamp' => now()
            ]);

            // Delete the order
            $orderNumber = $this->order->order_number;
            $this->order->deleteOrder();

            // Flash success message
            session()->flash('message', "Pesanan {$orderNumber} berhasil dihapus dari sistem.");

            // Redirect to order management page
            return redirect()->route('admin.orders.index');

        } catch (\Exception $e) {
            \Log::error('Failed to delete order', [
                'order_id' => $this->order->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            session()->flash('error', 'Terjadi kesalahan saat menghapus pesanan. Silakan coba lagi.');
        }
    }

    /**
     * Refresh order data after status changes.
     */
    public function refreshOrder()
    {
        $this->loadOrder();
        // Dispatch order-updated event
        $this->dispatch('order-updated');
    }
    
    /**
     * Handle order updated event from OrderStatusActions component.
     */
    public function handleOrderUpdate()
    {
        $this->loadOrder();
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.admin.order-detail', [
            'shippingAddress' => $this->shippingAddress,
            'timeline' => $this->timeline
        ]);
    }
}