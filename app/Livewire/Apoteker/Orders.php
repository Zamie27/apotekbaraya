<?php

namespace App\Livewire\Apoteker;

use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

#[Layout('components.layouts.apoteker')]
class Orders extends Component
{
    use WithPagination, WithFileUploads;

    public $statusFilter = 'all';
    public $search = '';

    protected $queryString = [
        'statusFilter' => ['except' => 'all'],
        'search' => ['except' => '']
    ];

    protected $listeners = [
        'orderStatusUpdated' => 'refreshOrders'
    ];





    /**
     * Reset pagination when filters change
     */
    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when search changes
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Get orders with filters and pagination
     */
    public function getOrdersProperty()
    {
        $query = Order::with([
            'items.product',
            'payment',
            'user',
            'delivery.courier'
        ])
        ->when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('order_number', 'like', '%' . $this->search . '%')
                  ->orWhere('order_id', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function ($userQuery) {
                      $userQuery->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('email', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('items.product', function ($productQuery) {
                      $productQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        })
        ->when($this->statusFilter !== 'all', function ($query) {
            $query->where('status', $this->statusFilter);
        })
        ->orderBy('created_at', 'desc');

        return $query->paginate(10);
    }

    /**
     * Get status options for filter
     */
    public function getStatusOptionsProperty()
    {
        return [
            'all' => 'Semua Status',
            'pending' => 'Pesanan Dibuat',
            'waiting_payment' => 'Menunggu Pembayaran',
            'waiting_confirmation' => 'Menunggu Konfirmasi',
            'confirmed' => 'Dikonfirmasi',
            'processing' => 'Diproses',
            'ready_to_ship' => 'Siap Diantar',
            'ready_for_pickup' => 'Siap Diambil',
            'shipped' => 'Dikirim',
            'delivered' => 'Diterima',
            'picked_up' => 'Diambil',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan'
        ];
    }

    /**
     * Refresh orders when status updated
     */
    public function refreshOrders()
    {
        $this->resetPage();
    }

    /**
     * Get order status badge color
     */
    public function getStatusBadgeColor($status)
    {
        return match($status) {
            'pending' => 'badge-warning',
            'waiting_payment' => 'badge-warning',
            'waiting_confirmation' => 'badge-warning',
            'confirmed' => 'badge-info',
            'processing' => 'badge-primary',
            'ready_to_ship' => 'badge-accent',
            'ready_for_pickup' => 'badge-accent',
            'shipped' => 'badge-secondary',
            'delivered' => 'badge-success',
            'picked_up' => 'badge-success',
            'completed' => 'badge-success',
            'cancelled' => 'badge-error',
            default => 'badge-neutral'
        };
    }

    /**
     * Get order status label
     */
    public function getStatusLabel($status)
    {
        return match($status) {
            'pending' => 'Pesanan Dibuat',
            'waiting_payment' => 'Menunggu Pembayaran',
            'waiting_confirmation' => 'Menunggu Konfirmasi',
            'confirmed' => 'Dikonfirmasi',
            'processing' => 'Diproses',
            'ready_to_ship' => 'Siap Diantar',
            'ready_for_pickup' => 'Siap Diambil',
            'shipped' => 'Dikirim',
            'delivered' => 'Diterima',
            'picked_up' => 'Diambil',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            'failed' => 'Gagal Diantar',
            default => ucfirst($status)
        };
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.apoteker.orders', [
            'orders' => $this->orders,
            'statusOptions' => $this->statusOptions
        ]);
    }
}