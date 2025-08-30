<div class="order-status-actions">
    <!-- Action Buttons -->
    @if(count($availableActions) > 0)
        <div class="flex flex-wrap gap-2">
            @foreach($availableActions as $action)
                <!-- All actions now use modal for confirmation -->
                <button 
                    wire:click="openActionModal('{{ $action['type'] }}')"
                    class="btn {{ $action['class'] }} btn-sm"
                >
                    @include('components.icons.' . $action['icon'], ['class' => 'w-4 h-4'])
                    {{ $action['label'] }}
                </button>
            @endforeach
        </div>
    @else
        <div class="text-sm text-base-content/60">
            Tidak ada aksi yang tersedia untuk status ini.
        </div>
    @endif

    <!-- Action Modal -->
    @if($showModal)
        <div class="modal modal-open">
            <div class="modal-box max-w-5xl">
                <h3 class="font-bold text-2xl text-gray-900 mb-6">
                    @if($actionType === 'confirm')
                        Konfirmasi Pesanan
                    @elseif($actionType === 'cancel')
                        Batalkan Pesanan
                    @elseif($actionType === 'process')
                        Mulai Proses Pesanan
                    @elseif($actionType === 'ready_to_ship')
                        Siap Diantar - Upload Struk
                    @elseif($actionType === 'ship')
                        {{ $order->shipping_type === 'delivery' ? 'Kirim Pesanan' : 'Siapkan untuk Diambil' }}
                    @elseif($actionType === 'deliver')
                        Selesaikan Pesanan
                    @endif
                </h3>

                @php
                    $formAction = match($actionType) {
                        'confirm' => 'confirmOrder',
                        'cancel' => 'cancelOrder',
                        'process' => 'markAsProcessing',
                        'ready_to_ship' => 'markAsReadyToShip',
                        'ship' => 'markAsShipped',
                        'deliver' => 'markAsDelivered',
                        default => 'closeModal'
                    };
                @endphp

                <form wire:submit.prevent="submitAction">
                    @if($actionType === 'confirm')
                        <!-- Order Details -->
                        <div class="card bg-blue-50 border border-blue-200 mb-6">
                            <div class="card-body p-6">
                                <h4 class="font-semibold text-lg text-gray-900 mb-4 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Detail Pesanan
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-3">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Nomor Pesanan:</span>
                                            <span class="font-semibold text-gray-900">{{ $order->order_number }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Pelanggan:</span>
                                            <span class="font-semibold text-gray-900">{{ $order->user->name }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Email:</span>
                                            <span class="font-medium text-gray-700">{{ $order->user->email }}</span>
                                        </div>
                                    </div>
                                    <div class="space-y-3">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Tanggal Pesanan:</span>
                                            <span class="font-medium text-gray-700">{{ $order->created_at->format('d M Y, H:i') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Total Pesanan:</span>
                                            <span class="font-bold text-lg text-primary">{{ $order->formatted_total }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Status:</span>
                                            <span class="badge badge-lg {{ $this->getStatusBadgeColor($order->status) }}">{{ $this->getStatusLabel($order->status) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="card bg-green-50 border border-green-200 mb-6">
                            <div class="card-body p-6">
                                <h4 class="font-semibold text-lg text-gray-900 mb-4 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    Produk yang Dipesan ({{ $order->items->count() }} item)
                                </h4>
                                <div class="overflow-x-auto">
                                    <table class="table table-zebra w-full">
                                        <thead>
                                            <tr class="bg-green-100">
                                                <th class="font-semibold text-gray-900">Produk</th>
                                                <th class="font-semibold text-gray-900">Harga Satuan</th>
                                                <th class="font-semibold text-gray-900">Qty</th>
                                                <th class="font-semibold text-gray-900">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($order->items as $item)
                                                <tr>
                                                    <td class="font-medium text-gray-900">{{ $item->product->name }}</td>
                                                    <td class="text-gray-700">{{ $item->formatted_price }}</td>
                                                    <td><span class="badge badge-outline">{{ $item->quantity }}</span></td>
                                                    <td class="font-semibold text-gray-900">{{ $item->formatted_subtotal }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="bg-green-100">
                                                <td colspan="3" class="font-bold text-gray-900">Total Pesanan:</td>
                                                <td class="font-bold text-lg text-primary">{{ $order->formatted_total }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        @if($order->status === 'processing')
                            <!-- Form untuk ready_to_ship dengan upload struk -->
                            <div class="card bg-yellow-50 border border-yellow-200">
                                <div class="card-body p-6">
                                    <h4 class="font-semibold text-lg text-gray-900 mb-4 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Form Konfirmasi - Siap Diantar
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="form-control">
                                            <label class="label">
                                                <span class="label-text font-semibold text-gray-700">Upload Foto Struk/Bukti Konfirmasi *</span>
                                            </label>
                                            <input 
                                                type="file" 
                                                wire:model="receiptImage" 
                                                accept="image/*" 
                                                class="file-input file-input-bordered w-full"
                                                required
                                            >
                                            @error('receiptImage')
                                                <label class="label">
                                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                                </label>
                                            @enderror
                                            <label class="label">
                                                <span class="label-text-alt text-gray-500">Format: JPG, PNG, maksimal 2MB</span>
                                            </label>
                                            
                                            @if ($receiptImage)
                                                <div class="mt-2">
                                                    <img src="{{ $receiptImage->temporaryUrl() }}" class="w-32 h-32 object-cover rounded-lg">
                                                </div>
                                            @endif
                                        </div>

                                        <div class="form-control">
                                            <label class="label">
                                                <span class="label-text font-semibold text-gray-700">Catatan Konfirmasi (Opsional)</span>
                                            </label>
                                            <textarea 
                                                wire:model="confirmationNote" 
                                                class="textarea textarea-bordered h-24" 
                                                placeholder="Tambahkan catatan jika diperlukan..."
                                            ></textarea>
                                            @error('confirmationNote')
                                                <label class="label">
                                                    <span class="label-text-alt text-red-500">{{ $message }}</span>
                                                </label>
                                            @enderror
                                            <label class="label">
                                                <span class="label-text-alt text-gray-500">Catatan akan dikirim ke pelanggan</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Form sederhana untuk konfirmasi pesanan baru -->
                            <div class="card bg-green-50 border border-green-200">
                                <div class="card-body p-6">
                                    <div class="flex items-center mb-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <h4 class="font-semibold text-lg text-gray-900">Konfirmasi Pesanan</h4>
                                    </div>
                                    <div class="alert alert-info mb-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>Pesanan akan dikonfirmasi dan segera dibuatkan sesuai dengan order pelanggan.</span>
                                    </div>
                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text font-semibold text-gray-700">Catatan Konfirmasi (Opsional)</span>
                                        </label>
                                        <textarea 
                                            wire:model="confirmationNote" 
                                            class="textarea textarea-bordered h-24" 
                                            placeholder="Tambahkan catatan jika diperlukan..."
                                        ></textarea>
                                        @error('confirmationNote')
                                            <label class="label">
                                                <span class="label-text-alt text-red-500">{{ $message }}</span>
                                            </label>
                                        @enderror
                                        <label class="label">
                                            <span class="label-text-alt text-gray-500">Catatan akan dikirim ke pelanggan</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @elseif($actionType === 'cancel')
                        <!-- Cancellation Form -->
                        <div class="space-y-4">
                            <div class="alert alert-warning">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <span>Pesanan yang dibatalkan tidak dapat dikembalikan!</span>
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Alasan Pembatalan <span class="text-error">*</span></span>
                                </label>
                                <textarea 
                                    wire:model="cancelReason"
                                    class="textarea textarea-bordered h-20"
                                    placeholder="Jelaskan alasan pembatalan pesanan..."
                                    required
                                ></textarea>
                                @error('cancelReason')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                        </div>
                    @elseif($actionType === 'process')
                        <!-- Process Confirmation -->
                        <div class="space-y-4">
                            <div class="alert alert-info">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Pesanan akan diubah ke status "Sedang Diproses". Pastikan semua obat tersedia dan siap untuk disiapkan.</span>
                            </div>
                        </div>
                    @elseif($actionType === 'ready_to_ship')
                        <!-- Ready to Ship - Upload Receipt -->
                        <div class="space-y-4">
                            <div class="alert alert-warning">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>Pesanan akan ditandai "Siap Diantar". Upload struk pesanan sebagai bukti bahwa pesanan telah siap untuk dikirim.</span>
                            </div>
                            
                            <!-- Receipt Upload -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Upload Struk Pesanan <span class="text-red-500">*</span></span>
                                </label>
                                <input 
                                    type="file" 
                                    wire:model="receiptImage" 
                                    accept="image/*"
                                    class="file-input file-input-bordered w-full @error('receiptImage') file-input-error @enderror"
                                >
                                @error('receiptImage')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                                <label class="label">
                                    <span class="label-text-alt">Format: JPG, PNG, maksimal 2MB</span>
                                </label>
                            </div>

                            <!-- Courier Selection -->
                            @if($order->shipping_type === 'delivery')
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-semibold">Pilih Kurir <span class="text-red-500">*</span></span>
                                    </label>
                                    <select 
                                        wire:model="selectedCourierId" 
                                        class="select select-bordered w-full @error('selectedCourierId') select-error @enderror"
                                    >
                                        <option value="">-- Pilih Kurir --</option>
                                        @foreach($this->availableCouriers as $courier)
                                            <option value="{{ $courier->user_id }}">{{ $courier->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('selectedCourierId')
                                        <label class="label">
                                            <span class="label-text-alt text-error">{{ $message }}</span>
                                        </label>
                                    @enderror
                                    <label class="label">
                                        <span class="label-text-alt">Kurir yang akan mengantar pesanan ini</span>
                                    </label>
                                </div>
                            @endif
                            
                            <!-- Preview uploaded image -->
                            @if($receiptImage)
                                <div class="mt-4">
                                    <label class="label">
                                        <span class="label-text font-semibold">Preview Struk:</span>
                                    </label>
                                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                        <img src="{{ $receiptImage->temporaryUrl() }}" alt="Preview Struk" class="max-w-full h-auto max-h-64 mx-auto rounded">
                                    </div>
                                </div>
                            @endif
                        </div>
                    @elseif($actionType === 'ship')
                        <!-- Ship Confirmation -->
                        <div class="space-y-4">
                            <div class="alert alert-info">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>
                                    @if($order->shipping_type === 'delivery')
                                        Pesanan akan dikirim ke alamat pelanggan. Pastikan semua obat sudah dikemas dengan baik.
                                    @else
                                        Pesanan akan siap untuk diambil pelanggan. Pastikan pelanggan sudah dihubungi.
                                    @endif
                                </span>
                            </div>
                        </div>
                    @elseif($actionType === 'ship')
                        <!-- Ship Confirmation -->
                        <div class="space-y-4">
                            <div class="alert alert-info">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Konfirmasi bahwa pesanan akan diantar ke alamat pelanggan.</span>
                            </div>
                        </div>
                    @elseif($actionType === 'pickup')
                        <!-- Pickup Confirmation -->
                        <div class="space-y-4">
                            <div class="alert alert-success">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Konfirmasi bahwa pesanan telah diambil oleh pelanggan. Upload foto sebagai bukti pengambilan.</span>
                            </div>
                            
                            <!-- Pickup Image Upload -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Upload Foto Bukti Pengambilan <span class="text-red-500">*</span></span>
                                </label>
                                <input 
                                    type="file" 
                                    wire:model="pickupImage" 
                                    accept="image/*"
                                    class="file-input file-input-bordered w-full @error('pickupImage') file-input-error @enderror"
                                >
                                @error('pickupImage')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                                <label class="label">
                                    <span class="label-text-alt">Format: JPG, PNG, maksimal 2MB</span>
                                </label>
                            </div>
                            
                            <!-- Preview uploaded image -->
                            @if($pickupImage)
                                <div class="mt-4">
                                    <label class="label">
                                        <span class="label-text font-semibold">Preview Foto:</span>
                                    </label>
                                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                        <img src="{{ $pickupImage->temporaryUrl() }}" alt="Preview Foto Pickup" class="max-w-full h-auto max-h-64 mx-auto rounded">
                                    </div>
                                </div>
                            @endif
                        </div>
                    @elseif($actionType === 'cancel_delivery')
                        <!-- Cancel Delivery -->
                        <div class="space-y-4">
                            <div class="alert alert-warning">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                                <span>Anda akan membatalkan pengiriman pesanan ini. Harap berikan alasan pembatalan yang jelas.</span>
                            </div>
                            
                            <!-- Cancellation Reason -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">Alasan Pembatalan <span class="text-red-500">*</span></span>
                                </label>
                                <textarea 
                                    wire:model="cancellationReason" 
                                    class="textarea textarea-bordered h-24 @error('cancellationReason') textarea-error @enderror" 
                                    placeholder="Jelaskan alasan pembatalan pengiriman (minimal 10 karakter)..."
                                ></textarea>
                                @error('cancellationReason')
                                    <label class="label">
                                        <span class="label-text-alt text-error">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                        </div>
                    @elseif($actionType === 'deliver')
                        <!-- Deliver Confirmation -->
                        <div class="space-y-4">
                            <div class="alert alert-success">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Pesanan akan diselesaikan dan ditandai sebagai "Selesai". Aksi ini tidak dapat dibatalkan.</span>
                            </div>
                        </div>
                    @endif

                    <!-- Modal Actions -->
                    <div class="flex flex-col sm:flex-row gap-3 justify-end mt-6 pt-4 border-t {{ $actionType === 'confirm' ? 'border-yellow-200' : 'border-gray-200' }}">
                        <button type="button" wire:click="closeModal" class="btn btn-ghost">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="btn {{ $actionType === 'cancel' ? 'btn-error' : ($actionType === 'deliver' ? 'btn-success' : ($actionType === 'confirm' ? 'btn-success' : ($actionType === 'ready_to_ship' ? 'btn-warning' : 'btn-primary'))) }}"
                            wire:loading.attr="disabled"
                        >
                            @if($actionType === 'confirm')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" wire:loading.remove>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            @elseif($actionType === 'ready_to_ship')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" wire:loading.remove>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            @endif
                            <span wire:loading.remove>
                                @if($actionType === 'confirm')
                                    Konfirmasi Pesanan
                                @elseif($actionType === 'cancel')
                                    Batalkan Pesanan
                                @elseif($actionType === 'process')
                                    Mulai Proses
                                @elseif($actionType === 'ready_to_ship')
                                    {{ $order->shipping_type === 'pickup' ? 'Upload & Siap Diambil' : 'Upload & Siap Diantar' }}
                                @elseif($actionType === 'ship')
                                    Antar Pesanan
                                @elseif($actionType === 'pickup')
                                    Konfirmasi Diambil
                                @elseif($actionType === 'cancel_delivery')
                                    Batalkan Pengiriman
                                @elseif($actionType === 'deliver')
                                    Selesaikan Pesanan
                                @endif
                            </span>
                            <span wire:loading class="loading loading-spinner loading-sm"></span>
                            <span wire:loading>Memproses...</span>
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-backdrop" wire:click="closeModal"></div>
        </div>
    @endif

    <!-- Loading State -->
    <div wire:loading.delay class="fixed inset-0 bg-black/20 flex items-center justify-center z-50">
        <div class="bg-base-100 p-6 rounded-lg shadow-lg flex items-center space-x-3">
            <span class="loading loading-spinner loading-md"></span>
            <span>Memproses...</span>
        </div>
    </div>
</div>