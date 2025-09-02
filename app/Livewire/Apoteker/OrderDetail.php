<?php

namespace App\Livewire\Apoteker;

use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.apoteker')]
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
     * Load order with all related data.
     */
    public function loadOrder()
    {
        $this->order = Order::where('order_id', $this->orderId)
            ->with([
                'items.product',
                'payment',
                'delivery.courier',
                'user',
                'cancelledBy'
            ])
            ->first();

        if (!$this->order) {
            abort(404, 'Pesanan tidak ditemukan');
        }
    }

    /**
     * Get shipping address for display.
     */
    public function getShippingAddressProperty()
    {
        if ($this->order->shipping_type === 'pickup') {
            return 'Ambil di apotek/toko';
        }

        // Handle array shipping_address
        if (is_array($this->order->shipping_address)) {
            $address = $this->order->shipping_address;
            $parts = [];
            
            // Use correct field names from CheckoutService
            if (!empty($address['detailed_address'])) {
                $parts[] = $address['detailed_address'];
            }
            if (!empty($address['village'])) {
                $parts[] = $address['village'];
            }
            if (!empty($address['sub_district'])) {
                $parts[] = $address['sub_district'];
            }
            if (!empty($address['regency'])) {
                $parts[] = $address['regency'];
            }
            if (!empty($address['province'])) {
                $parts[] = $address['province'];
            }
            if (!empty($address['postal_code'])) {
                $parts[] = $address['postal_code'];
            }
            
            return !empty($parts) ? implode(', ', $parts) : 'Alamat tidak lengkap';
        }

        return $this->order->shipping_address ?? 'Alamat tidak tersedia';
    }

    /**
     * Get timeline steps for order status.
     */
    public function getTimelineProperty()
    {
        $timeline = [];

        // Handle cancelled orders first
        if ($this->order->status === 'cancelled') {
            // 1. Order created - always show
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
     * Refresh order data after status changes.
     */
    public function refreshOrder()
    {
        $this->loadOrder();
        // Only dispatch order-updated event, no notification here
        // Notification will be handled by the component that triggered the action
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
        return view('livewire.apoteker.order-detail', [
            'shippingAddress' => $this->shippingAddress,
            'timeline' => $this->timeline
        ]);
    }
}