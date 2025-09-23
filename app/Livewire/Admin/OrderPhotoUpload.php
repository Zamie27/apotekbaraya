<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Helpers\FileUploadHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class OrderPhotoUpload extends Component
{
    use WithFileUploads;

    public Order $order;
    public $receiptPhoto;
    public $deliveryPhoto;
    public $uploadType = 'receipt'; // 'receipt' or 'delivery'
    public $isUploading = false;

    protected $rules = [
        'receiptPhoto' => 'nullable|image|max:2048', // Max 2MB
        'deliveryPhoto' => 'nullable|image|max:2048', // Max 2MB
    ];

    protected $messages = [
        'receiptPhoto.image' => 'File harus berupa gambar.',
        'receiptPhoto.max' => 'Ukuran file maksimal 2MB.',
        'deliveryPhoto.image' => 'File harus berupa gambar.',
        'deliveryPhoto.max' => 'Ukuran file maksimal 2MB.',
    ];

    /**
     * Mount the component with order data.
     */
    public function mount(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Upload receipt confirmation photo.
     */
    public function uploadReceiptPhoto()
    {
        $this->validate([
            'receiptPhoto' => 'required|image|max:2048'
        ]);

        $this->isUploading = true;

        try {
            // Delete old photo if exists
            if ($this->order->receipt_photo) {
                Storage::disk('public')->delete($this->order->receipt_photo);
            }

            // Store new photo with structured filename
            $path = FileUploadHelper::storeWithStructuredName(
                $this->receiptPhoto,
                FileUploadHelper::TYPE_STRUK,
                $this->order->order_id,
                'order-photos/receipts',
                'public'
            );

            // Check if upload was successful
            if (!$path) {
                throw new \Exception('Gagal menyimpan file foto struk');
            }

            // Update order record
            $this->order->update([
                'receipt_photo' => $path,
                'receipt_photo_uploaded_by' => Auth::id(),
                'receipt_photo_uploaded_at' => now(),
            ]);

            // Reset file input
            $this->receiptPhoto = null;

            // Emit success event
            $this->dispatch('show-notification', [
                'type' => 'success',
                'message' => 'Foto struk konfirmasi berhasil diupload!',
                'autoHide' => true,
                'delay' => 3000
            ]);

            // Refresh order data
            $this->order->refresh();

        } catch (\Exception $e) {
            $this->dispatch('show-notification', [
                'type' => 'error',
                'message' => 'Gagal mengupload foto: ' . $e->getMessage(),
                'autoHide' => true,
                'delay' => 5000
            ]);
        } finally {
            $this->isUploading = false;
        }
    }

    /**
     * Upload delivery/receipt photo.
     */
    public function uploadDeliveryPhoto()
    {
        $this->validate([
            'deliveryPhoto' => 'required|image|max:2048'
        ]);

        $this->isUploading = true;

        try {
            // Delete old photo if exists
            if ($this->order->delivery_photo) {
                Storage::disk('public')->delete($this->order->delivery_photo);
            }

            // Store new photo with structured filename
            // Determine file type based on shipping type
            $fileType = $this->order->shipping_type === 'pickup' 
                ? FileUploadHelper::TYPE_PENGAMBILAN 
                : FileUploadHelper::TYPE_PENGANTARAN;
                
            $path = FileUploadHelper::storeWithStructuredName(
                $this->deliveryPhoto,
                $fileType,
                $this->order->order_number,
                'order-photos/delivery',
                'public'
            );

            // Check if upload was successful
            if (!$path) {
                throw new \Exception('Gagal menyimpan file foto pengiriman/pengambilan');
            }

            // Update order record
            $this->order->update([
                'delivery_photo' => $path,
                'delivery_photo_uploaded_by' => Auth::id(),
                'delivery_photo_uploaded_at' => now(),
            ]);

            // Reset file input
            $this->deliveryPhoto = null;

            // Emit success event
            $this->dispatch('show-notification', [
                'type' => 'success',
                'message' => 'Foto pengiriman/penerimaan berhasil diupload!',
                'autoHide' => true,
                'delay' => 3000
            ]);

            // Refresh order data
            $this->order->refresh();

        } catch (\Exception $e) {
            $this->dispatch('show-notification', [
                'type' => 'error',
                'message' => 'Gagal mengupload foto: ' . $e->getMessage(),
                'autoHide' => true,
                'delay' => 5000
            ]);
        } finally {
            $this->isUploading = false;
        }
    }

    /**
     * Delete receipt photo.
     */
    public function deleteReceiptPhoto()
    {
        try {
            if ($this->order->receipt_photo) {
                Storage::disk('public')->delete($this->order->receipt_photo);
                
                $this->order->update([
                    'receipt_photo' => null,
                    'receipt_photo_uploaded_by' => null,
                    'receipt_photo_uploaded_at' => null,
                ]);

                $this->dispatch('show-notification', [
                    'type' => 'success',
                    'message' => 'Foto struk berhasil dihapus!',
                    'autoHide' => true,
                    'delay' => 3000
                ]);

                $this->order->refresh();
            }
        } catch (\Exception $e) {
            $this->dispatch('show-notification', [
                'type' => 'error',
                'message' => 'Gagal menghapus foto: ' . $e->getMessage(),
                'autoHide' => true,
                'delay' => 5000
            ]);
        }
    }

    /**
     * Delete delivery photo.
     */
    public function deleteDeliveryPhoto()
    {
        try {
            if ($this->order->delivery_photo) {
                Storage::disk('public')->delete($this->order->delivery_photo);
                
                $this->order->update([
                    'delivery_photo' => null,
                    'delivery_photo_uploaded_by' => null,
                    'delivery_photo_uploaded_at' => null,
                ]);

                $this->dispatch('show-notification', [
                    'type' => 'success',
                    'message' => 'Foto pengiriman berhasil dihapus!',
                    'autoHide' => true,
                    'delay' => 3000
                ]);

                $this->order->refresh();
            }
        } catch (\Exception $e) {
            $this->dispatch('show-notification', [
                'type' => 'error',
                'message' => 'Gagal menghapus foto: ' . $e->getMessage(),
                'autoHide' => true,
                'delay' => 5000
            ]);
        }
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.admin.order-photo-upload');
    }
}
