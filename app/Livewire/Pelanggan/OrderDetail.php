<?php

namespace App\Livewire\Pelanggan;

use App\Models\Order;
use App\Services\MidtransService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

#[Layout('components.layouts.user')]

class OrderDetail extends Component
{
    use WithFileUploads;
    public $orderId;
    public $order;
    public $showCancelModal = false;
    public $selectedOrderId;
    public $cancelReason = '';
    public $cancelReasonOther = '';
    
    // Delivery proof modal properties
    public $showDeliveryProofModal = false;
    public $deliveryProofImage = null;
    
    // Note: Properti konfirmasi pesanan dihapus karena pelanggan tidak boleh mengkonfirmasi pesanan

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
        // Force fresh query from database, bypass any caching
        $this->order = Order::where('order_id', $this->orderId)
            ->where('user_id', Auth::id())
            ->with([
                'items.product',
                'payment',
                'delivery.courier',
                'user',
                'failedByCourier'
            ])
            ->first();

        if (!$this->order) {
            abort(404, 'Pesanan tidak ditemukan');
        }
        
        // Force refresh the model to ensure fresh attributes
        $this->order->refresh();
    }
    
    /**
     * Force refresh component data.
     */
    public function refreshComponent()
    {
        $this->order = null;
        $this->loadOrder();
        $this->dispatch('$refresh');
    }

    /**
     * Open cancel modal for specific order
     * 
     * @return void
     */
    public function openCancelModal()
    {
        // Check if order exists and can be cancelled
        if (!$this->order || !$this->order->canBeCancelled()) {
            session()->flash('error', 'Pesanan tidak dapat dibatalkan.');
            return;
        }

        $this->selectedOrderId = $this->order->id;
        $this->showCancelModal = true;
        $this->reset(['cancelReason', 'cancelReasonOther']);
    }

    /**
     * Process order cancellation
     * 
     * @return void
     */
    public function cancelOrder()
    {
        // Validate cancel reason
        $this->validate([
            'cancelReason' => 'required|string',
            'cancelReasonOther' => 'required_if:cancelReason,lainnya|string|min:3|max:255'
        ], [
            'cancelReason.required' => 'Alasan pembatalan harus dipilih.',
            'cancelReasonOther.required_if' => 'Alasan lainnya harus diisi.',
            'cancelReasonOther.min' => 'Alasan lainnya minimal 3 karakter.',
            'cancelReasonOther.max' => 'Alasan lainnya maksimal 255 karakter.'
        ]);

        try {
            // Prepare cancel reason text
            $cancelReasonText = $this->cancelReason === 'lainnya' 
                ? $this->cancelReasonOther 
                : $this->getCancelReasonText($this->cancelReason);

            // Cancel the order
            $this->order->cancelOrder($cancelReasonText, auth()->id());

            // Log the cancellation
            Log::info('Order cancelled by customer', [
                'order_id' => $this->order->id,
                'customer_id' => auth()->id(),
                'reason' => $cancelReasonText
            ]);

            // Close modal and reset
            $this->closeCancelModal();

            // Refresh order data
            $this->loadOrder();

            session()->flash('success', 'Pesanan berhasil dibatalkan.');
            
        } catch (\Exception $e) {
            Log::error('Failed to cancel order', [
                'order_id' => $this->order->id,
                'error' => $e->getMessage()
            ]);
            
            session()->flash('error', 'Terjadi kesalahan saat membatalkan pesanan.');
        }
    }

    /**
     * Close cancel modal
     * 
     * @return void
     */
    public function closeCancelModal()
    {
        $this->showCancelModal = false;
        $this->selectedOrderId = null;
        $this->reset(['cancelReason', 'cancelReasonOther']);
    }

    /**
     * Get cancel reason text based on reason code
     * 
     * @param string $reason
     * @return string
     */
    private function getCancelReasonText($reason)
    {
        return match($reason) {
            'salah_pesan' => 'Salah membuat pesanan',
            'ganti_barang' => 'Ingin mengganti barang',
            'ganti_alamat' => 'Ingin mengganti alamat pengiriman',
            'tidak_jadi' => 'Tidak jadi membeli',
            'masalah_pembayaran' => 'Masalah dengan pembayaran',
            default => 'Dibatalkan oleh pelanggan'
        };
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
                
                Log::info('Payment status checked', [
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
                    
                    // Reload order data
                    $this->loadOrder();
                    
                    session()->flash('success', 'Status pembayaran berhasil diperbarui! Pesanan Anda sedang menunggu konfirmasi.');
                    return;
                }
                
                session()->flash('info', 'Status pembayaran: ' . ucfirst(str_replace('_', ' ', $transactionStatus)));
            } else {
                session()->flash('error', 'Gagal memeriksa status pembayaran.');
            }
        } catch (\Exception $e) {
            Log::error('Error checking payment status', [
                'order_id' => $this->order->order_id,
                'error' => $e->getMessage()
            ]);
            
            session()->flash('error', 'Terjadi kesalahan saat memeriksa status pembayaran.');
        }
    }

    /**
     * Continue payment for pending orders using SNAP Token
     */
    public function continuePayment()
    {
        // First, check current payment status from Midtrans
        $this->checkPaymentStatus();
        
        // Reload order to get updated status
        $this->loadOrder();
        
        // Check if payment is already completed
         if ($this->order->payment && $this->order->payment->status === 'paid') {
             session()->flash('info', 'Pembayaran sudah berhasil! Pesanan Anda sedang diproses.');
             return;
         }
         
         // Check if order can continue payment
         if (!$this->order->payment || $this->order->payment->status !== 'pending') {
             session()->flash('error', 'Pembayaran tidak dapat dilanjutkan!');
             return;
         }

        if ($this->order->isPaymentExpired()) {
            session()->flash('error', 'Pembayaran telah kedaluwarsa. Silakan buat pesanan baru.');
            return;
        }

        try {
            // Check if SNAP token already exists and still valid
            if ($this->order->payment->snap_token) {
                // Redirect to payment page with existing SNAP token
                session([
                    'order_id' => $this->order->order_id,
                    'snap_token' => $this->order->payment->snap_token
                ]);
                return redirect()->route('payment.snap');
            }

            // Create new SNAP Token with unique order ID
            $midtransService = app(MidtransService::class);
            $snapResult = $midtransService->createSnapToken($this->order);
            
            if ($snapResult['success']) {
                // Update payment with SNAP token
                $this->order->payment->update([
                    'snap_token' => $snapResult['snap_token']
                ]);
                
                // Store in session and redirect to payment page
                session([
                    'order_id' => $this->order->order_id,
                    'snap_token' => $snapResult['snap_token']
                ]);
                
                return redirect()->route('payment.snap');
            } else {
                Log::error('Failed to create Midtrans SNAP Token for continue payment', [
                    'order_id' => $this->order->order_id,
                    'error' => $snapResult['message']
                ]);
                
                session()->flash('error', 'Gagal membuat token pembayaran: ' . $snapResult['message']);
            }
        } catch (\Exception $e) {
            Log::error('Error creating SNAP token for continue payment', [
                'order_id' => $this->order->order_id,
                'error' => $e->getMessage()
            ]);
            
            session()->flash('error', 'Terjadi kesalahan saat membuat token pembayaran.');
        }
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
     * Show delivery proof modal
     */
    public function showDeliveryProof($imagePath)
    {
        $this->deliveryProofImage = $imagePath;
        $this->showDeliveryProofModal = true;
    }

    /**
     * Close delivery proof modal
     */
    public function closeDeliveryProofModal()
    {
        $this->showDeliveryProofModal = false;
        $this->deliveryProofImage = null;
    }

    // Note: Konfirmasi pesanan hanya dilakukan oleh apoteker, bukan pelanggan
    // Method openConfirmModal, closeConfirmModal, dan confirmOrderAction telah dihapus

    /**
     * Get order timeline for tracking
     */
    public function getOrderTimelineProperty()
    {
        $timeline = [];

        // Check if order is cancelled or failed first
        if ($this->order->status === 'cancelled') {
            // Order created
            $timeline[] = [
                'status' => 'created',
                'label' => 'Pesanan Dibuat',
                'date' => $this->order->created_at,
                'completed' => true,
                'icon' => 'shopping-cart'
            ];

            // Cancelled status
            $timeline[] = [
                'status' => 'cancelled',
                'label' => 'Pesanan Dibatalkan',
                'date' => $this->order->updated_at,
                'completed' => true,
                'icon' => 'x-circle',
                'is_cancelled' => true,
                'cancel_reason' => $this->order->cancel_reason
            ];

            return $timeline;
        }

        // Check if order is failed
        if ($this->order->status === 'failed') {
            // Order created
            $timeline[] = [
                'status' => 'created',
                'label' => 'Pesanan Dibuat',
                'date' => $this->order->created_at,
                'completed' => true,
                'icon' => 'shopping-cart'
            ];

            // Payment status
            if ($this->order->payment && $this->order->payment->isPaid()) {
                $timeline[] = [
                    'status' => 'payment_completed',
                    'label' => 'Pembayaran Berhasil',
                    'date' => $this->order->payment->paid_at,
                    'completed' => true,
                    'icon' => 'credit-card'
                ];
            }

            // Order confirmed
            if ($this->order->confirmed_at) {
                $timeline[] = [
                    'status' => 'confirmed',
                    'label' => 'Pesanan Dikonfirmasi',
                    'date' => $this->order->confirmed_at,
                    'completed' => true,
                    'icon' => 'check-circle'
                ];
            }

            // Processing
            if ($this->order->processing_at) {
                $timeline[] = [
                    'status' => 'processing',
                    'label' => 'Pesanan Diproses',
                    'date' => $this->order->processing_at,
                    'completed' => true,
                    'icon' => 'cog'
                ];
            }

            // Failed status
            $timeline[] = [
                'status' => 'failed',
                'label' => 'Gagal Diantar',
                'date' => $this->order->failed_at,
                'completed' => true,
                'icon' => 'x-circle',
                'is_failed' => true,
                'failed_reason' => $this->order->failed_reason,
                'failed_by_courier' => $this->order->failedByCourier ? $this->order->failedByCourier->name : null,
                'failed_by_courier_phone' => $this->order->failedByCourier ? $this->order->failedByCourier->phone : null
            ];

            return $timeline;
        }

        // 1. Order created - always show as completed
        $timeline[] = [
            'status' => 'created',
            'label' => 'Pesanan Dibuat',
            'date' => $this->order->created_at,
            'completed' => true,
            'icon' => 'shopping-cart'
        ];

        // 2. Payment status - always show
        if ($this->order->payment && $this->order->payment->isPaid()) {
            $timeline[] = [
                'status' => 'payment_completed',
                'label' => 'Pembayaran Berhasil',
                'date' => $this->order->payment->paid_at,
                'completed' => true,
                'icon' => 'credit-card'
            ];
        } else {
            $timeline[] = [
                'status' => 'payment_pending',
                'label' => 'Menunggu Pembayaran',
                'date' => null,
                'completed' => false,
                'icon' => 'credit-card'
            ];
        }

        // 3. Order confirmed - show if payment is completed
        if ($this->order->payment && $this->order->payment->isPaid()) {
            if ($this->order->status === 'waiting_confirmation') {
                $timeline[] = [
                    'status' => 'waiting_confirmation',
                    'label' => 'Menunggu Konfirmasi',
                    'date' => null,
                    'completed' => false,
                    'icon' => 'clock'
                ];
            } else {
                $timeline[] = [
                    'status' => 'confirmed',
                    'label' => 'Pesanan Dikonfirmasi',
                    'date' => $this->order->confirmed_at,
                    'completed' => in_array($this->order->status, ['confirmed', 'processing', 'ready_to_ship', 'ready_for_pickup', 'shipped', 'delivered', 'picked_up', 'completed']),
                    'icon' => 'check-circle'
                ];
            }
        }

        // 4. Processing - show if confirmed or beyond
        if ($this->order->payment && $this->order->payment->isPaid() && in_array($this->order->status, ['confirmed', 'processing', 'ready_to_ship', 'ready_for_pickup', 'shipped', 'delivered', 'picked_up', 'completed'])) {
            $timeline[] = [
                'status' => 'processing',
                'label' => 'Pesanan Diproses',
                'date' => in_array($this->order->status, ['processing', 'ready_to_ship', 'ready_for_pickup', 'shipped', 'delivered', 'picked_up', 'completed']) ? $this->order->processing_at : null,
                'completed' => in_array($this->order->status, ['processing', 'ready_to_ship', 'ready_for_pickup', 'shipped', 'delivered', 'picked_up', 'completed']),
                'icon' => 'cog'
            ];
        }

        // 5. Ready to ship/pickup - show if ready_to_ship or ready_for_pickup or beyond
        if ($this->order->payment && $this->order->payment->isPaid() && in_array($this->order->status, ['ready_to_ship', 'ready_for_pickup', 'shipped', 'delivered', 'picked_up', 'completed'])) {
            $timeline[] = [
                'status' => 'ready_to_ship',
                'label' => $this->order->shipping_type === 'delivery' ? 'Pesanan Siap Diantar' : 'Pesanan Siap Diambil',
                'date' => $this->order->shipping_type === 'delivery' ? $this->order->ready_to_ship_at : $this->order->ready_for_pickup_at,
                'completed' => $this->order->shipping_type === 'pickup' ? 
                    in_array($this->order->status, ['ready_for_pickup', 'picked_up', 'completed']) : 
                    in_array($this->order->status, ['shipped', 'delivered', 'completed']),
                'icon' => $this->order->shipping_type === 'delivery' ? 'package' : 'package-check'
            ];
        }

        // 6. Shipped - show if shipped or delivered (only for delivery type)
        if ($this->order->payment && $this->order->payment->isPaid() && $this->order->shipping_type === 'delivery' && in_array($this->order->status, ['shipped', 'delivered', 'completed'])) {
            $timeline[] = [
                'status' => 'shipped',
                'label' => 'Pesanan Diantar',
                'date' => $this->order->shipped_at,
                'completed' => in_array($this->order->status, ['shipped', 'delivered', 'completed']),
                'icon' => 'truck'
            ];
        }

        // 7. Picked up - show for pickup orders when ready_for_pickup or beyond
        if ($this->order->payment && $this->order->payment->isPaid() && $this->order->shipping_type === 'pickup' && in_array($this->order->status, ['ready_for_pickup', 'picked_up', 'completed'])) {
            $pickupProof = $this->order->pickup_image;
            
            $timeline[] = [
                'status' => 'picked_up',
                'label' => 'Pesanan Diambil',
                'date' => $this->order->picked_up_at,
                'completed' => in_array($this->order->status, ['picked_up', 'completed']),
                'icon' => 'check-circle',
                'delivery_proof' => $pickupProof,
                'show_proof_link' => in_array($this->order->status, ['picked_up', 'completed']) && $pickupProof
            ];
        }

        // 8. Delivered - show if delivered or completed (for delivery orders)
        if ($this->order->payment && $this->order->payment->isPaid() && $this->order->shipping_type === 'delivery' && in_array($this->order->status, ['delivered', 'completed'])) {
            $deliveryProof = null;
            if ($this->order->delivery && $this->order->delivery->delivery_photo) {
                $deliveryProof = $this->order->delivery->delivery_photo;
            }
            
            $timeline[] = [
                'status' => 'delivered',
                'label' => 'Pesanan Sampai Tujuan',
                'date' => $this->order->delivered_at,
                'completed' => in_array($this->order->status, ['delivered', 'completed']),
                'icon' => 'check-circle',
                'delivery_proof' => $deliveryProof,
                'show_proof_link' => in_array($this->order->status, ['delivered', 'completed']) && $deliveryProof
            ];
        }

        // 9. Completed - show final status for both delivery and pickup orders
        if ($this->order->payment && $this->order->payment->isPaid() && 
            (($this->order->shipping_type === 'delivery' && in_array($this->order->status, ['delivered', 'completed'])) ||
             ($this->order->shipping_type === 'pickup' && in_array($this->order->status, ['picked_up', 'completed'])))) {
            $timeline[] = [
                'status' => 'completed',
                'label' => 'Pesanan Selesai',
                'date' => $this->order->completed_at ?? $this->order->updated_at,
                'completed' => in_array($this->order->status, ['picked_up', 'delivered', 'completed']),
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
            return 'Ambil di apotek/toko';
        }

        // Handle array shipping_address
        if (is_array($this->order->shipping_address)) {
            $address = $this->order->shipping_address;
            $parts = [];
            
            // Use correct field names from CheckoutService
            if (!empty($address['detailed_address'])) {
                $parts[] = $address['detailed_address'];
            }
            if (!empty($address['village'])) {
                $parts[] = $address['village'];
            }
            if (!empty($address['sub_district'])) {
                $parts[] = $address['sub_district'];
            }
            if (!empty($address['regency'])) {
                $parts[] = $address['regency'];
            }
            if (!empty($address['province'])) {
                $parts[] = $address['province'];
            }
            if (!empty($address['postal_code'])) {
                $parts[] = $address['postal_code'];
            }
            
            return !empty($parts) ? implode(', ', $parts) : 'Alamat tidak lengkap';
        }

        return $this->order->shipping_address ?? 'Alamat tidak tersedia';
    }

    public function render()
    {
        return view('livewire.pelanggan.order-detail', [
            'timeline' => $this->orderTimeline,
            'shippingAddress' => $this->shippingAddress
        ]);
    }
}