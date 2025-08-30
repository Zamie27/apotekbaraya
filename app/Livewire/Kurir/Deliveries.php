<?php

namespace App\Livewire\Kurir;

use App\Models\Delivery;
use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.kurir')]
class Deliveries extends Component
{
    use WithPagination, WithFileUploads;

    // Filter properties
    public $statusFilter = 'all';
    public $search = '';

    // Modal properties
    public $selectedDelivery = null;
    public $showUpdateModal = false;
    public $showDetailModal = false;
    public $deliveryPhoto;
    public $deliveryNotes = '';
    public $newStatus = '';

    // Validation rules
    protected $rules = [
        'deliveryPhoto' => 'required|image|max:2048', // 2MB max
        'deliveryNotes' => 'nullable|string|max:500',
        'newStatus' => 'required|in:in_transit,delivered,failed'
    ];

    /**
     * Get validation rules based on new status.
     */
    protected function getValidationRules()
    {
        $rules = [
            'deliveryNotes' => 'nullable|string|max:500'
        ];

        // Photo validation based on new status
        if ($this->newStatus === 'delivered') {
            $rules['deliveryPhoto'] = 'required|image|max:2048';
        } elseif ($this->newStatus === 'failed') {
            $rules['deliveryPhoto'] = 'nullable|image|max:2048';
            $rules['deliveryNotes'] = 'required|string|max:500'; // Required for cancellation reason
        } else {
            $rules['deliveryPhoto'] = 'nullable|image|max:2048';
        }

        return $rules;
    }

