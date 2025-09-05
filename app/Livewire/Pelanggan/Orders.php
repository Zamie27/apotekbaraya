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
    public $showCancelModal = false;
    public $selectedOrderId = null;
    public $cancelReason = '';
    public $cancelReasonOther = '';

    protected $queryString = [
        'statusFilter' => ['except' => 'all'],
        'search' => ['except' => '']
    ];

    protected $rules = [
        'cancelReason' => 'required|string',
        'cancelReasonOther' => 'required_if:cancelReason,lainnya|string|min:3|max:500'
    ];

    protected $messages = [
        'cancelReason.required' => 'Alasan pembatalan wajib dipilih.',
        'cancelReasonOther.required_if' => 'Alasan lainnya wajib diisi.',
        'cancelReasonOther.min' => 'Alasan lainnya minimal 3 karakter.',
        'cancelReasonOther.max' => 'Alasan lainnya maksimal 500 karakter.'
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
            ->with(['items.product', 'payment', 'failedByCourier'])
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
            'pending' => 'Pesanan Dibuat',
            'waiting_payment' => 'Menunggu Pembayaran',
            'waiting_confirmation' => 'Menunggu Konfirmasi',
            'confirmed' => 'Dikonfirmasi',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'delivered' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            'failed' => 'Gagal Diantar'
        ];
    }

    /**
     * Open cancel order modal
     */
    public function openCancelModal($orderId)
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

        $this->selectedOrderId = $orderId;
        $this->cancelReason = '';
        $this->showCancelModal = true;
    }

    /**
     * Cancel order with reason
     */
    public function cancelOrder()
    {
        $this->validate();

        $order = Order::where('order_id', $this->selectedOrderId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$order) {
            session()->flash('error', 'Pesanan tidak ditemukan!');
            $this->closeCancelModal();
            return;
        }

        if (!$order->canBeCancelled()) {
            session()->flash('error', 'Pesanan tidak dapat dibatalkan!');
            $this->closeCancelModal();
            return;
        }

        // Prepare cancel reason based on selection
        $finalCancelReason = $this->cancelReason;
        if ($this->cancelReason === 'lainnya') {
            $finalCancelReason = $this->cancelReasonOther;
        } else {
            // Convert reason code to readable text
            $reasonLabels = [
                'salah_pesan' => 'Salah membuat pesanan',
                'ganti_barang' => 'Ingin mengganti barang',
                'ganti_alamat' => 'Ingin mengganti alamat pengiriman',
                'tidak_jadi' => 'Tidak jadi membeli',
                'masalah_pembayaran' => 'Masalah dengan pembayaran'
            ];
            $finalCancelReason = $reasonLabels[$this->cancelReason] ?? $this->cancelReason;
        }

        // Use the model's cancelOrder method for consistency
        $success = $order->cancelOrder($finalCancelReason, Auth::id());

        if ($success) {
            session()->flash('success', 'Pesanan berhasil dibatalkan!');
            $this->closeCancelModal();
        } else {
            session()->flash('error', 'Gagal membatalkan pesanan. Silakan coba lagi.');
            $this->closeCancelModal();
        }
    }

    /**
     * Close cancel modal
     */
    public function closeCancelModal()
    {
        $this->showCancelModal = false;
        $this->selectedOrderId = null;
        $this->cancelReason = '';
        $this->cancelReasonOther = '';
        $this->resetErrorBag();
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