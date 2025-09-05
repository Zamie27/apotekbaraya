<?php

namespace App\Livewire\Kurir;

use App\Models\Delivery;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

#[Layout('components.layouts.kurir')]
class DeliveryDetail extends Component
{
    use WithFileUploads;

    public $deliveryId;
    public $delivery;
    public $timeline = [];
    public $shippingAddress = '';

    // Modal properties for updating delivery
    public $showUpdateModal = false;
    public $deliveryPhoto;
    public $deliveryNotes = '';
    public $newStatus = '';
    public $failedReason = '';

    // Validation rules
    protected $rules = [
        'deliveryPhoto' => 'required|image|max:2048', // 2MB max
        'deliveryNotes' => 'nullable|string|max:500',
        'newStatus' => 'required|in:in_transit,delivered,failed',
        'failedReason' => 'required_if:newStatus,failed|string|max:500'
    ];

    protected $messages = [
        'deliveryPhoto.required' => 'Foto konfirmasi pengiriman wajib diupload.',
        'deliveryPhoto.image' => 'File harus berupa gambar.',
        'deliveryPhoto.max' => 'Ukuran file maksimal 2MB.',
        'newStatus.required' => 'Status pengiriman harus dipilih.',
        'deliveryNotes.max' => 'Catatan maksimal 500 karakter.',
        'failedReason.required_if' => 'Alasan pembatalan wajib diisi ketika status gagal kirim.',
        'failedReason.max' => 'Alasan pembatalan maksimal 500 karakter.'
    ];

    /**
     * Mount the component with delivery ID.
     */
    public function mount($deliveryId)
    {
        $this->deliveryId = $deliveryId;
        $this->loadDelivery();
        $this->buildTimeline();
        $this->formatShippingAddress();
    }

    /**
     * Load delivery data with relationships.
     */
    public function loadDelivery()
    {
        $this->delivery = Delivery::with([
                'order', 
                'order.user', 
                'order.items', 
                'order.items.product',
                'order.payment',
                'order.payment.paymentMethod',
                'courier'
            ])
            ->where('courier_id', Auth::id())
            ->findOrFail($this->deliveryId);
    }

    /**
     * Build timeline for delivery status.
     */
    public function buildTimeline()
    {
        $order = $this->delivery->order;
        $delivery = $this->delivery;

        // Debug: Log current status for troubleshooting
        Log::info('Timeline Debug', [
            'order_status' => $order->status,
            'delivery_status' => $delivery->delivery_status,
            'payment_status' => $order->payment?->payment_status,
            'order_id' => $order->order_id
        ]);

        // Check if order/delivery failed - if so, show completed steps up to failure point
        $isFailed = $order->status === 'failed' || $delivery->delivery_status === 'failed';
        $isCancelled = $order->status === 'cancelled';
        
        $this->timeline = [
            [
                'label' => 'Pesanan Dibuat',
                'completed' => true,
                'date' => $order->created_at
            ],
            [
                'label' => 'Pembayaran Dikonfirmasi',
                'completed' => ($order->payment && $order->payment->payment_status === 'paid') || 
                              in_array($order->status, ['processing', 'ready_to_ship', 'shipped', 'delivered', 'completed']) ||
                              ($isFailed && $order->processing_at), // Show completed if failed but had processing
                'date' => $order->payment?->paid_at ?? $order->processing_at
            ],
            [
                'label' => 'Pesanan Diproses',
                'completed' => in_array($order->status, ['processing', 'ready_to_ship', 'shipped', 'delivered', 'completed']) ||
                              ($isFailed && $order->processing_at), // Show completed if failed but had processing
                'date' => $order->processing_at
            ],
            [
                'label' => 'Siap Diantar',
                'completed' => in_array($delivery->delivery_status, ['ready_to_ship', 'in_transit', 'delivered']) || 
                              in_array($order->status, ['ready_to_ship', 'shipped', 'delivered', 'completed']) ||
                              ($isFailed && $order->ready_to_ship_at), // Show completed if failed but was ready to ship
                'date' => $order->ready_to_ship_at
            ],
            [
                'label' => 'Dalam Perjalanan',
                'completed' => in_array($delivery->delivery_status, ['in_transit', 'delivered']) || 
                              in_array($order->status, ['shipped', 'delivered', 'completed']) ||
                              ($isFailed && $order->shipped_at), // Show completed if failed but was in transit
                'date' => $order->shipped_at
            ],
            [
                'label' => 'Terkirim',
                'completed' => $delivery->delivery_status === 'delivered' || 
                              $order->status === 'delivered' || 
                              $order->status === 'completed' ||
                              ($isFailed && $order->shipped_at), // Show completed if failed after being shipped
                'date' => $delivery->delivered_at ?? $order->delivered_at
            ]
        ];

        // Add cancellation or failure status if applicable
        if ($order->status === 'cancelled') {
            $this->timeline[] = [
                'label' => 'Dibatalkan',
                'completed' => true,
                'date' => $order->cancelled_at
            ];
        } elseif ($delivery->delivery_status === 'failed' || $order->status === 'failed') {
            $this->timeline[] = [
                'label' => 'Gagal Kirim',
                'completed' => true,
                'date' => $delivery->updated_at ?? $order->failed_at,
                'failed_reason' => $order->failed_reason,
                'failed_by_courier' => $order->failedByCourier ? $order->failedByCourier->name : null
            ];
        }
    }

