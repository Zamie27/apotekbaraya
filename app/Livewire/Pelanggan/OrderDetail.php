<?php

namespace App\Livewire\Pelanggan;

use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.user')]

class OrderDetail extends Component
{
    public $orderId;
    public $order;

    public function mount($orderId)
    {
        $this->orderId = $orderId;
        $this->loadOrder();
    }

    /**
     * Load order with all related data
     */
    public function loadOrder()
    {
        $this->order = Order::where('order_id', $this->orderId)
            ->where('user_id', Auth::id())
            ->with([
                'items.product',
                'payment',
                'delivery.courier',
                'user'
            ])
            ->first();

        if (!$this->order) {
            abort(404, 'Pesanan tidak ditemukan');
        }
    }

    /**
     * Cancel order (if allowed)
     */
    public function cancelOrder()
    {
        if (!$this->order->canBeCancelled()) {
            session()->flash('error', 'Pesanan tidak dapat dibatalkan!');
            return;
        }

        $this->order->update(['status' => 'cancelled']);
        $this->loadOrder(); // Refresh order data
        session()->flash('success', 'Pesanan berhasil dibatalkan!');
    }

    /**
     * Confirm order delivery
     */
    public function confirmDelivery()
    {
        if ($this->order->status !== 'shipped') {
            session()->flash('error', 'Pesanan belum dalam status dikirim!');
            return;
        }

        $this->order->update([
            'status' => 'delivered',
            'delivered_at' => now()
        ]);

        // Update delivery record if exists
        if ($this->order->delivery) {
            $this->order->delivery->update([
                'delivered_at' => now(),
                'delivery_notes' => 'Dikonfirmasi diterima oleh pelanggan'
            ]);
        }

        $this->loadOrder(); // Refresh order data
        session()->flash('success', 'Terima kasih! Pesanan telah dikonfirmasi selesai.');
    }

    /**
     * Get order timeline for tracking
     */
    public function getOrderTimelineProperty()
    {
        $timeline = [];

        // Order created
        $timeline[] = [
            'status' => 'created',
            'label' => 'Pesanan Dibuat',
            'date' => $this->order->created_at,
            'completed' => true,
            'icon' => 'shopping-cart'
        ];

        // Order confirmed
        if ($this->order->confirmed_at) {
            $timeline[] = [
                'status' => 'confirmed',
                'label' => 'Pesanan Dikonfirmasi',
                'date' => $this->order->confirmed_at,
                'completed' => true,
                'icon' => 'check-circle'
            ];
        } else {
            $timeline[] = [
                'status' => 'confirmed',
                'label' => 'Menunggu Konfirmasi',
                'date' => null,
                'completed' => false,
                'icon' => 'clock'
            ];
        }

        // Processing/Shipped
        if (in_array($this->order->status, ['processing', 'shipped', 'delivered'])) {
            $timeline[] = [
                'status' => 'processing',
                'label' => 'Pesanan Diproses',
                'date' => $this->order->confirmed_at,
                'completed' => true,
                'icon' => 'cog'
            ];
        }

        // Shipped
        if ($this->order->shipped_at) {
            $timeline[] = [
                'status' => 'shipped',
                'label' => $this->order->shipping_type === 'delivery' ? 'Pesanan Dikirim' : 'Siap Diambil',
                'date' => $this->order->shipped_at,
                'completed' => true,
                'icon' => $this->order->shipping_type === 'delivery' ? 'truck' : 'package'
            ];
        } else if (in_array($this->order->status, ['shipped', 'delivered'])) {
            $timeline[] = [
                'status' => 'shipped',
                'label' => $this->order->shipping_type === 'delivery' ? 'Menunggu Pengiriman' : 'Menunggu Pickup',
                'date' => null,
                'completed' => false,
                'icon' => $this->order->shipping_type === 'delivery' ? 'truck' : 'package'
            ];
        }

        // Delivered
        if ($this->order->delivered_at) {
            $timeline[] = [
                'status' => 'delivered',
                'label' => $this->order->shipping_type === 'delivery' ? 'Pesanan Diterima' : 'Pesanan Diambil',
                'date' => $this->order->delivered_at,
                'completed' => true,
                'icon' => 'check-circle'
            ];
        } else if ($this->order->status === 'delivered') {
            $timeline[] = [
                'status' => 'delivered',
                'label' => $this->order->shipping_type === 'delivery' ? 'Menunggu Konfirmasi Penerimaan' : 'Menunggu Pickup',
                'date' => null,
                'completed' => false,
                'icon' => 'check-circle'
            ];
        }

        return $timeline;
    }

    /**
     * Get shipping address formatted
     */
    public function getShippingAddressProperty()
    {
        if ($this->order->shipping_type === 'pickup') {
            return 'Ambil di Toko';
        }

        return $this->order->shipping_address;
    }

    public function render()
    {
        return view('livewire.pelanggan.order-detail', [
            'timeline' => $this->orderTimeline,
            'shippingAddress' => $this->shippingAddress
        ]);
    }
}