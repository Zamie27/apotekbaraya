<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class RefundManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    // Modal properties
    public $selectedOrder = null;
    public $showRefundModal = false;
    public $refundNotes = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    /**
     * Reset pagination when search changes
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when filter changes
     */
    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    /**
     * Sort orders by specified column
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
     * Open refund modal for specific order
     */
    public function openRefundModal($orderId)
    {
        $this->selectedOrder = Order::with(['payment', 'user', 'orderItems.product'])
            ->findOrFail($orderId);
        $this->showRefundModal = true;
        $this->refundNotes = '';
    }

    /**
     * Close refund modal
     */
    public function closeRefundModal()
    {
        $this->showRefundModal = false;
        $this->selectedOrder = null;
        $this->refundNotes = '';
    }

    /**
     * Mark refund as completed
     */
    public function markRefundCompleted()
    {
        if (!$this->selectedOrder) {
            return;
        }

        try {
            $this->selectedOrder->markRefundCompleted();
            
            // Log the refund completion
            Log::info('Refund marked as completed by admin', [
                'order_id' => $this->selectedOrder->order_id,
                'order_number' => $this->selectedOrder->order_number,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'notes' => $this->refundNotes,
                'refund_amount' => $this->selectedOrder->total_price,
                'timestamp' => now(),
            ]);

            session()->flash('success', 'Refund berhasil ditandai sebagai selesai untuk pesanan ' . $this->selectedOrder->order_number);
            
            $this->closeRefundModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat memproses refund: ' . $e->getMessage());
        }
    }

    /**
     * Get orders that need refund processing
     */
    public function getOrdersProperty()
    {
        $query = Order::with(['payment', 'user', 'orderItems'])
            ->where('status', 'cancelled')
            ->whereHas('payment', function ($q) {
                // Include orders that were paid (and now cancelled) or still paid
                $q->whereIn('status', ['paid', 'cancelled']);
            });

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('order_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function ($userQuery) {
                      $userQuery->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('email', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            if ($this->statusFilter === 'pending') {
                $query->where(function ($q) {
                    $q->where('refund_status', 'pending')
                      ->orWhereNull('refund_status');
                });
            } else {
                $query->where('refund_status', $this->statusFilter);
            }
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    /**
     * Get refund statistics
     */
    public function getRefundStatsProperty()
    {
        $baseQuery = Order::where('status', 'cancelled')
            ->whereHas('payment', function ($q) {
                // Include orders that were paid (and now cancelled) or still paid
                $q->whereIn('status', ['paid', 'cancelled']);
            });

        return [
            'total_pending' => (clone $baseQuery)->where(function ($q) {
                $q->where('refund_status', 'pending')
                  ->orWhereNull('refund_status');
            })->count(),
            'total_completed' => (clone $baseQuery)->where('refund_status', 'completed')->count(),
            'total_amount_pending' => (clone $baseQuery)->where(function ($q) {
                $q->where('refund_status', 'pending')
                  ->orWhereNull('refund_status');
            })->sum('total_price'),
            'total_amount_completed' => (clone $baseQuery)->where('refund_status', 'completed')->sum('total_price'),
        ];
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.admin.refund-management', [
            'orders' => $this->orders,
            'refundStats' => $this->refundStats,
        ]);
    }
}