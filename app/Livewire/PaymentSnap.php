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
    public $shouldRedirect = false;
    public $redirectUrl = '';
    
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
        
        // Check payment status from Midtrans to confirm the payment
        $this->checkPaymentStatus();
        
        // Set redirect flag and URL for JavaScript to handle
        session()->flash('success', 'Pembayaran berhasil! Pesanan Anda sedang diproses.');
        $this->shouldRedirect = true;
        $this->redirectUrl = route('pelanggan.orders.show', [$this->order->order_id, 'from_payment' => 1]);
        
        // Dispatch browser event for redirect
        $this->dispatch('payment-redirect', ['url' => $this->redirectUrl]);
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
        
        // Check payment status from Midtrans to get latest status
        $this->checkPaymentStatus();
        
        // Set redirect flag and URL for JavaScript to handle
        session()->flash('info', 'Pembayaran sedang diproses. Silakan tunggu konfirmasi.');
        $this->shouldRedirect = true;
        $this->redirectUrl = route('pelanggan.orders.show', [$this->order->order_id, 'from_payment' => 1]);
        
        // Dispatch browser event for redirect
        $this->dispatch('payment-redirect', ['url' => $this->redirectUrl]);
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
        
        // Check payment status from Midtrans to confirm the error
        $this->checkPaymentStatus();
        
        // Set redirect flag and URL for JavaScript to handle
        session()->flash('error', 'Pembayaran gagal atau dibatalkan.');
        $this->shouldRedirect = true;
        $this->redirectUrl = route('pelanggan.orders.show', [$this->order->order_id, 'from_payment' => 1]);
        
        // Dispatch browser event for redirect
        $this->dispatch('payment-redirect', ['url' => $this->redirectUrl]);
    }
    
    /**
     * Handle payment close callback from frontend
     */
    public function handlePaymentClose()
    {
        Log::info('Payment popup closed', [
            'order_id' => $this->order->order_id
        ]);
        
        // Set redirect flag and URL for JavaScript to handle
        $this->shouldRedirect = true;
        $this->redirectUrl = route('pelanggan.orders.show', $this->order->order_id);
        
        // Dispatch browser event for redirect
        $this->dispatch('payment-redirect', ['url' => $this->redirectUrl]);
    }
    
    /**
     * Check payment status from Midtrans and update order if needed
     */
    public function checkPaymentStatus()
    {
        try {
            $midtransService = app(MidtransService::class);
            $statusResult = $midtransService->getTransactionStatus($this->order->order_number);
            
            if ($statusResult['success']) {
                $transactionData = $statusResult['data'];
                $transactionStatus = $transactionData['transaction_status'] ?? null;
                
                Log::info('Payment status checked from PaymentSnap', [
                    'order_id' => $this->order->order_id,
                    'order_number' => $this->order->order_number,
                    'transaction_status' => $transactionStatus
                ]);
                
                // Update payment and order status based on Midtrans response
                if ($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
                    // Payment successful - update payment and order status
                    $this->order->payment->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                        'transaction_id' => $transactionData['transaction_id'] ?? null,
                        'payment_type' => $transactionData['payment_type'] ?? null,
                    ]);
                   
                    $this->order->update([
                        'status' => 'waiting_confirmation',
                        'waiting_confirmation_at' => now()
                    ]);
                    
                    Log::info('Payment status updated successfully', [
                        'order_id' => $this->order->order_id,
                        'new_status' => 'waiting_confirmation'
                    ]);
                }
            } else {
                Log::warning('Failed to check payment status from PaymentSnap', [
                    'order_id' => $this->order->order_id,
                    'error' => $statusResult['message'] ?? 'Unknown error'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error checking payment status from PaymentSnap', [
                'order_id' => $this->order->order_id,
                'error' => $e->getMessage()
            ]);
        }
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