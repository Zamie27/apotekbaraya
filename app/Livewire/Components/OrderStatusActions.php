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
    public string $cancellationReason = '';
    public $receiptImage;
    public $pickupImage;
    public $deliveryImage;
    public $selectedCourierId;

    /**
     * Validation rules for the component
     */
    protected function rules()
    {
        $rules = [
            'confirmationNote' => 'nullable|string|max:500',
        ];

        // Add validation rules based on action type
        if ($this->actionType === 'cancel') {
            $rules['cancellationReason'] = 'required|string|max:500';
        }

        // Add receipt image validation when action type is ready_to_ship
        if ($this->actionType === 'ready_to_ship') {
            $rules['receiptImage'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
            // Only require courier selection for delivery orders
            if (isset($this->order) && $this->order && isset($this->order->shipping_type) && $this->order->shipping_type === 'delivery') {
                $rules['selectedCourierId'] = 'required|exists:users,user_id';
            }
        } elseif ($this->actionType === 'confirm' && isset($this->order) && $this->order && isset($this->order->status) && $this->order->status === 'processing') {
            // For processing status (ready_to_ship), receipt image is required
            $rules['receiptImage'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
        }

        // Pickup image is required for pickup confirmation
        if ($this->actionType === 'pickup') {
            $rules['pickupImage'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
        }

        // Delivery image is required for delivery confirmation
        if ($this->actionType === 'deliver') {
            $rules['deliveryImage'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
        }

        // Cancellation reason is required for delivery cancellation
        if ($this->actionType === 'cancel_delivery') {
            $rules['cancellationReason'] = 'required|string|min:10|max:500';
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
        'deliveryImage.required' => 'Foto bukti pengiriman harus diupload.',
        'deliveryImage.image' => 'File harus berupa gambar.',
        'deliveryImage.max' => 'Ukuran file maksimal 2MB.',
        'cancellationReason.required' => 'Alasan pembatalan harus diisi.',
        'cancellationReason.min' => 'Alasan pembatalan minimal 10 karakter.',
        'cancellationReason.max' => 'Alasan pembatalan maksimal 500 karakter.',
        'selectedCourierId.required_if' => 'Kurir harus dipilih untuk pesanan yang akan diantar.',
        'selectedCourierId.exists' => 'Kurir yang dipilih tidak valid.'
    ];

    /**
     * Mount the component.
     */
    public function mount(Order $order, string $userRole = 'apoteker')
    {
        // Ensure order is properly loaded with relationships
        $this->order = $order->load(['items.product', 'user', 'delivery']);
        $this->userRole = $userRole;

        // Validate that order exists and has required data
        if (!$this->order || !$this->order->exists) {
            throw new \Exception('Order data is invalid or missing');
        }
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
        $this->cancellationReason = '';
        $this->receiptImage = null;
        $this->pickupImage = null;
        $this->deliveryImage = null;
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
     * Reset loading state to ensure loading animation stops
     */
    public function resetLoadingState()
    {
        // Force Livewire to reset loading state by dispatching a browser event
        $this->dispatch('reset-loading-state');
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
        $this->cancellationReason = '';
        $this->receiptImage = null;
        $this->pickupImage = null;
        $this->deliveryImage = null;
        $this->resetValidation();
        $this->resetLoadingState();
    }

    /**
     * Confirm order with modal (for detailed confirmation).
     */
    public function confirmOrder()
    {
        try {
            // Check if order exists
            if (!isset($this->order) || !$this->order) {
                $this->dispatch('show-notification', [
                    'type' => 'error',
                    'message' => 'Data pesanan tidak ditemukan!',
                    'autoHide' => true,
                    'delay' => 4000
                ]);
                return;
            }

            $this->validate([
                'confirmationNote' => 'nullable|string|max:500',
                'receiptImage' => 'nullable|image|max:2048'
            ]);

            if (!$this->order->canBeConfirmed()) {
                $this->dispatch('show-notification', [
                    'type' => 'error',
                    'message' => 'Pesanan tidak dapat dikonfirmasi!',
                    'autoHide' => true,
                    'delay' => 4000
                ]);
                return;
            }

            // Handle receipt image upload
            $receiptPath = null;
            if ($this->receiptImage && $this->receiptImage->isValid()) {
                try {
                    $receiptPath = $this->receiptImage->store('receipts', 'public');
                } catch (\Exception $uploadError) {
                    \Log::error('Error uploading receipt image in confirmOrder: ' . $uploadError->getMessage());
                    $this->dispatch('show-notification', [
                        'type' => 'error',
                        'message' => 'Gagal mengupload struk. Silakan coba lagi.',
                        'autoHide' => true,
                        'delay' => 4000
                    ]);
                    return;
                }
            }

            $success = $this->order->confirmOrder(Auth::id(), $this->confirmationNote);

            if ($receiptPath) {
                $this->order->update(['receipt_image' => $receiptPath]);
            }

            if ($success) {
                $this->closeModal();
                $this->dispatch('orderUpdated');
                $this->dispatch('show-notification', [
                    'type' => 'success',
                    'message' => 'Pesanan berhasil dikonfirmasi!',
                    'autoHide' => true,
                    'delay' => 4000
                ]);
                // Auto refresh halaman setelah 1  detik
                $this->dispatch('auto-refresh-page', ['delay' => 1000]);
            } else {
                $this->dispatch('show-notification', [
                    'type' => 'error',
                    'message' => 'Gagal mengkonfirmasi pesanan!',
                    'autoHide' => true,
                    'delay' => 4000
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors - don't close modal, let user fix validation errors
            $errors = $e->validator->errors()->all();
            $this->dispatch('show-notification', [
                'type' => 'error',
                'message' => implode(' ', $errors),
                'autoHide' => true,
                'delay' => 5000
            ]);
        } catch (\Exception $e) {
            // Handle other errors - close modal and show error
            $this->closeModal();
            \Log::error('Error in confirmOrder: ' . $e->getMessage());
            $this->dispatch('show-notification', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.',
                'autoHide' => true,
                'delay' => 4000
            ]);
        }
    }



    /**
     * Cancel order.
     */
    public function cancelOrder()
    {
        // Check if order exists
        if (!isset($this->order) || !$this->order) {
            session()->flash('error', 'Data pesanan tidak ditemukan!');
            return;
        }

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
            $this->dispatch('show-notification', [
                'type' => 'success',
                'message' => 'Pesanan berhasil dibatalkan!',
                'autoHide' => true,
                'delay' => 4000
            ]);
            // Auto refresh halaman setelah 1  detik
            $this->dispatch('auto-refresh-page', ['delay' => 1000]);
            // Auto refresh halaman setelah 1  detik
            $this->dispatch('auto-refresh-page', ['delay' => 1000]);
        } else {
            session()->flash('error', 'Gagal membatalkan pesanan!');
            $this->dispatch('show-notification', [
                'type' => 'error',
                'message' => 'Gagal membatalkan pesanan!',
                'autoHide' => true,
                'delay' => 4000
            ]);
        }
    }

    /**
     * Mark order as processing.
     */
    public function markAsProcessing()
    {
        // Check if order exists
        if (!isset($this->order) || !$this->order) {
            session()->flash('error', 'Data pesanan tidak ditemukan!');
            return;
        }

        if (!$this->order->canBeProcessed()) {
            session()->flash('error', 'Pesanan tidak dapat diproses!');
            return;
        }

        $success = $this->order->markAsProcessing();

        if ($success) {
            session()->flash('success', 'Pesanan berhasil diubah ke status diproses!');
            $this->closeModal();
            $this->dispatch('orderUpdated');
            $this->dispatch('show-notification', [
                'type' => 'success',
                'message' => 'Pesanan berhasil diubah ke status diproses!',
                'autoHide' => true,
                'delay' => 4000
            ]);
            // Auto refresh halaman setelah 1  detik
            $this->dispatch('auto-refresh-page', ['delay' => 1000]);
        } else {
            session()->flash('error', 'Gagal mengubah status pesanan!');
            $this->dispatch('show-notification', [
                'type' => 'error',
                'message' => 'Gagal mengubah status pesanan!',
                'autoHide' => true,
                'delay' => 4000
            ]);
        }
    }

    /**
     * Mark order as ready to ship.
     */
    public function markAsReadyToShip()
    {
        try {
            // Check if order exists
            if (!isset($this->order) || !$this->order) {
                $this->dispatch('show-notification', [
                    'type' => 'error',
                    'message' => 'Data pesanan tidak ditemukan!',
                    'autoHide' => true,
                    'delay' => 4000
                ]);
                return;
            }

            // Validate input using the rules method
            $this->validate();

            // Check if order can be marked as ready to ship
            if (!$this->order->canBeReadyToShip()) {
                $this->dispatch('show-notification', [
                    'type' => 'error',
                    'message' => 'Pesanan tidak dapat diubah ke status siap diantar!',
                    'autoHide' => true,
                    'delay' => 4000
                ]);
                return;
            }

            // Validate that selected user is actually a courier (only for delivery orders)
            if (isset($this->order->shipping_type) && $this->order->shipping_type === 'delivery') {
                $courier = User::where('user_id', $this->selectedCourierId)->first();
                if (!$courier || !$courier->isKurir()) {
                    $this->dispatch('show-notification', [
                        'type' => 'error',
                        'message' => 'Pengguna yang dipilih bukan kurir!',
                        'autoHide' => true,
                        'delay' => 4000
                    ]);
                    return;
                }
            }

            // Handle receipt image upload
            $receiptPath = null;
            if ($this->receiptImage && $this->receiptImage->isValid()) {
                try {
                    $receiptPath = $this->receiptImage->store('receipts', 'public');
                } catch (\Exception $uploadError) {
                    \Log::error('Error uploading receipt image: ' . $uploadError->getMessage());
                    $this->dispatch('show-notification', [
                        'type' => 'error',
                        'message' => 'Gagal mengupload struk. Silakan coba lagi.',
                        'autoHide' => true,
                        'delay' => 4000
                    ]);
                    return;
                }
            }

            // Mark order as ready to ship
            $courierId = $this->order->shipping_type === 'delivery' ? $this->selectedCourierId : null;
            $success = $this->order->markAsReadyToShip($receiptPath, $courierId);

            if ($success) {
                $this->closeModal();
                $this->dispatch('orderUpdated');
                $this->dispatch('show-notification', [
                    'type' => 'success',
                    'message' => 'Pesanan berhasil ditandai siap diantar dan kurir telah ditugaskan!',
                    'autoHide' => true,
                    'delay' => 4000
                ]);
                // Auto refresh halaman setelah 1 detik
                $this->dispatch('auto-refresh-page', ['delay' => 1000]);
            } else {
                $this->dispatch('show-notification', [
                    'type' => 'error',
                    'message' => 'Gagal mengubah status pesanan!',
                    'autoHide' => true,
                    'delay' => 4000
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors - don't close modal, let user fix validation errors
            $errors = $e->validator->errors()->all();
            $this->dispatch('show-notification', [
                'type' => 'error',
                'message' => implode(' ', $errors),
                'autoHide' => true,
                'delay' => 5000
            ]);
        } catch (\Exception $e) {
            // Handle other errors - close modal and show error
            $this->closeModal();
            \Log::error('Error in markAsReadyToShip: ' . $e->getMessage());
            $this->dispatch('show-notification', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.',
                'autoHide' => true,
                'delay' => 4000
            ]);
        }
    }

    /**
     * Mark order as shipped.
     */
    public function markAsShipped()
    {
        // Check if order exists
        if (!isset($this->order) || !$this->order) {
            session()->flash('error', 'Data pesanan tidak ditemukan!');
            return;
        }

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
            $this->dispatch('show-notification', [
                'type' => 'success',
                'message' => 'Pesanan berhasil dikirim!',
                'autoHide' => true,
                'delay' => 4000
            ]);
            // Auto refresh halaman setelah 1  detik
            $this->dispatch('auto-refresh-page', ['delay' => 1000]);
        } else {
            session()->flash('error', 'Gagal mengubah status pesanan!');
            $this->dispatch('show-notification', [
                'type' => 'error',
                'message' => 'Gagal mengubah status pesanan!',
                'autoHide' => true,
                'delay' => 4000
            ]);
        }
    }

    /**
     * Mark order as picked up.
     */
    public function markAsPickedUp()
    {
        // Check if order exists
        if (!isset($this->order) || !$this->order) {
            session()->flash('error', 'Data pesanan tidak ditemukan!');
            return;
        }

        $this->validate([
            'pickupImage' => 'required|image|max:2048'
        ]);

        if (!$this->order->canBePickedUp()) {
            session()->flash('error', 'Pesanan tidak dapat dikonfirmasi diambil!');
            return;
        }

        // Handle pickup image upload
        $pickupPath = null;
        if ($this->pickupImage && $this->pickupImage->isValid()) {
            try {
                $pickupPath = $this->pickupImage->store('pickups', 'public');
            } catch (\Exception $uploadError) {
                \Log::error('Error uploading pickup image: ' . $uploadError->getMessage());
                $this->dispatch('show-notification', [
                    'type' => 'error',
                    'message' => 'Gagal mengupload foto bukti pengambilan. Silakan coba lagi.',
                    'autoHide' => true,
                    'delay' => 4000
                ]);
                return;
            }
        }

        $success = $this->order->markAsPickedUp($pickupPath);

        if ($success) {
            session()->flash('success', 'Pesanan berhasil dikonfirmasi diambil!');
            $this->closeModal();
            $this->dispatch('orderUpdated');
            $this->dispatch('show-notification', [
                'type' => 'success',
                'message' => 'Pesanan berhasil dikonfirmasi diambil!',
                'autoHide' => true,
                'delay' => 4000
            ]);
            // Auto refresh halaman setelah 1  detik
            $this->dispatch('auto-refresh-page', ['delay' => 1000]);
        } else {
            session()->flash('error', 'Gagal mengkonfirmasi pengambilan pesanan!');
            $this->dispatch('show-notification', [
                'type' => 'error',
                'message' => 'Gagal mengkonfirmasi pengambilan pesanan!',
                'autoHide' => true,
                'delay' => 4000
            ]);
        }
    }

    /**
     * Mark order as delivered.
     */
    public function markAsDelivered()
    {
        try {
            // Check if order exists
            if (!isset($this->order) || !$this->order) {
                $this->dispatch('show-notification', [
                    'type' => 'error',
                    'message' => 'Data pesanan tidak ditemukan!',
                    'autoHide' => true,
                    'delay' => 4000
                ]);
                return;
            }

            // Validate input using the rules method
            $this->validate();
            // Store delivery image
            $imagePath = null;
            if ($this->deliveryImage && $this->deliveryImage->isValid()) {
                try {
                    $imagePath = $this->deliveryImage->store('delivery-images', 'public');
                } catch (\Exception $uploadError) {
                    \Log::error('Error uploading delivery image: ' . $uploadError->getMessage());
                    $this->dispatch('show-notification', [
                        'type' => 'error',
                        'message' => 'Gagal mengupload foto bukti pengiriman. Silakan coba lagi.',
                        'autoHide' => true,
                        'delay' => 4000
                    ]);
                    return;
                }
            }

            // Mark order as delivered
            $this->order->markAsDelivered($imagePath);

            session()->flash('success', 'Pesanan berhasil diselesaikan!');
            $this->closeModal();
            $this->dispatch('orderUpdated');
            $this->dispatch('show-notification', [
                'type' => 'success',
                'message' => 'Pesanan berhasil diselesaikan!',
                'autoHide' => true,
                'delay' => 4000
            ]);
            // Auto refresh halaman setelah 1  detik
            $this->dispatch('auto-refresh-page', ['delay' => 1000]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            $this->closeModal();
            $errors = $e->validator->errors()->all();
            $this->dispatch('show-notification', [
                'type' => 'error',
                'message' => implode(' ', $errors),
                'autoHide' => true,
                'delay' => 5000
            ]);
        } catch (\Exception $e) {
            $this->closeModal();
            \Log::error('Error in markAsDelivered: ' . $e->getMessage());
            $this->dispatch('show-notification', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.',
                'autoHide' => true,
                'delay' => 4000
            ]);
        }
    }

    /**
     * Cancel order by delivery (for courier).
     */
    public function cancelOrderByDelivery()
    {
        try {
            // Check if order exists
            if (!isset($this->order) || !$this->order) {
                $this->dispatch('show-notification', [
                    'type' => 'error',
                    'message' => 'Data pesanan tidak ditemukan!',
                    'autoHide' => true,
                    'delay' => 4000
                ]);
                return;
            }

            // Validate input using the rules method
            $this->validate();
            // Cancel order with reason and user info
            $this->order->cancelOrder(
                $this->cancellationReason,
                auth()->user()->name . ' (Kurir)'
            );

            session()->flash('success', 'Pesanan berhasil dibatalkan!');
            $this->closeModal();
            $this->dispatch('orderUpdated');
            $this->dispatch('show-notification', [
                'type' => 'success',
                'message' => 'Pesanan berhasil dibatalkan!',
                'autoHide' => true,
                'delay' => 4000
            ]);
            // Auto refresh halaman setelah 1  detik
            $this->dispatch('auto-refresh-page', ['delay' => 1000]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            $this->closeModal();
            $errors = $e->validator->errors()->all();
            $this->dispatch('show-notification', [
                'type' => 'error',
                'message' => implode(' ', $errors),
                'autoHide' => true,
                'delay' => 5000
            ]);
        } catch (\Exception $e) {
            $this->closeModal();
            \Log::error('Error in cancelOrderByDelivery: ' . $e->getMessage());
            $this->dispatch('show-notification', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.',
                'autoHide' => true,
                'delay' => 4000
            ]);
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
        return match ($status) {
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
        return match ($status) {
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
