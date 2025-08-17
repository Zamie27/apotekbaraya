<?php

namespace App\Livewire\Pelanggan;

use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.user')]

class Orders extends Component
{
    use WithPagination;

    public $statusFilter = 'all';
    public $search = '';

    protected $queryString = [
        'statusFilter' => ['except' => 'all'],
        'search' => ['except' => '']
    ];

    /**
     * Reset pagination when filters change
     */
    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Get orders based on filters
     */
    public function getOrdersProperty()
    {
        $query = Order::where('user_id', Auth::id())
            ->with(['items.product', 'payment'])
            ->orderBy('created_at', 'desc');

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('order_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('items.product', function ($productQuery) {
                      $productQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        return $query->paginate(10);
    }

    /**
     * Get available status options
     */
    public function getStatusOptionsProperty()
    {
        return [
            'all' => 'Semua Status',
            'pending' => 'Menunggu Konfirmasi',
            'confirmed' => 'Dikonfirmasi',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'delivered' => 'Selesai',
            'cancelled' => 'Dibatalkan'
        ];
    }

    /**
     * Cancel order (if allowed)
     */
    public function cancelOrder($orderId)
    {
        $order = Order::where('order_id', $orderId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$order) {
            session()->flash('error', 'Pesanan tidak ditemukan!');
            return;
        }

        if (!$order->canBeCancelled()) {
            session()->flash('error', 'Pesanan tidak dapat dibatalkan!');
            return;
        }

        $order->update(['status' => 'cancelled']);
        session()->flash('success', 'Pesanan berhasil dibatalkan!');
    }

    /**
     * Confirm order delivery
     */
    public function confirmDelivery($orderId)
    {
        $order = Order::where('order_id', $orderId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$order) {
            session()->flash('error', 'Pesanan tidak ditemukan!');
            return;
        }

        if ($order->status !== 'shipped') {
            session()->flash('error', 'Pesanan belum dalam status dikirim!');
            return;
        }

        $order->update([
            'status' => 'delivered',
            'delivered_at' => now()
        ]);

        session()->flash('success', 'Terima kasih! Pesanan telah dikonfirmasi selesai.');
    }

    public function render()
    {
        return view('livewire.pelanggan.orders', [
            'orders' => $this->orders,
            'statusOptions' => $this->statusOptions
        ]);
    }
}