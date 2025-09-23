<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;

#[Layout('components.layouts.admin')]
#[Title('Manajemen Pesanan')]
class OrderManagement extends Component
{
    use WithPagination;

    #[Url(as: 'search')]
    public $search = '';

    #[Url(as: 'status')]
    public $statusFilter = 'all';

    #[Url(as: 'sort')]
    public $sortBy = 'created_at';

    #[Url(as: 'direction')]
    public $sortDirection = 'desc';

    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    /**
     * Reset pagination when search or filter changes.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    /**
     * Sort orders by specified column.
     */
    public function sortBy($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Update order status quickly.
     */
    public function updateOrderStatus($orderId, $status)
    {
        try {
            $order = Order::findOrFail($orderId);
            $order->update(['status' => $status]);

            $this->dispatch('toast', [
                'type' => 'success',
                'message' => 'Status pesanan berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Gagal memperbarui status pesanan!'
            ]);
        }
    }

    /**
     * Get orders with filters and pagination.
     */
    public function getOrdersProperty()
    {
        $query = Order::with(['user', 'items', 'payment', 'delivery.courier'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('order_number', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', function ($userQuery) {
                          $userQuery->where('name', 'like', '%' . $this->search . '%')
                                   ->orWhere('email', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    /**
     * Get order statistics.
     */
    public function getOrderStatsProperty()
    {
        return [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClass($status)
    {
        return match($status) {
            'pending' => 'badge-warning',
            'processing' => 'badge-info',
            'shipped' => 'badge-primary',
            'delivered' => 'badge-success',
            'cancelled' => 'badge-error',
            default => 'badge-neutral'
        };
    }

    /**
     * Get status label in Indonesian.
     */
    public function getStatusLabel($status, $order = null)
    {
        // Check if payment is expired for waiting_payment status
        if ($status === 'waiting_payment' && $order && $order->isPaymentExpired()) {
            return 'Pesanan Expired';
        }

        return match($status) {
            'pending' => 'Menunggu',
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
            default => ucfirst($status)
        };
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.admin.order-management', [
            'orders' => $this->orders,
            'orderStats' => $this->orderStats,
        ]);
    }
}