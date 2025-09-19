@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-4">
                <a href="{{ route('kurir.deliveries') }}" class="btn btn-ghost btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Detail Pengiriman</h1>
            </div>
            <div class="breadcrumbs text-sm">
                <ul>
                    <li><a href="{{ route('kurir.dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a></li>
                    <li><a href="{{ route('kurir.deliveries') }}" class="text-blue-600 hover:text-blue-800">Pengiriman</a></li>
                    <li class="text-gray-500">{{ $delivery->order->order_number }}</li>
                </ul>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="alert alert-success mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-error mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Delivery Info & Actions -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-4 mb-6">
                            <div>
                                <h2 class="text-xl font-semibold">{{ $delivery->order->order_number }}</h2>
                                <p class="text-gray-600">{{ $delivery->created_at->format('d M Y, H:i') }}</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    <strong>Pelanggan:</strong> {{ $delivery->order->user->name }} ({{ $delivery->order->user->email }})
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="badge {{ $delivery->delivery_status === 'delivered' ? 'badge-success' : ($delivery->delivery_status === 'failed' ? 'badge-error' : ($delivery->delivery_status === 'in_transit' ? 'badge-warning' : 'badge-info')) }} badge-lg mb-2">
                                    {{ ucfirst(str_replace('_', ' ', $delivery->delivery_status)) }}
                                </span>
                                @if ($delivery->order->payment)
                                    <br>
                                    <span class="badge {{ $delivery->order->payment->status_badge_color }}">{{ $delivery->order->payment->payment_status_label }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Delivery Actions -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-3">Aksi Pengiriman</h3>
                            <div class="flex flex-wrap gap-3">
                                @if($delivery->delivery_status === 'ready_to_ship')
                                    <button 
                                        wire:click="showStartDeliveryConfirmation" 
                                        class="btn btn-primary" 
                                        wire:loading.attr="disabled">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                        </svg>
                                        <span wire:loading.remove>Mulai Pengiriman</span>
                                        <span wire:loading>Memproses...</span>
                                    </button>
                                @elseif($delivery->delivery_status === 'in_transit')
                                    <button 
                                        wire:click="showUpdateDeliveryModal" 
                                        class="btn btn-secondary" 
                                        wire:loading.attr="disabled">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        <span wire:loading.remove>Update Status</span>
                                        <span wire:loading>Memproses...</span>
                                    </button>
                                @endif
                            </div>


                        </div>
                    </div>
                </div>

                <!-- Delivery Timeline -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h3 class="text-lg font-semibold mb-4">Timeline Pengiriman</h3>
                        <div class="space-y-4">
                            @foreach ($timeline as $step)
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0">
                                        @if ($step['completed'])
                                            @if (in_array($step['label'], ['Dibatalkan', 'Gagal Kirim']))
                                                <!-- Cancelled/Failed status with red styling -->
                                                <div class="w-8 h-8 bg-error rounded-full flex items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </div>
                                            @else
                                                <!-- Normal completed status with green styling -->
                                                <div class="w-8 h-8 bg-success rounded-full flex items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </div>
                                            @endif
                                        @else
                                            <!-- Pending status with gray styling -->
                                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        @if (in_array($step['label'], ['Dibatalkan', 'Gagal Kirim']))
                                            <!-- Cancelled/Failed status text with red color -->
                                            <h4 class="font-medium {{ $step['completed'] ? 'text-error' : 'text-gray-600' }}">
                                                {{ $step['label'] }}
                                            </h4>
                                        @else
                                            <!-- Normal status text -->
                                            <h4 class="font-medium {{ $step['completed'] ? 'text-success' : 'text-gray-600' }}">
                                                {{ $step['label'] }}
                                            </h4>
                                        @endif
                                        @if ($step['date'])
                                            <p class="text-sm text-gray-500">{{ $step['date']->format('d M Y, H:i') }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h3 class="text-lg font-semibold mb-4">Produk Pesanan</h3>
                        <div class="space-y-4">
                            @foreach ($delivery->order->items as $item)
                                <div class="flex justify-between items-center py-3 border-b border-gray-100 last:border-b-0">
                                    <div class="flex-1">
                                        <h4 class="font-medium">{{ $item->product->name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $item->quantity }}x @ {{ $item->formatted_price }}</p>
                                        @if ($item->product->description)
                                            <p class="text-xs text-gray-500 mt-1">{{ Str::limit($item->product->description, 100) }}</p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium">{{ $item->formatted_total }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Delivery Photo & Notes -->
                @if($delivery->delivery_photo || $delivery->delivery_notes)
                    <div class="card bg-base-100 shadow-lg">
                        <div class="card-body">
                            <h3 class="text-lg font-semibold mb-4">Dokumentasi Pengiriman</h3>
                            
                            @if($delivery->delivery_photo)
                                <div class="mb-6">
                                    <h4 class="font-semibold text-md mb-3">Foto Pengiriman</h4>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <img src="{{ Storage::url($delivery->delivery_photo) }}" 
                                             class="w-64 h-64 object-cover rounded-lg border border-gray-200 shadow-sm" 
                                             alt="Foto Pengiriman">
                                    </div>
                                </div>
                            @endif

                            @if($delivery->delivery_notes)
                                <div>
                                    <h4 class="font-semibold text-md mb-3">Catatan Kurir</h4>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="text-sm">{{ $delivery->delivery_notes }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Order Summary -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h3 class="text-lg font-semibold mb-4">Ringkasan Pesanan</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span>Subtotal</span>
                                <span>{{ $delivery->order->formatted_subtotal }}</span>
                            </div>
                            @if ($delivery->delivery_fee > 0)
                                <div class="flex justify-between">
                                    <span>Biaya Pengiriman</span>
                                    <span>{{ 'Rp ' . number_format($delivery->delivery_fee, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if ($delivery->order->discount_amount > 0)
                                <div class="flex justify-between text-success">
                                    <span>Diskon</span>
                                    <span>-{{ $delivery->order->formatted_discount }}</span>
                                </div>
                            @endif
                            <div class="divider my-2"></div>
                            <div class="flex justify-between font-bold text-lg">
                                <span>Total</span>
                                <span>{{ $delivery->order->formatted_total }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Info -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h3 class="text-lg font-semibold mb-4">Informasi Pengiriman</h3>
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm text-gray-600">Metode:</span>
                                <p class="font-medium">{{ $delivery->order->shipping_type_label }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Alamat:</span>
                                <p class="font-medium">{{ $shippingAddress }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Kurir:</span>
                                <p class="font-medium">{{ $delivery->courier->name }}</p>
                                <p class="text-sm text-gray-500">{{ $delivery->courier->phone ?? 'Telepon tidak tersedia' }}</p>
                            </div>
                            @if ($delivery->order->notes)
                                <div>
                                    <span class="text-sm text-gray-600">Catatan:</span>
                                    <p class="font-medium">{{ $delivery->order->notes }}</p>
                                </div>
                            @endif
                            @if ($delivery->estimated_delivery)
                                <div>
                                    <span class="text-sm text-gray-600">Estimasi Pengiriman:</span>
                                    <p class="font-medium">{{ $delivery->estimated_delivery->format('d M Y, H:i') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Payment Info -->
                @if ($delivery->order->payment)
                    <div class="card bg-base-100 shadow-lg">
                        <div class="card-body">
                            <h3 class="text-lg font-semibold mb-4">Informasi Pembayaran</h3>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-sm text-gray-600">Metode:</span>
                                    <p class="font-medium">{{ $delivery->order->payment->payment_method_label }}</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-600">Status:</span>
                                    <span class="badge {{ $delivery->order->payment->status_badge_color }}">{{ $delivery->order->payment->payment_status_label }}</span>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-600">Jumlah:</span>
                                    <p class="font-medium">{{ $delivery->order->payment->formatted_amount }}</p>
                                </div>
                                @if ($delivery->order->payment->paid_at)
                                    <div>
                                        <span class="text-sm text-gray-600">Dibayar:</span>
                                        <p class="font-medium">{{ $delivery->order->payment->paid_at->format('d M Y, H:i') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Customer Info -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h3 class="text-lg font-semibold mb-4">Informasi Pelanggan</h3>
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm text-gray-600">Nama:</span>
                                <p class="font-medium">{{ $delivery->order->user->name }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Email:</span>
                                <p class="font-medium">{{ $delivery->order->user->email }}</p>
                            </div>
                            @if ($delivery->order->user->phone)
                                <div>
                                    <span class="text-sm text-gray-600">Telepon:</span>
                                    <p class="font-medium">{{ $delivery->order->user->phone }}</p>
                                </div>
                            @endif
                            <div>
                                <span class="text-sm text-gray-600">Bergabung:</span>
                                <p class="font-medium">{{ $delivery->order->user->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Delivery Modal -->
    @if($showUpdateModal)
        <div class="modal modal-open">
            <div class="modal-box max-w-2xl">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Update Status Pengiriman</h2>
                
                <!-- Order Info Card -->
                <div class="bg-gradient-to-r from-primary/10 to-secondary/10 border-l-4 border-primary p-6 rounded-lg mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <div>
                                <p class="text-sm text-gray-600">No. Pesanan</p>
                                <p class="font-semibold text-gray-900">{{ $delivery->order->order_number }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <div>
                                <p class="text-sm text-gray-600">Pelanggan</p>
                                <p class="font-semibold text-gray-900">{{ $delivery->order->user->name }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <div>
                                <p class="text-sm text-gray-600">Alamat</p>
                                <p class="font-semibold text-gray-900">{{ Str::limit($shippingAddress, 30) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                            <div>
                                <p class="text-sm text-gray-600">Total Pesanan</p>
                                <p class="font-semibold text-gray-900">{{ $delivery->order->formatted_total }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <form wire:submit="updateDelivery">
                    <!-- Status Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Status Pengiriman *</label>
                        <select wire:model.live="newStatus" class="select select-bordered w-full focus:border-primary" required>
                            <option value="">Pilih Status</option>
                            @if($delivery->delivery_status === 'ready_to_ship')
                                <option value="in_transit">Dalam Perjalanan</option>
                            @endif
                            @if(in_array($delivery->delivery_status, ['ready_to_ship', 'in_transit']))
                                <option value="delivered">Terkirim</option>
                                <option value="failed">Gagal Kirim</option>
                            @endif
                        </select>
                        @error('newStatus') 
                            <span class="text-error text-sm mt-1">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Photo Upload -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">
                            <span>
                                @if($newStatus === 'delivered')
                                    Foto Bukti Pengiriman *
                                @elseif($newStatus === 'failed')
                                    Foto Bukti Masalah (Opsional)
                                @else
                                    Foto Konfirmasi
                                @endif
                            </span>
                        </label>
                        <input type="file" wire:model="deliveryPhoto" accept="image/*" 
                               class="file-input file-input-bordered w-full focus:border-primary">
                        <div class="label">
                            <span class="label-text-alt text-gray-600">
                                @if($newStatus === 'delivered')
                                    Upload foto saat pesanan diterima pelanggan (wajib, max 2MB)
                                @elseif($newStatus === 'failed')
                                    Upload foto bukti masalah pengiriman (opsional, max 2MB)
                                @else
                                    Upload foto konfirmasi (max 2MB)
                                @endif
                            </span>
                        </div>
                        @error('deliveryPhoto') 
                            <span class="text-error text-sm mt-1">{{ $message }}</span> 
                        @enderror

                        @if($deliveryPhoto)
                            <div class="mt-3">
                                <img src="{{ $deliveryPhoto->temporaryUrl() }}" class="w-32 h-32 object-cover rounded border border-gray-200 shadow-sm" alt="Preview">
                            </div>
                        @endif
                    </div>

                    <!-- Notes -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Catatan Pengiriman</label>
                        <textarea wire:model="deliveryNotes" 
                                  class="textarea textarea-bordered w-full focus:border-primary" 
                                  rows="3" 
                                  placeholder="Tambahkan catatan tentang pengiriman (opsional)..."></textarea>
                        @error('deliveryNotes') 
                            <span class="text-error text-sm mt-1">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Failed Reason (Only show when status is 'failed') -->
                    @if($newStatus === 'failed')
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-900 mb-2">
                                Alasan Pembatalan *
                                <span class="text-error">*</span>
                            </label>
                            <select wire:model="failedReason" class="select select-bordered w-full focus:border-primary" required>
                                <option value="">Pilih alasan pembatalan</option>
                                <option value="Alamat tidak ditemukan">Alamat tidak ditemukan</option>
                                <option value="Pelanggan tidak ada di tempat">Pelanggan tidak ada di tempat</option>
                                <option value="Pelanggan menolak pesanan">Pelanggan menolak pesanan</option>
                                <option value="Cuaca buruk">Cuaca buruk</option>
                                <option value="Kendala kendaraan">Kendala kendaraan</option>
                                <option value="Alamat tidak lengkap/salah">Alamat tidak lengkap/salah</option>
                                <option value="Pelanggan tidak dapat dihubungi">Pelanggan tidak dapat dihubungi</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                            @error('failedReason') 
                                <span class="text-error text-sm mt-1">{{ $message }}</span> 
                            @enderror
                            <div class="label">
                                <span class="label-text-alt text-gray-600">
                                    Pilih alasan yang paling sesuai dengan kondisi yang terjadi
                                </span>
                            </div>
                        </div>
                    @endif

                    <!-- Warning Alert -->
                    @if($newStatus === 'delivered')
                        <div class="alert border-l-4 border-warning bg-warning/10 mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            <span class="text-warning-content font-medium">
                                Pastikan pesanan benar-benar telah diterima pelanggan sebelum mengkonfirmasi pengiriman.
                            </span>
                        </div>
                    @elseif($newStatus === 'failed')
                        <div class="alert border-l-4 border-error bg-error/10 mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            <span class="text-error-content font-medium">
                                Jelaskan alasan kegagalan pengiriman pada catatan dan upload foto bukti jika memungkinkan.
                            </span>
                        </div>
                    @endif

                    <!-- Modal Actions -->
                    <div class="modal-action" x-data="{ showConfirm: false }">
                        <button type="button" wire:click="closeModal" class="btn btn-ghost" wire:loading.attr="disabled">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Batal
                        </button>
                        <button type="button" 
                                x-on:click="showConfirm = true" 
                                class="btn btn-primary" 
                                wire:loading.attr="disabled">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span wire:loading.remove>
                                @if($newStatus === 'delivered')
                                    Konfirmasi Terkirim
                                @elseif($newStatus === 'failed')
                                    Laporkan Gagal Kirim
                                @else
                                    Update Status
                                @endif
                            </span>
                            <span wire:loading>
                                @if($newStatus === 'delivered')
                                    Mengkonfirmasi...
                                @elseif($newStatus === 'failed')
                                    Melaporkan...
                                @else
                                    Memperbarui...
                                @endif
                            </span>
                        </button>

                        <!-- Confirmation Dialog for Submit -->
                        <div x-show="showConfirm" 
                             x-transition:enter="transition ease-out duration-300" 
                             x-transition:enter-start="opacity-0" 
                             x-transition:enter-end="opacity-100" 
                             x-transition:leave="transition ease-in duration-200" 
                             x-transition:leave-start="opacity-100" 
                             x-transition:leave-end="opacity-0" 
                             class="fixed inset-0 z-[60] overflow-y-auto" 
                             style="display: none;">
                            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" x-on:click="showConfirm = false"></div>
                                <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                                    <div class="flex items-center mb-4">
                                        <div class="flex items-center justify-center w-12 h-12 mx-auto bg-orange-100 rounded-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 text-center mb-2">
                                        @if($newStatus === 'delivered')
                                            Konfirmasi Pengiriman Selesai
                                        @elseif($newStatus === 'failed')
                                            Konfirmasi Laporan Gagal Kirim
                                        @else
                                            Konfirmasi Update Status
                                        @endif
                                    </h3>
                                    <p class="text-sm text-gray-500 text-center mb-6">
                                        @if($newStatus === 'delivered')
                                            Apakah Anda yakin pesanan telah berhasil diterima oleh pelanggan?
                                        @elseif($newStatus === 'failed')
                                            Apakah Anda yakin ingin melaporkan pengiriman ini sebagai gagal kirim?
                                        @else
                                            Apakah Anda yakin ingin mengupdate status pengiriman ini?
                                        @endif
                                    </p>
                                    <div class="flex gap-3 justify-center">
                                        <button type="button" 
                                                x-on:click="showConfirm = false" 
                                                class="btn btn-ghost">
                                            Batal
                                        </button>
                                        <button type="button" 
                                                x-on:click="showConfirm = false; $wire.updateDelivery()" 
                                                class="btn btn-primary">
                                            @if($newStatus === 'delivered')
                                                Ya, Konfirmasi Terkirim
                                            @elseif($newStatus === 'failed')
                                                Ya, Laporkan Gagal
                                            @else
                                                Ya, Update Status
                                            @endif
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Confirmation Modal Component -->
    <livewire:confirmation-modal />
</div>