<?php

namespace App\Livewire\Components;

use App\Models\Order;
use App\Models\User;
use App\Models\UserLog;
use App\Services\OrderNotificationService;
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
    public string $actionReason = '';
    public string $cancellationReason = '';
    public $receiptImage;
    public $pickupImage;
    public $deliveryImage;
    public $selectedCourierId;

    protected OrderNotificationService $notificationService;

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
            $rules['cancelReason'] = 'required|string|max:500';
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
        'cancelReason.required' => 'Alasan pembatalan harus diisi.',
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

        // Initialize notification service using dependency injection
        $this->notificationService = app(OrderNotificationService::class);

        // Validate that order exists and has required data
        if (!$this->order || !$this->order->exists) {
            throw new \Exception('Order data is invalid or missing');
        }
    }

    /**
     * Ensure notification service is initialized.
     */
    private function ensureNotificationServiceInitialized(): void
    {
        if (!isset($this->notificationService)) {
            $this->notificationService = app(OrderNotificationService::class);
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
        $this->actionReason = '';
        $this->actionReason = '';
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
            case 'delete':
                $this->deleteOrder();
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
                // Send email notification to customer
                try {
                    $this->ensureNotificationServiceInitialized();
                    $this->notificationService->sendOrderConfirmation($this->order);
                } catch (\Exception $emailError) {
                    \Log::warning('Failed to send order confirmation email', [
                        'order_id' => $this->order->order_id,
                        'error' => $emailError->getMessage()
                    ]);
                }

                $this->closeModal();
                $this->dispatch('orderUpdated');
                $this->dispatch('show-notification', [
                    'type' => 'success',
                    'message' => 'Pesanan berhasil dikonfirmasi!',
                    'autoHide' => true,
                    'delay' => 4000
                ]);
                // Auto refresh halaman setelah 1 detik
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
            \Log::error('Error in confirmOrder', [
                'order_id' => $this->order->order_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Provide more specific error messages
            $errorMessage = 'Terjadi kesalahan sistem. Silakan coba lagi.';
            
            if (str_contains($e->getMessage(), 'confirmation_note')) {
                $errorMessage = 'Gagal menyimpan catatan konfirmasi. Periksa koneksi database.';
            } elseif (str_contains($e->getMessage(), 'status')) {
                $errorMessage = 'Gagal mengubah status pesanan. Periksa status pesanan saat ini.';
            } elseif (str_contains($e->getMessage(), 'receipt_image')) {
                $errorMessage = 'Gagal menyimpan gambar struk. Periksa file yang diupload.';
            }

            $this->dispatch('show-notification', [
                'type' => 'error',
                'message' => $errorMessage,
                'autoHide' => true,
                'delay' => 5000
            ]);
        }
    }



    /**
     * Cancel order with reason.
     */
    public function cancelOrder()
    {
        $this->validate([
            'actionReason' => 'required|string|min:10|max:500'
        ], [
            'actionReason.required' => 'Alasan pembatalan harus diisi',
            'actionReason.min' => 'Alasan pembatalan minimal 10 karakter',
            'actionReason.max' => 'Alasan pembatalan maksimal 500 karakter'
        ]);

        if (!$this->order->canBeCancelled()) {
            $this->dispatch('show-notification', [
                'type' => 'error',
                'message' => 'Pesanan tidak dapat dibatalkan pada status ini!',
                'autoHide' => true,
                'delay' => 5000
            ]);
            return;
        }

        try {
            $success = $this->order->cancelOrder(
                $this->actionReason,
                auth()->id(),
                true // Process refund automatically
            );

            if ($success) {
                // Send email notification to customer
                try {
                    $this->ensureNotificationServiceInitialized();
                    $this->notificationService->sendCancelledNotification($this->order, $this->actionReason);
                } catch (\Exception $emailError) {
                    \Log::warning('Failed to send order cancellation email', [
                        'order_id' => $this->order->order_id,
                        'error' => $emailError->getMessage()
                    ]);
                }

                $this->dispatch('show-notification', [
                    'type' => 'success',
                    'message' => 'Pesanan berhasil dibatalkan!',
                    'autoHide' => true,
                    'delay' => 5000
                ]);
                
                $this->dispatch('orderUpdated');
                $this->closeModal();
            } else {
                $this->dispatch('show-notification', [
                    'type' => 'error',
                    'message' => 'Gagal membatalkan pesanan. Silakan coba lagi.',
                    'autoHide' => true,
                    'delay' => 5000
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error cancelling order', [
                'order_id' => $this->order->order_id,
                'error' => $e->getMessage()
            ]);
            
            $this->dispatch('show-notification', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan saat membatalkan pesanan.',
                'autoHide' => true,
                'delay' => 5000
            ]);
        }
    }

    /**
     * Delete order permanently from database.
     */
    public function deleteOrder()
    {
        $this->validate([
            'actionReason' => 'required|string|min:10|max:500'
        ], [
            'actionReason.required' => 'Alasan penghapusan harus diisi',
            'actionReason.min' => 'Alasan penghapusan minimal 10 karakter',
            'actionReason.max' => 'Alasan penghapusan maksimal 500 karakter'
        ]);

        if (!$this->order->canBeDeleted()) {
            $this->dispatch('show-notification', [
                'type' => 'error',
                'message' => 'Pesanan tidak dapat dihapus pada status ini! Hanya pesanan dengan status pending, waiting_payment, waiting_confirmation, atau cancelled yang dapat dihapus.',
                'autoHide' => true,
                'delay' => 7000
            ]);
            return;
        }

        try {
            $success = $this->order->deleteOrder(
                $this->actionReason,
                auth()->id()
            );

            if ($success) {
                $this->dispatch('show-notification', [
                    'type' => 'success',
                    'message' => 'Pesanan berhasil dihapus dari sistem!',
                    'autoHide' => true,
                    'delay' => 5000
                ]);
                
                // Redirect to order management page since order is deleted
                return redirect()->route('admin.orders');
            } else {
                $this->dispatch('show-notification', [
                    'type' => 'error',
                    'message' => 'Gagal menghapus pesanan. Silakan coba lagi.',
                    'autoHide' => true,
                    'delay' => 5000
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error deleting order', [
                'order_id' => $this->order->order_id,
                'error' => $e->getMessage()
            ]);
            
            $this->dispatch('show-notification', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan saat menghapus pesanan.',
                'autoHide' => true,
                'delay' => 5000
            ]);
        }
    }

    /**
     * Mark order as processing.
     */
    public function markAsProcessing()
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

            if (!$this->order->canBeProcessed()) {
                $this->dispatch('show-notification', [
                    'type' => 'error',
                    'message' => 'Pesanan tidak dapat diproses! Status saat ini: ' . $this->order->status,
                    'autoHide' => true,
                    'delay' => 5000
                ]);
                return;
            }

            $success = $this->order->markAsProcessing();

            if ($success) {
                // Send email notification to customer
                try {
                    $this->ensureNotificationServiceInitialized();
                    $this->notificationService->sendProcessingNotification($this->order);
                } catch (\Exception $emailError) {
                    \Log::warning('Failed to send order processing email', [
                        'order_id' => $this->order->order_id,
                        'error' => $emailError->getMessage()
                    ]);
                }

                $this->closeModal();
                $this->dispatch('orderUpdated');
                $this->dispatch('show-notification', [
                    'type' => 'success',
                    'message' => 'Pesanan berhasil diubah ke status diproses!',
                    'autoHide' => true,
                    'delay' => 4000
                ]);
                // Auto refresh halaman setelah 1 detik
                $this->dispatch('auto-refresh-page', ['delay' => 1000]);
            } else {
                $this->dispatch('show-notification', [
                    'type' => 'error',
                    'message' => 'Gagal mengubah status pesanan! Periksa log untuk detail error.',
                    'autoHide' => true,
                    'delay' => 5000
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error in markAsProcessing', [
                'order_id' => $this->order->order_id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Provide more specific error messages
            $errorMessage = 'Terjadi kesalahan sistem saat memproses pesanan.';
            
            if (str_contains($e->getMessage(), 'processing_at')) {
                $errorMessage = 'Gagal menyimpan waktu proses. Periksa koneksi database.';
            } elseif (str_contains($e->getMessage(), 'status')) {
                $errorMessage = 'Gagal mengubah status pesanan. Periksa status pesanan saat ini.';
            }

            $this->dispatch('show-notification', [
                'type' => 'error',
                'message' => $errorMessage,
                'autoHide' => true,
                'delay' => 5000
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
                // Send email notification to customer
                try {
                    $this->ensureNotificationServiceInitialized();
                    if ($this->order->shipping_type === 'delivery') {
                        $this->notificationService->sendReadyToShipNotification($this->order);
                    } else {
                        $this->notificationService->sendReadyForPickupNotification($this->order);
                    }
                } catch (\Exception $emailError) {
                    \Log::warning('Failed to send ready to ship/pickup email', [
                        'order_id' => $this->order->order_id,
                        'shipping_type' => $this->order->shipping_type,
                        'error' => $emailError->getMessage()
                    ]);
                }

                // Log activity for courier assignment
                if ($courierId) {
                    $courier = User::find($courierId);
                    UserLog::logDeliveryActivity(
                        Auth::id(),
                        'courier_assigned',
                        "Kurir {$courier->name} ditugaskan untuk mengantar pesanan #{$this->order->order_id}",
                        [
                            'order_id' => $this->order->order_id,
                            'courier_id' => $courierId,
                            'courier_name' => $courier->name,
                            'receipt_image' => $receiptPath,
                            'shipping_type' => $this->order->shipping_type
                        ]
                    );
                }

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
                    'status' => 'in_transit'
                ]);
            }

            // Log shipping activity
            $courierInfo = $this->order->delivery && $this->order->delivery->courier ? 
                $this->order->delivery->courier->name : 'Tidak diketahui';
            
            UserLog::logDeliveryActivity(
                Auth::id(),
                'order_shipped',
                "Pesanan #{$this->order->order_id} telah dikirim oleh kurir {$courierInfo}",
                [
                    'order_id' => $this->order->order_id,
                    'courier_name' => $courierInfo,
                    'shipping_type' => $this->order->shipping_type,
                    'delivery_status' => 'in_transit'
                ]
            );

            // Send email notification to customer
            try {
                $this->ensureNotificationServiceInitialized();
                $this->notificationService->sendShippedNotification($this->order);
            } catch (\Exception $emailError) {
                \Log::warning('Failed to send order shipped email', [
                    'order_id' => $this->order->order_id,
                    'error' => $emailError->getMessage()
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
            // Auto refresh halaman setelah 1 detik
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
            // Send email notification to customer
            try {
                $this->ensureNotificationServiceInitialized();
                $this->notificationService->sendCompletedNotification($this->order);
            } catch (\Exception $emailError) {
                \Log::warning('Failed to send order completion email', [
                    'order_id' => $this->order->order_id,
                    'error' => $emailError->getMessage()
                ]);
            }

            session()->flash('success', 'Pesanan berhasil dikonfirmasi diambil!');
            $this->closeModal();
            $this->dispatch('orderUpdated');
            $this->dispatch('show-notification', [
                'type' => 'success',
                'message' => 'Pesanan berhasil dikonfirmasi diambil!',
                'autoHide' => true,
                'delay' => 4000
            ]);
            // Auto refresh halaman setelah 1 detik
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

            // Log delivery completion with proof
            $courierInfo = Auth::user()->name;
            UserLog::logDeliveryActivity(
                Auth::id(),
                'order_delivered',
                "Pesanan #{$this->order->order_id} telah berhasil diantar oleh kurir {$courierInfo} dengan bukti pengiriman",
                [
                    'order_id' => $this->order->order_id,
                    'courier_name' => $courierInfo,
                    'delivery_proof' => $imagePath,
                    'shipping_type' => $this->order->shipping_type,
                    'delivery_status' => 'delivered'
                ]
            );

            // Send email notification to customer
            try {
                $this->ensureNotificationServiceInitialized();
                $this->notificationService->sendCompletedNotification($this->order);
            } catch (\Exception $emailError) {
                \Log::warning('Failed to send order completion email', [
                    'order_id' => $this->order->order_id,
                    'error' => $emailError->getMessage()
                ]);
            }

            session()->flash('success', 'Pesanan berhasil diselesaikan!');
            $this->closeModal();
            $this->dispatch('orderUpdated');
            $this->dispatch('show-notification', [
                'type' => 'success',
                'message' => 'Pesanan berhasil diselesaikan!',
                'autoHide' => true,
                'delay' => 4000
            ]);
            // Auto refresh halaman setelah 1 detik
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

            // Log delivery cancellation
            UserLog::logDeliveryActivity(
                Auth::id(),
                'delivery_cancelled',
                "Pengiriman pesanan #{$this->order->order_id} dibatalkan oleh kurir " . Auth::user()->name,
                [
                    'order_id' => $this->order->order_id,
                    'courier_name' => Auth::user()->name,
                    'cancellation_reason' => $this->cancellationReason,
                    'shipping_type' => $this->order->shipping_type
                ]
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
            // Auto refresh halaman setelah 1 detik
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

        // Delete order for admin only on specific statuses
        if ($this->order->canBeDeleted() && in_array($this->userRole, ['admin'])) {
            $actions[] = [
                'type' => 'delete',
                'label' => 'Hapus Pesanan',
                'class' => 'btn-error',
                'icon' => 'trash'
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
        // Check if payment is expired for waiting_payment status
        if ($status === 'waiting_payment' && $this->order->isPaymentExpired()) {
            return 'Pesanan Expired';
        }

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