    /**
     * Format shipping address for display.
     */
    public function formatShippingAddress()
    {
        $address = $this->delivery->delivery_address;
        
        if (is_array($address)) {
            $this->shippingAddress = implode(', ', array_filter([
                $address['street'] ?? '',
                $address['city'] ?? '',
                $address['state'] ?? '',
                $address['postal_code'] ?? ''
            ]));
        } else {
            $this->shippingAddress = $address ?? 'Alamat tidak tersedia';
        }
    }

    /**
     * Show update delivery modal.
     */
    public function showUpdateDelivery()
    {
        $this->deliveryNotes = $this->delivery->delivery_notes ?? '';
        $this->newStatus = $this->delivery->delivery_status;
        $this->showUpdateModal = true;
    }

    /**
     * Start delivery - change status from ready_to_ship to in_transit.
     */
    public function startDelivery()
    {
        try {
            if ($this->delivery->delivery_status !== 'ready_to_ship') {
                session()->flash('error', 'Pengiriman tidak dapat dimulai pada status saat ini.');
                return;
            }

            // Update delivery status to in_transit
            $this->delivery->update([
                'delivery_status' => 'in_transit'
            ]);

            // Update order status to shipped
            $this->delivery->order->update([
                'status' => 'shipped',
                'shipped_at' => now()
            ]);

            // Reload data
            $this->loadDelivery();
            $this->buildTimeline();

            session()->flash('success', 'Pengiriman berhasil dimulai.');

        } catch (\Exception $e) {
            Log::error('Error starting delivery: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat memulai pengiriman.');
        }
    }

    /**
     * Update delivery status with photo and notes.
     */
    public function updateDelivery()
    {
        // Validate based on status
        $rules = $this->rules;
        if ($this->newStatus === 'failed') {
            $rules['deliveryPhoto'] = 'nullable|image|max:2048';
        }

        $this->validate($rules);

        try {
            $updateData = [
                'delivery_status' => $this->newStatus,
                'delivery_notes' => $this->deliveryNotes
            ];

            // Handle photo upload
            if ($this->deliveryPhoto) {
                // Delete old photo if exists
                if ($this->delivery->delivery_photo) {
                    Storage::delete($this->delivery->delivery_photo);
                }

                $photoPath = $this->deliveryPhoto->store('delivery-photos', 'public');
                $updateData['delivery_photo'] = $photoPath;
            }

            // Handle status-specific updates
            if ($this->newStatus === 'delivered') {
                $updateData['delivered_at'] = now();
                
                // Update order status to delivered
                $this->delivery->order->update([
                    'status' => 'delivered',
                    'delivered_at' => now()
                ]);
            } elseif ($this->newStatus === 'failed') {
                // Update order status to failed when delivery fails
                $this->delivery->order->update([
                    'status' => 'failed',
                    'failed_at' => now(),
                    'failed_by_courier_id' => Auth::id(), // Store courier who failed the delivery
                    'failed_reason' => $this->failedReason ?? 'Pengiriman gagal' // Store failure reason
                ]);
            }

            // Update delivery
            $this->delivery->update($updateData);

            // Reload data
            $this->loadDelivery();
            $this->buildTimeline();

            // Reset form
            $this->reset(['deliveryPhoto', 'deliveryNotes', 'newStatus', 'failedReason', 'showUpdateModal']);

            session()->flash('success', 'Status pengiriman berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('Error updating delivery: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat memperbarui pengiriman.');
        }
    }

    /**
     * Close modal and reset form.
     */
    public function closeModal()
    {
        $this->reset(['deliveryPhoto', 'deliveryNotes', 'newStatus', 'failedReason', 'showUpdateModal']);
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.kurir.delivery-detail');
    }
}