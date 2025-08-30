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
                                    <button wire:click="showDeliveryDetail({{ $delivery->delivery_id }})"
                                            class="btn btn-sm btn-outline btn-info">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Detail
                                    </button>
                                    
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
            <h3 class="font-bold text-lg mb-4">
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
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium">No. Pesanan:</span>
                        <span class="ml-2">{{ $selectedDelivery->order->order_number }}</span>
                    </div>
                    <div>
                        <span class="font-medium">Pelanggan:</span>
                        <span class="ml-2">{{ $selectedDelivery->order->user->name }}</span>
                    </div>
                    <div class="col-span-2">
                        <span class="font-medium">Alamat:</span>
                        <div class="ml-2 mt-1">
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

            <form wire:submit="updateDelivery">
                <!-- Status Info -->
                @if($newStatus === 'delivered')
                    <div class="alert alert-success mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Konfirmasi bahwa pesanan telah berhasil diterima oleh pelanggan</span>
                    </div>
                @elseif($newStatus === 'picked_up')
                    <div class="alert alert-success mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Konfirmasi bahwa pesanan telah berhasil diambil oleh pelanggan</span>
                    </div>
                @elseif($newStatus === 'failed')
                    <div class="alert alert-error mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Konfirmasi pembatalan pesanan dengan alasan yang jelas</span>
                    </div>
                @endif

                <!-- Status Selection -->
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text font-medium">Status Pengiriman *</span>
                    </label>
                    <select wire:model="newStatus" class="select select-bordered w-full">
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
                    @error('newStatus') <span class="text-error text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Photo Upload -->
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text font-medium">
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
                           class="file-input file-input-bordered w-full">
                    <div class="label">
                        <span class="label-text-alt">
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
                    @error('deliveryPhoto') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    
                    @if($deliveryPhoto)
                        <div class="mt-2">
                            <img src="{{ $deliveryPhoto->temporaryUrl() }}" 
                                 class="w-32 h-32 object-cover rounded-lg border">
                        </div>
                    @endif
                </div>

                <!-- Notes -->
                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text font-medium">
                            @if($newStatus === 'failed')
                                Alasan Pembatalan *
                            @else
                                Catatan Kurir
                            @endif
                        </span>
                    </label>
                    <textarea wire:model="deliveryNotes" 
                              class="textarea textarea-bordered h-24" 
                              placeholder="@if($newStatus === 'failed')Jelaskan alasan pembatalan pesanan...@elseTambahkan catatan untuk pesanan ini (opsional)...@endif"></textarea>
                    @error('deliveryNotes') <span class="text-error text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Actions -->
                <div class="modal-action">
                    <button type="button" wire:click="closeModal" class="btn btn-ghost">Batal</button>
                    <button type="submit" class="btn {{ $newStatus === 'delivered' ? 'btn-success' : ($newStatus === 'picked_up' ? 'btn-success' : ($newStatus === 'failed' ? 'btn-error' : 'btn-primary')) }}">
                        <span wire:loading.remove wire:target="updateDelivery">
                            @if($newStatus === 'delivered')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Konfirmasi Selesai
                            @elseif($newStatus === 'picked_up')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Konfirmasi Diambil
                            @elseif($newStatus === 'failed')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Konfirmasi Batal
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Update Status
                            @endif
                        </span>
                        <span wire:loading wire:target="updateDelivery" class="loading loading-spinner loading-sm"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Delivery Detail Modal -->
    @if($showDetailModal && $selectedDelivery)
    <div class="modal modal-open">
        <div class="modal-box max-w-4xl">
            <h3 class="font-bold text-lg mb-4">Detail Pengiriman</h3>
            
            <!-- Order Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Left Column: Order Details -->
                <div class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-md mb-3">Informasi Pesanan</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="font-medium">No. Pesanan:</span>
                                <span>{{ $selectedDelivery->order->order_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Tanggal Pesanan:</span>
                                <span>{{ $selectedDelivery->order->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Status Pesanan:</span>
                                <span class="badge badge-info">{{ ucfirst($selectedDelivery->order->status) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Total Pesanan:</span>
                                <span class="font-semibold">Rp {{ number_format($selectedDelivery->order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-md mb-3">Informasi Pelanggan</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="font-medium">Nama:</span>
                                <span>{{ $selectedDelivery->order->user->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Email:</span>
                                <span>{{ $selectedDelivery->order->user->email }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Telepon:</span>
                                <span>{{ $selectedDelivery->order->user->phone ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Delivery Details -->
                <div class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-md mb-3">Informasi Pengiriman</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="font-medium">Status:</span>
                                <span class="badge badge-info">{{ $selectedDelivery->delivery_status_label }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Tipe Pengiriman:</span>
                                <span>{{ $selectedDelivery->delivery_type_label }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium">Ongkos Kirim:</span>
                                <span>{{ $selectedDelivery->formatted_fee }}</span>
                            </div>
                            @if($selectedDelivery->estimated_delivery)
                            <div class="flex justify-between">
                                <span class="font-medium">Estimasi:</span>
                                <span>{{ $selectedDelivery->estimated_delivery->format('d/m/Y H:i') }}</span>
                            </div>
                            @endif
                            @if($selectedDelivery->delivered_at)
                            <div class="flex justify-between">
                                <span class="font-medium">Diterima:</span>
                                <span>{{ $selectedDelivery->delivered_at->format('d/m/Y H:i') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Delivery Address -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-md mb-3">Alamat Pengiriman</h4>
                        <div class="text-sm">
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

            <!-- Order Items -->
            <div class="mb-6">
                <h4 class="font-semibold text-md mb-3">Daftar Produk</h4>
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($selectedDelivery->order->items as $item)
                            <tr>
                                <td>
                                    <div class="font-medium">{{ $item->product->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $item->product->category->name ?? 'Tanpa Kategori' }}</div>
                                </td>
                                <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payment Info -->
            @if($selectedDelivery->order->payment)
            <div class="mb-6">
                <h4 class="font-semibold text-md mb-3">Informasi Pembayaran</h4>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="flex justify-between">
                            <span class="font-medium">Metode:</span>
                            <span>{{ $selectedDelivery->order->payment->paymentMethod->name ?? 'Manual Transfer' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Status:</span>
                            <span class="badge badge-success">{{ ucfirst($selectedDelivery->order->payment->status) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Jumlah:</span>
                            <span>Rp {{ number_format($selectedDelivery->order->payment->amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Tanggal:</span>
                            <span>{{ $selectedDelivery->order->payment->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Delivery Photo -->
            @if($selectedDelivery->delivery_photo)
            <div class="mb-6">
                <h4 class="font-semibold text-md mb-3">Foto Pengiriman</h4>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <img src="{{ Storage::url($selectedDelivery->delivery_photo) }}" 
                         class="w-64 h-64 object-cover rounded-lg border" 
                         alt="Foto Pengiriman">
                </div>
            </div>
            @endif

            <!-- Delivery Notes -->
            @if($selectedDelivery->delivery_notes)
            <div class="mb-6">
                <h4 class="font-semibold text-md mb-3">Catatan Kurir</h4>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm">{{ $selectedDelivery->delivery_notes }}</p>
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="modal-action">
                <button type="button" wire:click="closeModal" class="btn btn-ghost">Tutup</button>
                @if($selectedDelivery->canBeUpdatedByCourier())
                    <button wire:click="showUpdateDelivery({{ $selectedDelivery->delivery_id }})" class="btn btn-primary">
                        Update Status
                    </button>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
