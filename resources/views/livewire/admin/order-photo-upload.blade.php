<div>
    <!-- Receipt Photo Section -->
    <div class="card bg-base-200 mb-6">
        <div class="card-body">
            <h4 class="font-semibold text-lg mb-3 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Foto Konfirmasi Struk
            </h4>

            @if($order->receipt_photo)
            <!-- Display existing photo -->
            <div class="mb-4">
                <div class="relative inline-block">
                    <img
                        src="{{ Storage::url($order->receipt_photo) }}"
                        alt="Foto Struk Konfirmasi"
                        class="max-w-xs max-h-64 object-cover rounded-lg shadow-md cursor-pointer"
                        onclick="document.getElementById('receipt_photo_modal').showModal()">
                    <div class="absolute top-2 right-2">
                        <button
                            wire:click="deleteReceiptPhoto"
                            class="btn btn-error btn-sm btn-circle"
                            onclick="return confirm('Yakin ingin menghapus foto struk?')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                @if($order->receiptPhotoUploadedBy)
                <div class="mt-2 text-sm text-gray-600">
                    <p><strong>Diupload oleh:</strong> {{ $order->receiptPhotoUploadedBy->name }} ({{ ucfirst($order->receiptPhotoUploadedBy->role->name) }})</p>
                    <p><strong>Waktu upload:</strong> {{ $order->receipt_photo_uploaded_at ? $order->receipt_photo_uploaded_at->format('d M Y, H:i') : '-' }}</p>
                </div>
                @endif
            </div>

            <!-- Replace photo option -->
            <div class="collapse collapse-arrow bg-base-100">
                <input type="checkbox" />
                <div class="collapse-title text-sm font-medium">
                    Ganti Foto Struk
                </div>
                <div class="collapse-content">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Pilih foto baru</span>
                        </label>
                        <input
                            type="file"
                            wire:model="receiptPhoto"
                            accept="image/*"
                            class="file-input file-input-bordered w-full">
                        @error('receiptPhoto')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    @if($receiptPhoto)
                    <div class="mt-4">
                        <button
                            wire:click="uploadReceiptPhoto"
                            class="btn btn-primary"
                            wire:loading.attr="disabled"
                            wire:target="uploadReceiptPhoto">
                            <span wire:loading.remove wire:target="uploadReceiptPhoto">Upload Foto Baru</span>
                            <span wire:loading wire:target="uploadReceiptPhoto" class="loading loading-spinner loading-sm"></span>
                            <span wire:loading wire:target="uploadReceiptPhoto">Mengupload...</span>
                        </button>
                    </div>
                    @endif
                </div>
            </div>
            @else
            <!-- Upload new photo -->
            <div class="text-center">
                <div class="bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-gray-500 mb-2">Upload foto struk konfirmasi</p>
                    <p class="text-sm text-gray-400">Format: JPG, PNG, GIF (Max: 2MB)</p>
                </div>

                <div class="form-control">
                    <input
                        type="file"
                        wire:model="receiptPhoto"
                        accept="image/*"
                        class="file-input file-input-bordered w-full">
                    @error('receiptPhoto')
                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                @if($receiptPhoto)
                <div class="mt-4">
                    <button
                        wire:click="uploadReceiptPhoto"
                        class="btn btn-primary"
                        wire:loading.attr="disabled"
                        wire:target="uploadReceiptPhoto">
                        <span wire:loading.remove wire:target="uploadReceiptPhoto">Upload Foto Struk</span>
                        <span wire:loading wire:target="uploadReceiptPhoto" class="loading loading-spinner loading-sm"></span>
                        <span wire:loading wire:target="uploadReceiptPhoto">Mengupload...</span>
                    </button>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- Delivery Photo Section -->
    <div class="card bg-base-200">
        <div class="card-body">
            <h4 class="font-semibold text-lg mb-3 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Foto Penerimaan/Pengiriman Barang
            </h4>

            @if($order->delivery_photo)
            <!-- Display existing photo -->
            <div class="mb-4">
                <div class="relative inline-block">
                    <img
                        src="{{ Storage::url($order->delivery_photo) }}"
                        alt="Foto Pengiriman/Penerimaan"
                        class="max-w-xs max-h-64 object-cover rounded-lg shadow-md cursor-pointer"
                        onclick="document.getElementById('delivery_photo_modal').showModal()">
                    <div class="absolute top-2 right-2">
                        <button
                            wire:click="deleteDeliveryPhoto"
                            class="btn btn-error btn-sm btn-circle"
                            onclick="return confirm('Yakin ingin menghapus foto pengiriman?')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                @if($order->deliveryPhotoUploadedBy)
                <div class="mt-2 text-sm text-gray-600">
                    <p><strong>Diupload oleh:</strong> {{ $order->deliveryPhotoUploadedBy->name }} ({{ ucfirst($order->deliveryPhotoUploadedBy->role->name) }})</p>
                    <p><strong>Waktu upload:</strong> {{ $order->delivery_photo_uploaded_at ? $order->delivery_photo_uploaded_at->format('d M Y, H:i') : '-' }}</p>
                </div>
                @endif
            </div>

            <!-- Replace photo option -->
            <div class="collapse collapse-arrow bg-base-100">
                <input type="checkbox" />
                <div class="collapse-title text-sm font-medium">
                    Ganti Foto Pengiriman
                </div>
                <div class="collapse-content">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Pilih foto baru</span>
                        </label>
                        <input
                            type="file"
                            wire:model="deliveryPhoto"
                            accept="image/*"
                            class="file-input file-input-bordered w-full">
                        @error('deliveryPhoto')
                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    @if($deliveryPhoto)
                    <div class="mt-4">
                        <button
                            wire:click="uploadDeliveryPhoto"
                            class="btn btn-primary"
                            wire:loading.attr="disabled"
                            wire:target="uploadDeliveryPhoto">
                            <span wire:loading.remove wire:target="uploadDeliveryPhoto">Upload Foto Baru</span>
                            <span wire:loading wire:target="uploadDeliveryPhoto" class="loading loading-spinner loading-sm"></span>
                            <span wire:loading wire:target="uploadDeliveryPhoto">Mengupload...</span>
                        </button>
                    </div>
                    @endif
                </div>
            </div>
            @else
            <!-- Upload new photo -->
            <div class="text-center">
                <div class="bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg p-8 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <p class="text-gray-500 mb-2">Upload foto penerimaan/pengiriman barang</p>
                    <p class="text-sm text-gray-400">Format: JPG, PNG, GIF (Max: 2MB)</p>
                </div>

                <div class="form-control">
                    <input
                        type="file"
                        wire:model="deliveryPhoto"
                        accept="image/*"
                        class="file-input file-input-bordered w-full">
                    @error('deliveryPhoto')
                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                @if($deliveryPhoto)
                <div class="mt-4">
                    <button
                        wire:click="uploadDeliveryPhoto"
                        class="btn btn-primary"
                        wire:loading.attr="disabled"
                        wire:target="uploadDeliveryPhoto">
                        <span wire:loading.remove wire:target="uploadDeliveryPhoto">Upload Foto Pengiriman</span>
                        <span wire:loading wire:target="uploadDeliveryPhoto" class="loading loading-spinner loading-sm"></span>
                        <span wire:loading wire:target="uploadDeliveryPhoto">Mengupload...</span>
                    </button>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- Receipt Photo Modal -->
    @if($order->receipt_photo)
    <dialog id="receipt_photo_modal" class="modal">
        <div class="modal-box w-11/12 max-w-3xl">
            <h3 class="font-bold text-lg mb-4">Foto Struk Konfirmasi</h3>
            <div class="text-center">
                <img
                    src="{{ Storage::url($order->receipt_photo) }}"
                    alt="Foto Struk Konfirmasi"
                    class="max-w-full max-h-96 object-contain mx-auto rounded-lg">
            </div>
            @if($order->receiptPhotoUploadedBy)
            <div class="mt-4 text-sm text-gray-600">
                <p><strong>Diupload oleh:</strong> {{ $order->receiptPhotoUploadedBy->name }} ({{ ucfirst($order->receiptPhotoUploadedBy->role->name) }})</p>
                <p><strong>Waktu upload:</strong> {{ $order->receipt_photo_uploaded_at ? $order->receipt_photo_uploaded_at->format('d M Y, H:i') : '-' }}</p>
            </div>
            @endif
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn">Tutup</button>
                </form>
            </div>
        </div>
    </dialog>
    @endif

    <!-- Delivery Photo Modal -->
    @if($order->delivery_photo)
    <dialog id="delivery_photo_modal" class="modal">
        <div class="modal-box w-11/12 max-w-3xl">
            <h3 class="font-bold text-lg mb-4">Foto Penerimaan/Pengiriman Barang</h3>
            <div class="text-center">
                <img
                    src="{{ Storage::url($order->delivery_photo) }}"
                    alt="Foto Pengiriman/Penerimaan"
                    class="max-w-full max-h-96 object-contain mx-auto rounded-lg">
            </div>
            @if($order->deliveryPhotoUploadedBy)
            <div class="mt-4 text-sm text-gray-600">
                <p><strong>Diupload oleh:</strong> {{ $order->deliveryPhotoUploadedBy->name }} ({{ ucfirst($order->deliveryPhotoUploadedBy->role->name) }})</p>
                <p><strong>Waktu upload:</strong> {{ $order->delivery_photo_uploaded_at ? $order->delivery_photo_uploaded_at->format('d M Y, H:i') : '-' }}</p>
                @if($order->delivery && $order->delivery->courier)
                <p><strong>Kurir:</strong> {{ $order->delivery->courier->name }}</p>
                @endif
            </div>
            @endif
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn">Tutup</button>
                </form>
            </div>
        </div>
    </dialog>
    @endif
</div>