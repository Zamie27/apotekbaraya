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

    // Modal state properties
    public bool $showModal = false;
    public string $actionType = '';
    public string $newStatus = '';
    public string $deliveryNotes = '';
    public string $failedReason = '';
    public $deliveryPhoto;

    // Validation rules (dynamic validation is used in updateDelivery method)
    protected $rules = [
        'deliveryPhoto' => 'nullable|image|max:2048', // 2MB max
        'deliveryNotes' => 'nullable|string|max:500',
        'newStatus' => 'required|in:in_transit,delivered,failed',
        'failedReason' => 'nullable|string|max:500'
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
            'status' => $delivery->status,
            'payment_status' => $order->payment?->payment_status,
            'order_id' => $order->order_id
        ]);

        // Check if order/delivery failed - if so, show completed steps up to failure point
        $isFailed = $order->status === 'failed' || $delivery->status === 'failed';
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
                'completed' => in_array($delivery->status, ['ready_to_ship', 'in_transit', 'delivered']) || 
                              in_array($order->status, ['ready_to_ship', 'shipped', 'delivered', 'completed']) ||
                              ($isFailed && $order->ready_to_ship_at), // Show completed if failed but was ready to ship
                'date' => $order->ready_to_ship_at
            ],
            [
                'label' => 'Dalam Perjalanan',
                'completed' => in_array($delivery->status, ['in_transit', 'delivered']) || 
                              in_array($order->status, ['shipped', 'delivered', 'completed']) ||
                              ($isFailed && $order->shipped_at), // Show completed if failed but was in transit
                'date' => $order->shipped_at
            ],
            [
                'label' => 'Terkirim',
                'completed' => $delivery->status === 'delivered' || 
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
        } elseif ($delivery->status === 'failed' || $order->status === 'failed') {
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
     * Format shipping address for display with complete details.
     */
    public function formatShippingAddress()
    {
        $address = $this->delivery->order->shipping_address;
        
        if (is_array($address)) {
            // Build complete address string with all available details
            $addressParts = [];
            
            // Add detailed address first
            if (!empty($address['detailed_address'])) {
                $addressParts[] = $address['detailed_address'];
            }
            
            // Add district/subdistrict
            if (!empty($address['district'])) {
                $addressParts[] = $address['district'];
            }
            
            // Add city
            if (!empty($address['city'])) {
                $addressParts[] = $address['city'];
            }
            
            // Add regency (kabupaten)
            if (!empty($address['regency'])) {
                $addressParts[] = $address['regency'];
            }
            
            // Add province
            if (!empty($address['province'])) {
                $addressParts[] = $address['province'];
            }
            
            // Add postal code
            if (!empty($address['postal_code'])) {
                $addressParts[] = $address['postal_code'];
            }
            
            $this->shippingAddress = implode(', ', array_filter($addressParts));
        } else {
            $this->shippingAddress = $address ?? 'Alamat tidak tersedia';
        }
    }

    /**
     * Show modal for updating delivery status.
     */
    public function showUpdateDeliveryModal()
    {
        $this->deliveryNotes = $this->delivery->delivery_notes ?? '';
        $this->newStatus = ''; // Reset to empty so user must select new status
        $this->failedReason = ''; // Reset failed reason
        $this->showModal = true;
    }

    /**
     * Listen for confirmation events from ConfirmationModal.
     */
    #[On('confirmation-confirmed')]
    public function handleConfirmation($actionMethod, $actionParams = [])
    {
        if (method_exists($this, $actionMethod)) {
            $this->$actionMethod(...$actionParams);
        }
    }

    /**
     * Open action modal for delivery status update
     */
    public function openActionModal($actionType)
    {
        Log::info('DeliveryDetail: Opening action modal', [
            'delivery_id' => $this->delivery->delivery_id,
            'action_type' => $actionType,
            'current_status' => $this->delivery->status
        ]);
        
        $this->actionType = $actionType;
        $this->showModal = true;
        $this->resetValidation();
        
        // Reset form fields
        $this->deliveryNotes = '';
        $this->failedReason = '';
        $this->deliveryPhoto = null;
        
        // Set new status based on action type
        $this->newStatus = match($actionType) {
            'start_delivery' => 'in_transit',
            'complete_delivery' => 'delivered',
            'fail_delivery' => 'failed',
            default => $this->delivery->status
        };
    }
    
    /**
     * Show confirmation modal for starting delivery (backward compatibility)
     */
    public function showStartDeliveryConfirmation()
    {
        $this->openActionModal('start_delivery');
    }

    /**
     * Start delivery - change status from ready_to_ship to in_transit.
     */
    public function startDelivery()
    {
        try {
            Log::info('StartDelivery called for delivery ID: ' . $this->delivery->delivery_id);
            Log::info('Current delivery status: ' . $this->delivery->status);
            Log::info('Current order status: ' . $this->delivery->order->status);
            
            // Check if delivery status is ready_to_ship
            if ($this->delivery->status !== 'ready_to_ship') {
            Log::warning('Delivery status is not ready_to_ship: ' . $this->delivery->status);
            session()->flash('error', 'Pengiriman tidak dapat dimulai. Status saat ini: ' . $this->delivery->status);
                return;
            }

            // Update delivery status to in_transit
            $deliveryUpdated = $this->delivery->update([
                'status' => 'in_transit'
            ]);
            Log::info('Delivery update result: ' . ($deliveryUpdated ? 'success' : 'failed'));

            // Update order status to shipped
            $orderUpdated = $this->delivery->order->update([
                'status' => 'shipped',
                'shipped_at' => now()
            ]);
            Log::info('Order update result: ' . ($orderUpdated ? 'success' : 'failed'));

            // Reload data
            $this->loadDelivery();
            $this->buildTimeline();
            
            // Close modal
            $this->closeModal();
            
            session()->flash('success', 'Pengiriman berhasil dimulai!');
            
            Log::info('DeliveryDetail: Start delivery completed successfully', [
                'delivery_id' => $this->delivery->delivery_id,
                'new_delivery_status' => $this->delivery->status,
                'new_order_status' => $this->delivery->order->status
            ]);

        } catch (\Exception $e) {
            Log::error('Error starting delivery: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            session()->flash('error', 'Terjadi kesalahan saat memulai pengiriman.');
        }
    }

    /**
     * Update delivery status based on action type
     */
    public function updateDelivery()
    {
        Log::info('UpdateDelivery method called', [
            'delivery_id' => $this->delivery->delivery_id,
            'current_status' => $this->delivery->status,
            'action_type' => $this->actionType,
            'new_status' => $this->newStatus,
            'delivery_notes' => $this->deliveryNotes,
            'failed_reason' => $this->failedReason ?? null,
            'has_photo' => !empty($this->deliveryPhoto)
        ]);
        
        try {
            if ($this->actionType === 'complete_delivery') {
                $this->completeDelivery();
            } elseif ($this->actionType === 'fail_delivery') {
                $this->failDelivery();
            }

            // Reload data
            $this->loadDelivery();
            $this->buildTimeline();

            // Close modal and reset form
            $this->closeModal();
            
            Log::info('UpdateDelivery completed successfully', [
                'final_delivery_status' => $this->delivery->status,
                'final_order_status' => $this->delivery->order->status
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating delivery: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat memperbarui pengiriman.');
        }
    }

    /**
     * Complete delivery process
     */
    private function completeDelivery()
    {
        $this->validate([
            'deliveryPhoto' => 'required|image|max:2048',
            'deliveryNotes' => 'nullable|string|max:500',
        ]);

        // Delete old photo if exists
        if ($this->delivery->delivery_photo) {
            Storage::delete($this->delivery->delivery_photo);
        }

        // Upload photo
        $photoPath = $this->deliveryPhoto->store('delivery-photos', 'public');

        $this->delivery->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'delivery_photo' => $photoPath,
            'delivery_notes' => $this->deliveryNotes,
        ]);

        // Update order status
        $this->delivery->order->update([
            'status' => 'delivered',
            'delivered_at' => now()
        ]);

        session()->flash('success', 'Pengiriman berhasil diselesaikan!');
        Log::info('Delivery completed', ['delivery_id' => $this->delivery->delivery_id]);
    }

    /**
     * Mark delivery as failed
     */
    private function failDelivery()
    {
        $this->validate([
            'failedReason' => 'required|string|max:500',
            'deliveryNotes' => 'nullable|string|max:500',
            'deliveryPhoto' => 'nullable|image|max:2048',
        ]);

        $updateData = [
            'status' => 'failed',
            'delivery_notes' => $this->deliveryNotes,
        ];

        // Handle optional photo upload for failed delivery
        if ($this->deliveryPhoto) {
            // Delete old photo if exists
            if ($this->delivery->delivery_photo) {
                Storage::delete($this->delivery->delivery_photo);
            }
            $photoPath = $this->deliveryPhoto->store('delivery-photos', 'public');
            $updateData['delivery_photo'] = $photoPath;
        }

        $this->delivery->update($updateData);

        // Update order status
        $this->delivery->order->update([
            'status' => 'failed',
            'failed_at' => now(),
            'failed_by_courier_id' => Auth::id(),
            'failed_reason' => $this->failedReason
        ]);

        session()->flash('success', 'Status pengiriman berhasil diperbarui menjadi gagal.');
        Log::info('Delivery marked as failed', ['delivery_id' => $this->delivery->delivery_id, 'reason' => $this->failedReason]);
    }

    /**
     * Handle form submission based on action type
     */
    public function submitAction()
    {
        Log::info('DeliveryDetail: Submitting action', [
            'delivery_id' => $this->delivery->delivery_id,
            'action_type' => $this->actionType,
            'new_status' => $this->newStatus
        ]);
        
        try {
            switch ($this->actionType) {
                case 'start_delivery':
                    $this->startDelivery();
                    break;
                case 'complete_delivery':
                    $this->updateDelivery();
                    break;
                case 'fail_delivery':
                    $this->updateDelivery();
                    break;
                default:
                    session()->flash('error', 'Aksi tidak valid!');
            }
        } catch (\Exception $e) {
            Log::error('Error in submitAction: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get badge color class for delivery status
     */
    public function getDeliveryStatusBadgeColor($status)
    {
        return match($status) {
            'pending' => 'badge-warning',
            'ready_to_ship' => 'badge-info',
            'in_transit' => 'badge-primary',
            'delivered' => 'badge-success',
            'failed' => 'badge-error',
            default => 'badge-neutral'
        };
    }

    /**
     * Get human readable label for delivery status
     */
    public function getDeliveryStatusLabel($status)
    {
        return match($status) {
            'pending' => 'Menunggu Pengiriman',
            'ready_to_ship' => 'Siap Diantar',
            'in_transit' => 'Pesanan Diantar',
            'delivered' => 'Pesanan Selesai',
            'failed' => 'Pesanan Dibatalkan',
            default => 'Status Tidak Dikenal'
        };
    }

    /**
     * Get badge color class for order status
     */
    public function getOrderStatusBadgeColor($status)
    {
        return match($status) {
            'pending' => 'badge-warning',
            'confirmed' => 'badge-info',
            'processing' => 'badge-primary',
            'ready_to_ship' => 'badge-secondary',
            'shipped' => 'badge-accent',
            'delivered' => 'badge-success',
            'cancelled' => 'badge-error',
            'refunded' => 'badge-neutral',
            default => 'badge-ghost'
        };
    }

    /**
     * Get human readable label for order status
     */
    public function getOrderStatusLabel($status)
    {
        return match($status) {
            'pending' => 'Menunggu',
            'confirmed' => 'Dikonfirmasi',
            'processing' => 'Diproses',
            'ready_to_ship' => 'Siap Diantar',
            'shipped' => 'Dikirim',
            'delivered' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            'refunded' => 'Dikembalikan',
            default => 'Status Tidak Diketahui'
        };
    }
    
    /**
     * Close modal and reset form
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->actionType = '';
        $this->newStatus = '';
        $this->deliveryNotes = '';
        $this->failedReason = '';
        $this->deliveryPhoto = null;
        $this->resetValidation();
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.kurir.delivery-detail');
    }
}