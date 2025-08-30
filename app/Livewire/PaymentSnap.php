<?php

namespace App\Livewire;

use App\Models\Order;
use App\Services\MidtransService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentSnap extends Component
{
    public $order;
    public $snapToken;
    public $clientKey;
    public $snapJsUrl;
    public $paymentStatus = 'pending';
    
    /**
     * Mount the payment SNAP component
     */
    public function mount()
    {
        // Get order and snap token from session
        $orderId = session('order_id');
        $this->snapToken = session('snap_token');
        
        if (!$orderId || !$this->snapToken) {
            session()->flash('error', 'Data pembayaran tidak ditemukan.');
            return redirect()->route('dashboard');
        }
        
        // Load order data
        $this->order = Order::with(['items.product', 'payment'])
            ->where('order_id', $orderId)
            ->where('user_id', Auth::id())
            ->first();
            
        if (!$this->order) {
            session()->flash('error', 'Pesanan tidak ditemukan.');
            return redirect()->route('dashboard');
        }
        
        // Check if payment is still pending
        if (!$this->order->payment || $this->order->payment->status !== 'pending') {
            session()->flash('error', 'Pembayaran tidak dapat dilanjutkan.');
            return redirect()->route('pelanggan.orders.show', $this->order->order_id);
        }
        
        // Check if payment is expired
        if ($this->order->isPaymentExpired()) {
            session()->flash('error', 'Pembayaran telah kedaluwarsa. Silakan buat pesanan baru.');
            return redirect()->route('pelanggan.orders.show', $this->order->order_id);
        }
        
        // Get Midtrans configuration
        $midtransService = app(MidtransService::class);
        $this->clientKey = $midtransService->getClientKey();
        $this->snapJsUrl = $midtransService->getSnapJsUrl();
        
        Log::info('Payment SNAP page loaded', [
            'order_id' => $this->order->order_id,
            'order_number' => $this->order->order_number,
            'snap_token' => $this->snapToken
        ]);
    }
    
    /**
     * Handle payment success callback from frontend
     */
    public function handlePaymentSuccess($result)
    {
        Log::info('Payment success callback received', [
            'order_id' => $this->order->order_id,
            'result' => $result
        ]);
        
        // Redirect to order detail with success message
        session()->flash('success', 'Pembayaran berhasil! Pesanan Anda sedang diproses.');
        return redirect()->route('pelanggan.orders.show', $this->order->order_id);
    }
    
    /**
     * Handle payment pending callback from frontend
     */
    public function handlePaymentPending($result)
    {
        Log::info('Payment pending callback received', [
            'order_id' => $this->order->order_id,
            'result' => $result
        ]);
        
        // Redirect to order detail with info message
        session()->flash('info', 'Pembayaran sedang diproses. Silakan tunggu konfirmasi.');
        return redirect()->route('pelanggan.orders.show', $this->order->order_id);
    }
    
    /**
     * Handle payment error callback from frontend
     */
    public function handlePaymentError($result)
    {
        Log::info('Payment error callback received', [
            'order_id' => $this->order->order_id,
            'result' => $result
        ]);
        
        // Redirect to order detail with error message
        session()->flash('error', 'Pembayaran gagal atau dibatalkan.');
        return redirect()->route('pelanggan.orders.show', $this->order->order_id);
    }
    
    /**
     * Handle payment close callback from frontend
     */
    public function handlePaymentClose()
    {
        Log::info('Payment popup closed', [
            'order_id' => $this->order->order_id
        ]);
        
        // Redirect back to order detail
        return redirect()->route('pelanggan.orders.show', $this->order->order_id);
    }
    
    /**
     * Render the payment SNAP page
     */
    public function render()
    {
        return view('livewire.payment-snap')
            ->layout('components.layouts.user');
    }
}