    protected $messages = [
        'deliveryPhoto.required' => 'Foto konfirmasi pengiriman wajib diupload.',
        'deliveryPhoto.image' => 'File harus berupa gambar.',
        'deliveryPhoto.max' => 'Ukuran file maksimal 2MB.',
        'newStatus.required' => 'Status pengiriman harus dipilih.',
        'deliveryNotes.max' => 'Catatan maksimal 500 karakter.'
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
     * Show delivery detail modal.
     */
    public function showDeliveryDetail($deliveryId)
    {
        $this->selectedDelivery = Delivery::with([
                'order', 
                'order.user', 
                'order.items', 
                'order.items.product',
                'order.payment',
                'order.payment.paymentMethod'
            ])
            ->where('courier_id', Auth::id())
            ->findOrFail($deliveryId);
        
        $this->showDetailModal = true;
    }

    /**
     * Show update delivery modal.
     */
    public function showUpdateDelivery($deliveryId)
    {
        $this->selectedDelivery = Delivery::with([
                'order', 
                'order.user', 
                'order.items', 
                'order.items.product',
                'order.payment',
                'order.payment.paymentMethod'
            ])
            ->where('courier_id', Auth::id())
            ->findOrFail($deliveryId);
        
        $this->deliveryNotes = $this->selectedDelivery->delivery_notes ?? '';
        $this->newStatus = $this->selectedDelivery->delivery_status;
        $this->showUpdateModal = true;
    }

    /**
     * Start delivery - change status from ready_to_ship to shipped.
     */
    public function startDelivery($deliveryId)
    {
        try {
            $delivery = Delivery::where('courier_id', Auth::id())
                              ->where('delivery_status', 'ready_to_ship')
                              ->findOrFail($deliveryId);

            // Update delivery status to in_transit
            $delivery->update([
                'delivery_status' => 'in_transit'
            ]);

            // Update order status to shipped
            $delivery->order->update([
                'status' => 'shipped',
                'shipped_at' => now()
            ]);

            session()->flash('success', 'Pesanan berhasil dimulai untuk diantar.');

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Complete delivery - change status from shipped to delivered with photo.
     */
    public function completeDelivery($deliveryId)
    {
        $this->selectedDelivery = Delivery::where('courier_id', Auth::id())
                                        ->where('delivery_status', 'in_transit')
                                        ->findOrFail($deliveryId);
        $this->newStatus = 'delivered';
        $this->showUpdateModal = true;
    }

    /**
     * Cancel delivery - change status to failed with reason.
     */
    public function cancelDelivery($deliveryId)
    {
        $this->selectedDelivery = Delivery::where('courier_id', Auth::id())
                                        ->where('delivery_status', 'in_transit')
                                        ->findOrFail($deliveryId);
        $this->newStatus = 'failed';
        $this->showUpdateModal = true;
    }

    /**
     * Confirm pickup - for pickup orders that are ready to be picked up.
     */
    public function confirmPickup($deliveryId)
    {
        $this->selectedDelivery = Delivery::with('order')
                                        ->where('courier_id', Auth::id())
                                        ->findOrFail($deliveryId);
        
        // Check if this is a pickup order and ready for pickup
        if ($this->selectedDelivery->order->shipping_type !== 'pickup' || 
            $this->selectedDelivery->order->status !== 'ready_for_pickup') {
            session()->flash('error', 'Pesanan ini tidak dapat dikonfirmasi untuk diambil.');
            return;
        }
        
        $this->newStatus = 'picked_up';
        $this->showUpdateModal = true;
    }

    /**
     * Update delivery status with photo confirmation.
     */
    public function updateDelivery()
    {
        // Use dynamic validation based on new status
        $this->validate($this->getValidationRules());

        try {
            // Upload photo
            $photoPath = null;
            if ($this->deliveryPhoto) {
                $photoPath = $this->deliveryPhoto->store('delivery-photos', 'public');
            }

            // Prepare update data
            $updateData = [
                'delivery_status' => $this->newStatus,
                'delivery_notes' => $this->deliveryNotes,
            ];

            // Add photo if uploaded
            if ($photoPath) {
                $updateData['delivery_photo'] = $photoPath;
            }

            // Set timestamps based on status
            if ($this->newStatus === 'delivered') {
                $updateData['delivered_at'] = now();
            }

            // Update delivery
            $this->selectedDelivery->update($updateData);

            // Update order status based on delivery status
            $this->updateOrderStatus();

            session()->flash('success', 'Status pesanan berhasil diperbarui.');
            $this->closeModal();

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update order status based on delivery status.
     */
    private function updateOrderStatus()
    {
        $order = $this->selectedDelivery->order;
        
        switch ($this->newStatus) {
            case 'in_transit':
                $order->update([
                    'status' => 'shipped',
                    'shipped_at' => now()
                ]);
                break;
                
            case 'delivered':
                $order->update([
                    'status' => 'delivered',
                    'delivered_at' => now()
                ]);
                break;
                
            case 'picked_up':
                // For pickup orders, use the markAsPickedUp method
                if ($order->shipping_type === 'pickup') {
                    $photoPath = null;
                    if ($this->deliveryPhoto) {
                        $photoPath = $this->deliveryPhoto->store('pickup-photos', 'public');
                    }
                    $order->markAsPickedUp($photoPath);
                }
                break;
                
            case 'failed':
                // Keep order status as shipped but mark delivery as failed
                // Admin/Apoteker can decide next action
                break;
        }
    }

    /**
     * Close modal and reset properties.
     */
    public function closeModal()
    {
        $this->showUpdateModal = false;
        $this->showDetailModal = false;
        $this->selectedDelivery = null;
        $this->deliveryPhoto = null;
        $this->deliveryNotes = '';
        $this->newStatus = '';
        $this->resetValidation();
    }

    /**
     * Render the component.
     */
    public function render()
    {
        $deliveries = Delivery::with([
                'order', 
                'order.user', 
                'order.items', 
                'order.items.product',
                'order.payment',
                'order.payment.paymentMethod'
            ])
            ->where('courier_id', Auth::id())
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('delivery_status', $this->statusFilter);
            })
            ->when($this->search, function ($query) {
                $query->whereHas('order', function ($q) {
                    $q->where('order_number', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', function ($userQuery) {
                          $userQuery->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.kurir.deliveries', compact('deliveries'));
    }
}
