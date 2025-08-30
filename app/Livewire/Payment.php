<?php

namespace App\Livewire;

use App\Models\Order;
use App\Services\MidtransService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Payment extends Component
{
    public $order;
    public $paymentUrl;
    public $paymentStatus = 'pending';
    
    /**
     * Mount the payment component
     */
    public function mount()
    {
        // Get order from session or redirect
        $orderId = session('order_id');
        $this->paymentUrl = session('payment_url');
        
        if (!$orderId || !$this->paymentUrl) {
            session()->flash('error', 'Data pembayaran tidak ditemukan.');
            return redirect()->route('dashboard');
        }
        
        // Load order data
        $this->order = Order::with(['items.product'])
            ->where('order_id', $orderId)
            ->where('user_id', Auth::id())
            ->first();
            
        if (!$this->order) {
            session()->flash('error', 'Pesanan tidak ditemukan.');
            return redirect()->route('dashboard');
        }
        
        Log::info('Payment page loaded', [
            'order_id' => $this->order->order_id,
            'order_number' => $this->order->order_number,
            'payment_url' => $this->paymentUrl
        ]);
        
        // Redirect to Midtrans Payment Link
        return redirect()->away($this->paymentUrl);
    }
    

    

    
    /**
     * Render the payment page
     */
    public function render()
    {
        return view('livewire.payment')
            ->layout('components.layouts.user');
    }


}