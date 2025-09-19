<div class="min-h-screen bg-gray-50">
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Detail Pengiriman</h1>
                    <p class="text-gray-600 mt-1">Kelola status dan informasi pengiriman pesanan</p>
                </div>
                <div class="text-sm text-gray-500">
                    ID: {{ $delivery->delivery_id }}
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <!-- Success/Error Messages -->
        @if (session()->has('success'))
            <div class="alert alert-success mb-6 shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-error mb-6 shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Delivery Info Card -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-gray-900">Informasi Pengiriman</h2>
                            <span class="badge {{ $this->getDeliveryStatusBadgeColor($delivery->status) }} badge-lg">
                                {{ $this->getDeliveryStatusLabel($delivery->status) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <div>
                                    <span class="text-sm text-gray-600">ID Pengiriman:</span>
                                    <p class="font-semibold text-gray-900">{{ $delivery->delivery_id }}</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-600">Nomor Pesanan:</span>
                                    <p class="font-semibold text-gray-900">{{ $delivery->order->order_number }}</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-600">Kurir:</span>
                                    <p class="font-medium text-gray-700">{{ $delivery->courier->name }}</p>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-sm text-gray-600">Tanggal Dibuat:</span>
                                    <p class="font-medium text-gray-700">{{ $delivery->created_at->format('d M Y, H:i') }}</p>
                                </div>
                                @if($delivery->started_at)
                                <div>
                                    <span class="text-sm text-gray-600">Dimulai:</span>
                                    <p class="font-medium text-gray-700">{{ $delivery->started_at->format('d M Y, H:i') }}</p>
                                </div>
                                @endif
                                @if($delivery->delivered_at)
                                <div>
                                    <span class="text-sm text-gray-600">Selesai:</span>
                                    <p class="font-medium text-gray-700">{{ $delivery->delivered_at->format('d M Y, H:i') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Delivery Actions -->
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <h3 class="text-lg font-semibold mb-3">Aksi Pengiriman</h3>
                            <div class="flex flex-wrap gap-2">
                                @if($delivery->status === 'ready_to_ship')
                                    <button 
                                        wire:click="openActionModal('start_delivery')"
                                        class="btn btn-primary btn-sm"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                        </svg>
                                        Mulai Pengiriman
                                    </button>
                                @elseif($delivery->status === 'in_transit')
                                    <button 
                                        wire:click="openActionModal('complete_delivery')"
                                        class="btn btn-success btn-sm mr-2"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Selesaikan Pengiriman
                                    </button>
                                    <button 
                                        wire:click="openActionModal('fail_delivery')"
                                        class="btn btn-error btn-sm"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Gagal Antar
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Details Card -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h3 class="text-lg font-semibold mb-4">Detail Pesanan</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Pelanggan:</span>
                                <span class="font-medium">{{ $delivery->order->user->name }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Total Pesanan:</span>
                                <span class="font-bold text-primary">{{ $delivery->order->formatted_total }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Status Pesanan:</span>
                                <span class="badge {{ $this->getOrderStatusBadgeColor($delivery->order->status) }}">
                                    {{ $this->getOrderStatusLabel($delivery->order->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Address Card -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h3 class="text-lg font-semibold mb-4">Alamat Pengiriman</h3>
                        <div class="space-y-2">
                            <p class="font-medium">{{ $delivery->order->user->name }}</p>
                            @if($delivery->order->user->phone)
                                <p class="text-gray-600">{{ $delivery->order->user->phone }}</p>
                            @endif
                            <p class="text-gray-700">{{ $shippingAddress }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Timeline Card -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h3 class="text-lg font-semibold mb-4">Timeline Pengiriman</h3>
                        <div class="space-y-4">
                            @foreach($timeline as $step)
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        @if($step['completed'])
                                            <div class="w-3 h-3 bg-success rounded-full mt-1"></div>
                                        @else
                                            <div class="w-3 h-3 bg-gray-300 rounded-full mt-1"></div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-medium {{ $step['completed'] ? 'text-success' : 'text-gray-600' }}">
                                            {{ $step['label'] }}
                                        </h4>
                                        @if($step['date'])
                                            <p class="text-sm text-gray-500">{{ $step['date']->format('d M Y, H:i') }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">
                        @switch($actionType)
                            @case('start_delivery')
                                Konfirmasi Mulai Pengiriman
                                @break
                            @case('complete_delivery')
                                Konfirmasi Selesaikan Pengiriman
                                @break
                            @case('fail_delivery')
                                Konfirmasi Gagal Kirim
                                @break
                        @endswitch
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="submitAction">
                    <!-- Alert Messages -->
                    @if($actionType === 'start_delivery')
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle"></i>
                            <span>Anda akan memulai pengiriman untuk pesanan <strong>{{ $delivery->order->order_number }}</strong></span>
                        </div>
                    @elseif($actionType === 'complete_delivery')
                        <div class="alert alert-success mb-4">
                            <i class="fas fa-check-circle"></i>
                            <span>Anda akan menyelesaikan pengiriman untuk pesanan <strong>{{ $delivery->order->order_number }}</strong></span>
                        </div>
                    @elseif($actionType === 'fail_delivery')
                        <div class="alert alert-error mb-4">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Anda akan menandai pengiriman sebagai gagal untuk pesanan <strong>{{ $delivery->order->order_number }}</strong></span>
                        </div>
                    @endif

                    <!-- Form Fields Based on Action Type -->
                    @if($actionType === 'complete_delivery')
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Foto Bukti Pengiriman *</label>
                            <input type="file" wire:model="deliveryPhoto" accept="image/*" class="file-input file-input-bordered w-full">
                            @error('deliveryPhoto') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    @if($actionType === 'fail_delivery')
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alasan Gagal *</label>
                            <textarea wire:model="failedReason" class="textarea textarea-bordered w-full" rows="3" placeholder="Jelaskan alasan pengiriman gagal..."></textarea>
                            @error('failedReason') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    @if($actionType !== 'start_delivery')
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Pengiriman</label>
                            <textarea wire:model="deliveryNotes" class="textarea textarea-bordered w-full" rows="3" placeholder="Tambahkan catatan pengiriman..."></textarea>
                            @error('deliveryNotes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <div class="flex justify-end space-x-2">
                        <button type="button" wire:click="closeModal" class="btn btn-ghost">Batal</button>
                        <button type="submit" class="btn {{ $actionType === 'fail_delivery' ? 'btn-error' : 'btn-primary' }}">
                            @switch($actionType)
                                @case('start_delivery')
                                    Mulai Pengiriman
                                    @break
                                @case('complete_delivery')
                                    Selesaikan
                                    @break
                                @case('fail_delivery')
                                    Tandai Gagal
                                    @break
                            @endswitch
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>