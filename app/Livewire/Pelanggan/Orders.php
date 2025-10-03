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
    
    // Refund modal properties
    public $showRefundModal = false;
    public $selectedRefundOrderId = null;
    public $refundReason = '';
    public $refundReasonOther = '';
    public $refundAmount = 0;

    protected $queryString = [
        'statusFilter' => ['except' => 'all'],
        'search' => ['except' => '']
    ];

    protected $rules = [
        'cancelReason' => 'required|string',
        'cancelReasonOther' => 'required_if:cancelReason,lainnya|string|min:3|max:500',
        'refundReason' => 'required|string',
        'refundReasonOther' => 'required_if:refundReason,lainnya|string|min:3|max:500'
    ];

    protected $messages = [
        'cancelReason.required' => 'Alasan pembatalan wajib dipilih.',
        'cancelReasonOther.required_if' => 'Alasan lainnya wajib diisi.',
        'cancelReasonOther.min' => 'Alasan lainnya minimal 3 karakter.',
        'cancelReasonOther.max' => 'Alasan lainnya maksimal 500 karakter.',
        'refundReason.required' => 'Alasan refund wajib dipilih.',
        'refundReasonOther.required_if' => 'Alasan lainnya wajib diisi.',
        'refundReasonOther.min' => 'Alasan lainnya minimal 3 karakter.',
        'refundReasonOther.max' => 'Alasan lainnya maksimal 500 karakter.'
    ];

    /**
     * Check if cancel button should be enabled
     * 
     * @return bool
     */
    public function getCanSubmitCancelProperty()
    {
        if (empty($this->cancelReason)) {
            return false;
        }
        
        if ($this->cancelReason === 'lainnya') {
            return !empty($this->cancelReasonOther) && strlen($this->cancelReasonOther) >= 3;
        }
        
        return true;
    }

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
            if ($this->statusFilter === 'expired') {
                // Filter for expired orders (waiting_payment with expired payment)
                $query->where('status', 'waiting_payment')
                      ->whereHas('payment', function ($paymentQuery) {
                          $paymentQuery->where('status', 'pending')
                                       ->where('created_at', '<', now()->subHours(24));
                      });
            } else {
                $query->where('status', $this->statusFilter);
            }
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
            'failed' => 'Gagal Diantar',
            'expired' => 'Pesanan Expired'
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
            
            // Refresh orders data to show updated status
            $this->dispatch('$refresh');
            
            // Dispatch notification and auto-refresh page
            $this->dispatch('show-notification', [
                'type' => 'success',
                'message' => 'Pesanan berhasil dibatalkan!'
            ]);
            
            // Auto-refresh page after 1 second
            $this->dispatch('auto-refresh-page');
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

        // Update to delivered status first
        $order->update([
            'status' => 'delivered',
            'delivered_at' => now()
        ]);

        // Update delivery record if exists
        if ($order->delivery) {
            $order->delivery->update([
                'status' => 'delivered',
                'delivered_at' => now(),
                'delivery_notes' => 'Dikonfirmasi diterima oleh pelanggan'
            ]);
        }

        // Automatically mark as completed for delivery orders
        $order->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        session()->flash('success', 'Terima kasih! Pesanan telah dikonfirmasi selesai.');
    }

    /**
     * Open refund request modal
     */
    public function openRefundModal($orderId)
    {
        $order = Order::where('order_id', $orderId)
            ->where('user_id', Auth::id())
            ->with(['payment', 'refunds'])
            ->first();

        if (!$order) {
            session()->flash('error', 'Pesanan tidak ditemukan!');
            return;
        }

        // Check if order can be refunded
        if (!$order->canBeRefunded()) {
            session()->flash('error', 'Pesanan tidak dapat di-refund!');
            return;
        }

        // Check if refund already exists
        if ($order->refunds()->where('status', '!=', 'rejected')->exists()) {
            session()->flash('error', 'Permintaan refund sudah ada untuk pesanan ini!');
            return;
        }

        $this->selectedRefundOrderId = $orderId;
        $this->refundAmount = $order->total_amount;
        $this->refundReason = '';
        $this->refundReasonOther = '';
        $this->showRefundModal = true;
    }

    /**
     * Submit refund request
     */
    public function submitRefundRequest()
    {
        // Validate refund data
        $this->validate([
            'refundReason' => 'required|string',
            'refundReasonOther' => 'required_if:refundReason,lainnya|string|min:3|max:500'
        ]);

        $order = Order::where('order_id', $this->selectedRefundOrderId)
            ->where('user_id', Auth::id())
            ->with(['payment', 'refunds'])
            ->first();

        if (!$order) {
            session()->flash('error', 'Pesanan tidak ditemukan!');
            $this->closeRefundModal();
            return;
        }

        if (!$order->canBeRefunded()) {
            session()->flash('error', 'Pesanan tidak dapat di-refund!');
            $this->closeRefundModal();
            return;
        }

        // Check if refund already exists
        if ($order->refunds()->where('status', '!=', 'rejected')->exists()) {
            session()->flash('error', 'Permintaan refund sudah ada untuk pesanan ini!');
            $this->closeRefundModal();
            return;
        }

        // Prepare refund reason
        $finalRefundReason = $this->refundReason;
        if ($this->refundReason === 'lainnya') {
            $finalRefundReason = $this->refundReasonOther;
        } else {
            // Convert reason code to readable text
            $reasonLabels = [
                'produk_rusak' => 'Produk rusak atau cacat',
                'salah_produk' => 'Produk yang diterima salah',
                'tidak_sesuai' => 'Produk tidak sesuai deskripsi',
                'kadaluarsa' => 'Produk sudah kadaluarsa',
                'tidak_puas' => 'Tidak puas dengan produk',
                'duplikasi_pesanan' => 'Pesanan duplikasi'
            ];
            $finalRefundReason = $reasonLabels[$this->refundReason] ?? $this->refundReason;
        }

        try {
            // Create refund request
            $refund = $order->refunds()->create([
                'refund_amount' => $this->refundAmount,
                'reason' => $finalRefundReason,
                'status' => 'pending',
                'requested_by' => Auth::id(),
                'requested_at' => now()
            ]);

            session()->flash('success', 'Permintaan refund berhasil diajukan! Kami akan memproses dalam 1-3 hari kerja.');
            $this->closeRefundModal();

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengajukan refund. Silakan coba lagi.');
            $this->closeRefundModal();
        }
    }

    /**
     * Close refund modal
     */
    public function closeRefundModal()
    {
        $this->showRefundModal = false;
        $this->selectedRefundOrderId = null;
        $this->refundReason = '';
        $this->refundReasonOther = '';
        $this->refundAmount = 0;
        $this->resetErrorBag();
    }

    /**
     * Redirect to dashboard for buy again functionality
     */
    public function buyAgain()
    {
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.pelanggan.orders', [
            'orders' => $this->orders,
            'statusOptions' => $this->statusOptions
        ]);
    }
}