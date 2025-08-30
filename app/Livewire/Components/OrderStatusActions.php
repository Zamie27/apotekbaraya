<?php

namespace App\Livewire\Components;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class OrderStatusActions extends Component
{
    use WithFileUploads;

    public Order $order;
    public string $userRole;
    public bool $showModal = false;
    public string $actionType = '';
    public string $confirmationNote = '';
    public string $cancelReason = '';
    public $receiptImage;
    public $pickupImage;
    public $selectedCourierId;

    /**
     * Validation rules for the component
     */
    protected function rules()
    {
        $rules = [
            'confirmationNote' => 'nullable|string|max:500',
            'cancellationReason' => 'required_if:actionType,cancel|string|max:500',
        ];

        // Add receipt image validation when action type is ready_to_ship
        if ($this->actionType === 'ready_to_ship') {
            $rules['receiptImage'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
            $rules['selectedCourierId'] = 'required|exists:users,user_id';
        } elseif ($this->actionType === 'confirm' && $this->order->status === 'processing') {
            // For processing status (ready_to_ship), receipt image is required
            $rules['receiptImage'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
        }
        
        // Pickup image is required for pickup confirmation
        if ($this->actionType === 'pickup') {
            $rules['pickupImage'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
        }
        // For waiting_confirmation status, no receipt image required

        return $rules;
    }

    protected $messages = [
        'cancelReason.required_if' => 'Alasan pembatalan harus diisi.',
        'receiptImage.image' => 'File harus berupa gambar.',
        'receiptImage.max' => 'Ukuran file maksimal 2MB.',
        'receiptImage.required_if' => 'Struk pesanan harus diupload untuk menandai pesanan siap diantar.',
        'pickupImage.required' => 'Foto bukti pengambilan harus diupload.',
        'pickupImage.image' => 'File harus berupa gambar.',
        'pickupImage.max' => 'Ukuran file maksimal 2MB.',
        'selectedCourierId.required_if' => 'Kurir harus dipilih untuk pesanan yang akan diantar.',
        'selectedCourierId.exists' => 'Kurir yang dipilih tidak valid.'
    ];

    /**
     * Mount the component.
     */
    public function mount(Order $order, string $userRole = 'apoteker')
    {
        $this->order = $order;
        $this->userRole = $userRole;
    }

    /**
     * Open action modal.
     */
    public function openActionModal($actionType)
    {
        $this->actionType = $actionType;
        
        // For waiting_confirmation status, show modal for both confirm and cancel actions
        // Only ready_to_ship status requires special handling (upload receipt + select courier)
        
        $this->showModal = true;
        $this->resetValidation();
        
        // Reset form fields
        $this->confirmationNote = '';
        $this->cancelReason = '';
        $this->receiptImage = null;
        $this->pickupImage = null;
    }

    /**
     * Handle form submission based on action type.
     */
    public function submitAction()
    {
        switch ($this->actionType) {
            case 'confirm':
                $this->confirmOrder();
                break;
            case 'cancel':
                $this->cancelOrder();
                break;
            case 'process':
                $this->markAsProcessing();
                break;
            case 'ready_to_ship':
                $this->markAsReadyToShip();
                break;
            case 'ship':
                $this->markAsShipped();
                break;
            case 'pickup':
                $this->markAsPickedUp();
                break;
            case 'deliver':
                $this->markAsDelivered();
                break;
            case 'cancel_delivery':
                $this->cancelOrderByDelivery();
                break;
            default:
                session()->flash('error', 'Aksi tidak valid!');
        }
    }

    /**
     * Close action modal.
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->actionType = '';
        $this->confirmationNote = '';
        $this->cancelReason = '';
        $this->receiptImage = null;
        $this->pickupImage = null;
        $this->resetValidation();
    }

    /**
     * Confirm order with modal (for detailed confirmation).
     */
    public function confirmOrder()
    {
        $this->validate([
            'confirmationNote' => 'nullable|string|max:500',
            'receiptImage' => 'nullable|image|max:2048'
        ]);

        if (!$this->order->canBeConfirmed()) {
            session()->flash('error', 'Pesanan tidak dapat dikonfirmasi!');
            return;
        }

        // Handle receipt image upload
        $receiptPath = null;
        if ($this->receiptImage) {
            $receiptPath = $this->receiptImage->store('receipts', 'public');
        }

        $success = $this->order->confirmOrder(Auth::id(), $this->confirmationNote);
        
        if ($receiptPath) {
            $this->order->update(['receipt_image' => $receiptPath]);
        }

        if ($success) {
            session()->flash('success', 'Pesanan berhasil dikonfirmasi!');
            $this->closeModal();
            $this->dispatch('orderUpdated');
        } else {
            session()->flash('error', 'Gagal mengkonfirmasi pesanan!');
        }
    }



    /**
     * Cancel order.
     */
    public function cancelOrder()
    {
        $this->validate([
            'cancelReason' => 'required|string|max:500'
        ]);

        if (!$this->order->canBeCancelled()) {
            session()->flash('error', 'Pesanan tidak dapat dibatalkan!');
            return;
        }

        $success = $this->order->cancelOrder($this->cancelReason, Auth::id());

        if ($success) {
            session()->flash('success', 'Pesanan berhasil dibatalkan!');
            $this->closeModal();
            $this->dispatch('orderUpdated');
        } else {
            session()->flash('error', 'Gagal membatalkan pesanan!');
        }
    }

    /**
     * Mark order as processing.
     */
    public function markAsProcessing()
    {
        if (!$this->order->canBeProcessed()) {
            session()->flash('error', 'Pesanan tidak dapat diproses!');
            return;
        }

        $success = $this->order->markAsProcessing();

        if ($success) {
            session()->flash('success', 'Pesanan berhasil diubah ke status diproses!');
            $this->closeModal();
            $this->dispatch('orderUpdated');
        } else {
            session()->flash('error', 'Gagal mengubah status pesanan!');
        }
    }

    /**
     * Mark order as ready to ship.
     */
    public function markAsReadyToShip()
    {
        $this->validate([
            'receiptImage' => 'required|image|max:2048',
            'selectedCourierId' => 'required|exists:users,user_id'
        ]);

        if (!$this->order->canBeReadyToShip()) {
            session()->flash('error', 'Pesanan tidak dapat diubah ke status siap diantar!');
            return;
        }

        // Validate that selected user is actually a courier
        $courier = User::where('user_id', $this->selectedCourierId)->first();
        if (!$courier || !$courier->isKurir()) {
            session()->flash('error', 'Pengguna yang dipilih bukan kurir!');
            return;
        }

        // Handle receipt image upload
        $receiptPath = null;
        if ($this->receiptImage) {
            $receiptPath = $this->receiptImage->store('receipts', 'public');
        }

        $success = $this->order->markAsReadyToShip($receiptPath, $this->selectedCourierId);

        if ($success) {
            session()->flash('success', 'Pesanan berhasil ditandai siap diantar dan kurir telah ditugaskan!');
            $this->closeModal();
            $this->dispatch('orderUpdated');
        } else {
            session()->flash('error', 'Gagal mengubah status pesanan!');
        }
    }

    /**
     * Mark order as shipped.
     */
    public function markAsShipped()
    {
        if (!$this->order->canBeShipped()) {
            session()->flash('error', 'Pesanan tidak dapat dikirim!');
            return;
        }

        $success = $this->order->markAsShipped();

        if ($success) {
            // Update delivery status to in_transit if delivery record exists
            if ($this->order->delivery) {
                $this->order->delivery->update([
                    'delivery_status' => 'in_transit'
                ]);
            }

            session()->flash('success', 'Pesanan berhasil dikirim!');
            $this->closeModal();
            $this->dispatch('orderUpdated');
        } else {
            session()->flash('error', 'Gagal mengubah status pesanan!');
        }
    }

    /**
     * Mark order as picked up.
     */
    public function markAsPickedUp()
    {
        $this->validate([
            'pickupImage' => 'required|image|max:2048'
        ]);

        if (!$this->order->canBePickedUp()) {
            session()->flash('error', 'Pesanan tidak dapat dikonfirmasi diambil!');
            return;
        }

        // Handle pickup image upload
        $pickupPath = null;
        if ($this->pickupImage) {
            $pickupPath = $this->pickupImage->store('pickups', 'public');
        }

        $success = $this->order->markAsPickedUp($pickupPath);

        if ($success) {
            session()->flash('success', 'Pesanan berhasil dikonfirmasi diambil!');
            $this->closeModal();
            $this->dispatch('orderUpdated');
        } else {
            session()->flash('error', 'Gagal mengkonfirmasi pengambilan pesanan!');
        }
    }

    /**
     * Mark order as delivered.
     */
    public function markAsDelivered()
    {
        $this->validate([
            'deliveryImage' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            // Store delivery image
            $imagePath = $this->deliveryImage->store('delivery-images', 'public');
            
            // Mark order as delivered
            $this->order->markAsDelivered($imagePath);
            
            session()->flash('success', 'Pesanan berhasil diselesaikan!');
            $this->closeModal();
            $this->dispatch('orderUpdated');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyelesaikan pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Cancel order by delivery (for courier).
     */
    public function cancelOrderByDelivery()
    {
        $this->validate([
            'cancellationReason' => 'required|string|min:10|max:500',
        ]);

        try {
            // Cancel order with reason and user info
            $this->order->cancelOrder(
                $this->cancellationReason,
                auth()->user()->name . ' (Kurir)'
            );
            
            session()->flash('success', 'Pesanan berhasil dibatalkan!');
            $this->closeModal();
            $this->dispatch('orderUpdated');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal membatalkan pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Get available couriers for delivery assignment.
     */
    public function getAvailableCouriersProperty()
    {
        return User::whereHas('role', function ($query) {
            $query->where('name', 'kurir');
        })->get();
    }

    /**
     * Get available actions for current order status.
     */
    public function getAvailableActionsProperty()
    {
        $actions = [];

        if ($this->order->canBeConfirmed() && in_array($this->userRole, ['apoteker', 'admin'])) {
            $actions[] = [
                'type' => 'confirm',
                'label' => 'Konfirmasi Pesanan',
                'class' => 'btn-success',
                'icon' => 'check'
            ];
        }

        if ($this->order->canBeProcessed() && in_array($this->userRole, ['apoteker', 'admin'])) {
            $actions[] = [
                'type' => 'process',
                'label' => 'Mulai Proses',
                'class' => 'btn-primary',
                'icon' => 'cog'
            ];
        }

        if ($this->order->canBeReadyToShip() && in_array($this->userRole, ['apoteker', 'admin'])) {
            $actions[] = [
                'type' => 'ready_to_ship',
                'label' => $this->order->shipping_type === 'pickup' ? 'Siap Diambil' : 'Siap Diantar',
                'class' => 'btn-warning',
                'icon' => 'clipboard-check'
            ];
        }

        if ($this->order->canBeShipped() && in_array($this->userRole, ['kurir', 'admin'])) {
            $actions[] = [
                'type' => 'ship',
                'label' => 'Antar Pesanan',
                'class' => 'btn-accent',
                'icon' => 'truck'
            ];
        }

        if ($this->order->canBePickedUp() && in_array($this->userRole, ['apoteker', 'admin'])) {
            $actions[] = [
                'type' => 'pickup',
                'label' => 'Konfirmasi Diambil',
                'class' => 'btn-success',
                'icon' => 'check-circle'
            ];
        }

        if ($this->order->canBeDelivered() && in_array($this->userRole, ['kurir', 'admin'])) {
            $actions[] = [
                'type' => 'deliver',
                'label' => 'Selesaikan Pesanan',
                'class' => 'btn-success',
                'icon' => 'check-circle'
            ];
        }

        // Cancel delivery for shipped orders (kurir only)
        if ($this->order->status === 'shipped' && in_array($this->userRole, ['kurir', 'admin'])) {
            $actions[] = [
                'type' => 'cancel_delivery',
                'label' => 'Batalkan Pengiriman',
                'class' => 'btn-error',
                'icon' => 'x-circle'
            ];
        }

        if ($this->order->canBeCancelled()) {
            $actions[] = [
                'type' => 'cancel',
                'label' => 'Batalkan Pesanan',
                'class' => 'btn-error',
                'icon' => 'x-circle'
            ];
        }

        return $actions;
    }

    /**
     * Get order status badge color
     */
    public function getStatusBadgeColor($status)
    {
        return match($status) {
            'pending' => 'badge-warning',
            'waiting_payment' => 'badge-warning',
            'waiting_confirmation' => 'badge-info',
            'confirmed' => 'badge-info',
            'processing' => 'badge-primary',
            'ready_to_ship' => 'badge-secondary',
            'shipped' => 'badge-accent',
            'delivered' => 'badge-success',
            'cancelled' => 'badge-error',
            default => 'badge-neutral'
        };
    }

    /**
     * Get order status label
     */
    public function getStatusLabel($status)
    {
        return match($status) {
            'pending' => 'Pesanan Dibuat',
            'waiting_payment' => 'Menunggu Pembayaran',
            'waiting_confirmation' => 'Menunggu Konfirmasi',
            'confirmed' => 'Dikonfirmasi',
            'processing' => 'Diproses',
            'ready_to_ship' => 'Siap Diantar',
            'shipped' => 'Dikirim',
            'delivered' => 'Diterima',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($status)
        };
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.components.order-status-actions', [
            'availableActions' => $this->availableActions
        ]);
    }
}