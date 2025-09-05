@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Manajemen Pengiriman</h1>
        <p class="mt-2 text-gray-600">Kelola pengiriman pesanan yang ditugaskan kepada Anda</p>
    </div>

    <!-- Filters -->
    <div class="mb-6 bg-white rounded-lg shadow p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Pengiriman</label>
                <input type="text" wire:model.live="search" 
                       placeholder="Cari berdasarkan nomor pesanan atau nama pelanggan..."
                       class="input input-bordered w-full">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Status</label>
                <select wire:model.live="statusFilter" class="select select-bordered w-full">
                    <option value="all">Semua Status</option>
                    <option value="pending">Menunggu Pengiriman</option>
                    <option value="ready_to_ship">Siap Diantar</option>
                    <option value="in_transit">Dalam Perjalanan</option>
                    <option value="delivered">Terkirim</option>
                    <option value="failed">Gagal Kirim</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Deliveries List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($deliveries->count() > 0)
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th>No. Pesanan</th>
                            <th>Pelanggan</th>
                            <th>Total Pesanan</th>
                            <th>Status</th>
                            <th>Estimasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deliveries as $delivery)
                        <tr>
                            <td>
                                <div class="font-semibold text-primary">
                                    {{ $delivery->order->order_number }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $delivery->order->created_at->format('d/m/Y H:i') }}
                                </div>
                            </td>
                            <td>
                                <div class="font-medium">{{ $delivery->order->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $delivery->order->user->email }}</div>
                            </td>
                            <td>
                                <div class="font-semibold text-lg">
                                    Rp {{ number_format($delivery->order->total_amount, 0, ',', '.') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $delivery->order->items->count() }} item
                                </div>
                                <div class="text-xs text-gray-400">
                                    Ongkir: {{ $delivery->formatted_fee }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $statusClass = match($delivery->delivery_status) {
                                        'pending' => 'badge-warning',
                                        'ready_to_ship' => 'badge-accent',
                                        'in_transit' => 'badge-info',
                                        'delivered' => 'badge-success',
                                        'failed' => 'badge-error',
                                        default => 'badge-neutral'
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">
                                    {{ $delivery->delivery_status_label }}
                                </span>
                            </td>
                            <td>
                                @if($delivery->estimated_delivery)
                                    <div class="text-sm">
                                        {{ $delivery->estimated_delivery->format('d/m/Y H:i') }}
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex gap-2">
                                    <!-- Detail Button -->
                                    <a href="{{ route('kurir.deliveries.detail', $delivery->delivery_id) }}"
                                       class="btn btn-sm btn-outline btn-info">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Detail
                                    </a>
                                    
                                    <!-- Action Buttons based on Status -->
                                    @if($delivery->delivery_status === 'ready_to_ship')
                                        @if($delivery->order->shipping_type === 'pickup')
                                            <!-- Confirm Pickup Button for pickup orders -->
                                            <button wire:click="confirmPickup({{ $delivery->delivery_id }})"
                                                    class="btn btn-sm btn-success">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Konfirmasi Diambil
                                            </button>
                                        @else
                                            <!-- Start Delivery Button for delivery orders -->
                                            <button wire:click="startDelivery({{ $delivery->delivery_id }})"
                                                    wire:confirm="Apakah Anda yakin akan memulai pengiriman pesanan ini?"
                                                    class="btn btn-sm btn-success">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                          d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                                </svg>
                                                Antar Pesanan
                                            </button>
                                        @endif
                                    @elseif($delivery->delivery_status === 'in_transit')
                                        <!-- Complete Delivery Button -->
                                        <button wire:click="completeDelivery({{ $delivery->delivery_id }})"
                                                class="btn btn-sm btn-success">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Selesaikan Pesanan
                                        </button>
                                        
                                        <!-- Cancel Delivery Button -->
                                        <button wire:click="cancelDelivery({{ $delivery->delivery_id }})"
                                                class="btn btn-sm btn-error">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Batalkan Pesanan
                                        </button>
                                    @elseif($delivery->delivery_status === 'delivered')
                                        <span class="badge badge-success">Selesai</span>
                                    @elseif($delivery->delivery_status === 'failed')
                                        <span class="badge badge-error">Dibatalkan</span>
                                    @else
                                        <span class="badge badge-warning">{{ ucfirst(str_replace('_', ' ', $delivery->delivery_status)) }}</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4">
                {{ $deliveries->links() }}
            </div>
        @else
            <div class="p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2m13-8V4a1 1 0 00-1-1H7a1 1 0 00-1 1v1m8 0V4.5a.5.5 0 00-.5-.5h-3a.5.5 0 00-.5.5V5" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada pengiriman</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if($search || $statusFilter !== 'all')
                        Tidak ada pengiriman yang sesuai dengan filter.
                    @else
                        Belum ada pengiriman yang ditugaskan kepada Anda.
                    @endif
                </p>
            </div>
        @endif
    </div>

    <!-- Update Delivery Modal -->
    @if($showUpdateModal && $selectedDelivery)
    <div class="modal modal-open">
        <div class="modal-box max-w-2xl">
            <h3 class="font-bold text-2xl text-gray-900 mb-6">
                @if($newStatus === 'delivered')
                    Konfirmasi Penyelesaian Pesanan
                @elseif($newStatus === 'picked_up')
                    Konfirmasi Pesanan Diambil
                @elseif($newStatus === 'failed')
                    Konfirmasi Pembatalan Pesanan
                @else
                    Update Status Pengiriman
                @endif
            </h3>
            
            <!-- Delivery Info -->
            <div class="card bg-blue-50 border border-blue-200 mb-6">
                <div class="card-body p-6">
                    <h4 class="font-semibold text-lg text-gray-900 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Informasi Pengiriman
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">No. Pesanan:</span>
                                <span class="font-semibold text-gray-900">{{ $selectedDelivery->order->order_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Pelanggan:</span>
                                <span class="font-semibold text-gray-900">{{ $selectedDelivery->order->user->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Pesanan:</span>
                                <span class="font-bold text-lg text-primary">{{ $selectedDelivery->order->formatted_total }}</span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <span class="text-gray-600">Alamat Pengiriman:</span>
                                <div class="font-medium text-gray-700 mt-1">
                                    @if(is_array($selectedDelivery->delivery_address))
                                        {{ $selectedDelivery->delivery_address['street'] ?? '' }}<br>
                                        {{ $selectedDelivery->delivery_address['village'] ?? '' }}, 
                                        {{ $selectedDelivery->delivery_address['district'] ?? '' }}<br>
                                        {{ $selectedDelivery->delivery_address['city'] ?? '' }}, 
                                        {{ $selectedDelivery->delivery_address['province'] ?? '' }}
                                    @else
                                        {{ $selectedDelivery->delivery_address }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form wire:submit="updateDelivery">
                <!-- Status Info -->
                @if($newStatus === 'delivered')
                    <div class="alert alert-success mb-6 border-l-4 border-success bg-success/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5 text-success" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-success-content font-medium">Konfirmasi bahwa pesanan telah berhasil diterima oleh pelanggan</span>
                    </div>
                @elseif($newStatus === 'picked_up')
                    <div class="alert alert-success mb-6 border-l-4 border-success bg-success/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5 text-success" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-success-content font-medium">Konfirmasi bahwa pesanan telah berhasil diambil oleh pelanggan</span>
                    </div>
                @elseif($newStatus === 'failed')
                    <div class="alert alert-warning mb-6 border-l-4 border-warning bg-warning/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5 text-warning" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <span class="text-warning-content font-medium">Pesanan akan dibatalkan dan tidak dapat dikembalikan. Pastikan alasan pembatalan sudah benar.</span>
                    </div>
                @endif

                <!-- Status Selection -->
                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text font-semibold text-gray-900">Status Pengiriman *</span>
                    </label>
                    <select wire:model="newStatus" class="select select-bordered w-full focus:border-primary">
                        <option value="">Pilih Status</option>
                        @if($selectedDelivery->delivery_status === 'ready_to_ship')
                            @if($selectedDelivery->order->shipping_type === 'pickup')
                                <option value="picked_up">Pesanan Diambil Pelanggan</option>
                            @else
                                <option value="in_transit">Pesanan Diterima Kurir</option>
                            @endif
                        @elseif($selectedDelivery->delivery_status === 'in_transit')
                            <option value="delivered">Pesanan Sudah Diterima</option>
                            <option value="failed">Batalkan Pesanan</option>
                        @endif
                    </select>
                    @error('newStatus') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Photo Upload -->
                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text font-semibold text-gray-900">
                            @if($newStatus === 'delivered')
                                Foto Bukti Penyerahan *
                            @elseif($newStatus === 'picked_up')
                                Foto Bukti Pengambilan *
                            @elseif($newStatus === 'failed')
                                Foto Bukti Masalah (Opsional)
                            @else
                                Foto Konfirmasi 
                                @if($selectedDelivery->delivery_status === 'ready_to_ship')
                                    (Opsional)
                                @else
                                    *
                                @endif
                            @endif
                        </span>
                    </label>
                    <input type="file" wire:model="deliveryPhoto" accept="image/*" 
                           class="file-input file-input-bordered w-full focus:border-primary">
                    <div class="label">
                        <span class="label-text-alt text-gray-600">
                            @if($newStatus === 'delivered')
                                Upload foto saat pesanan diterima pelanggan (wajib, max 2MB)
                            @elseif($newStatus === 'picked_up')
                                Upload foto saat pesanan diambil pelanggan (wajib, max 2MB)
                            @elseif($newStatus === 'failed')
                                Upload foto bukti masalah pengiriman (opsional, max 2MB)
                            @else
                                @if($selectedDelivery->delivery_status === 'ready_to_ship')
                                    Upload foto saat menerima pesanan (opsional, max 2MB)
                                @else
                                    Upload foto bukti pesanan diterima pelanggan (wajib, max 2MB)
                                @endif
                            @endif
                        </span>
                    </div>
                    @error('deliveryPhoto') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                    
                    @if($deliveryPhoto)
                        <div class="mt-3">
                            <img src="{{ $deliveryPhoto->temporaryUrl() }}" 
                                 class="w-32 h-32 object-cover rounded-lg border border-gray-200 shadow-sm">
                        </div>
                    @endif
                </div>

                <!-- Notes -->
                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text font-semibold text-gray-900">
                            @if($newStatus === 'failed')
                                Alasan Pembatalan *
                            @else
                                Catatan Kurir
                            @endif
                        </span>
                    </label>
                    <textarea wire:model="deliveryNotes" 
                              class="textarea textarea-bordered h-24 focus:border-primary" 
                              placeholder="@if($newStatus === 'failed')Jelaskan alasan pembatalan pesanan...@elseTambahkan catatan untuk pesanan ini (opsional)...@endif"></textarea>
                    @error('deliveryNotes') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Actions -->
                <div class="modal-action">
                    <button type="button" wire:click="closeModal" class="btn btn-ghost">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        class="btn {{ $newStatus === 'delivered' ? 'btn-success' : ($newStatus === 'picked_up' ? 'btn-success' : ($newStatus === 'failed' ? 'btn-error' : 'btn-primary')) }}"
                        wire:loading.attr="disabled"
                    >
                        <!-- Loading Spinner -->
                        <span wire:loading wire:target="updateDelivery" class="loading loading-spinner loading-sm mr-2"></span>
                        
                        <span wire:loading.remove wire:target="updateDelivery">
                            @if($newStatus === 'delivered')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Konfirmasi Selesai
                            @elseif($newStatus === 'picked_up')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Konfirmasi Diambil
                            @elseif($newStatus === 'failed')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Konfirmasi Batal
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Update Status
                            @endif
                        </span>
                        
                        <span wire:loading wire:target="updateDelivery">
                            @if($newStatus === 'delivered')
                                Mengkonfirmasi Selesai...
                            @elseif($newStatus === 'picked_up')
                                Mengkonfirmasi Diambil...
                            @elseif($newStatus === 'failed')
                                Mengkonfirmasi Batal...
                            @else
                                Memperbarui Status...
                            @endif
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif


</div>
