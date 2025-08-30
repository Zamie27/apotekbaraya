<div class="min-h-screen bg-gray-50">
    <!-- Page Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Manajemen Pesanan</h1>
                    <p class="text-gray-600 mt-1">Kelola konfirmasi dan pembatalan pesanan pelanggan</p>
                </div>
                <div class="text-sm text-gray-500">
                    Total: {{ $orders->total() }} pesanan
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

        <!-- Filters -->
        <div class="card bg-base-100 shadow-lg mb-8 border border-gray-200">
            <div class="card-body p-6">
                <h3 class="font-semibold text-lg text-gray-900 mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z" />
                    </svg>
                    Filter & Pencarian
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Search -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold text-gray-700">Cari Pesanan</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="text" 
                                wire:model.live.debounce.300ms="search" 
                                placeholder="Nomor pesanan, nama pelanggan, atau produk..." 
                                class="input input-bordered w-full pl-10"
                            />
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold text-gray-700">Filter Status</span>
                        </label>
                        <select wire:model.live="statusFilter" class="select select-bordered w-full">
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Results Info -->
                    <div class="flex flex-col justify-end">
                        <div class="stats shadow border border-gray-200">
                            <div class="stat py-4">
                                <div class="stat-title text-xs">Hasil Pencarian</div>
                                <div class="stat-value text-lg">{{ $orders->count() }}</div>
                                <div class="stat-desc text-xs">dari {{ $orders->total() }} total pesanan</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders List -->
        @if ($orders->count() > 0)
            <div class="space-y-6">
                @foreach ($orders as $order)
                    <div class="card bg-base-100 shadow-lg border border-gray-200">
                        <div class="card-body p-6">
                            <!-- Header Section -->
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4 pb-4 border-b border-gray-100">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                    <h3 class="font-bold text-xl text-gray-900">{{ $order->order_number }}</h3>
                                    <div class="flex flex-wrap gap-2">
                                        <span class="badge badge-lg {{ $this->getStatusBadgeColor($order->status) }}">
                                            {{ $this->getStatusLabel($order->status) }}
                                        </span>
                                        @if ($order->payment)
                                            <span class="badge badge-lg {{ $order->payment->status_badge_color }}">
                                                {{ $order->payment->payment_status_label }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $order->created_at->format('d M Y, H:i') }}
                                </div>
                            </div>

                            <!-- Content Section -->
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <!-- Customer Info -->
                                <div class="lg:col-span-1">
                                    <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Informasi Pelanggan
                                    </h4>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex flex-col">
                                            <span class="font-medium text-gray-900">{{ $order->user->name }}</span>
                                            <span class="text-gray-600">{{ $order->user->email }}</span>
                                        </div>
                                        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                                            <span class="text-gray-600">Total Pesanan:</span>
                                            <span class="font-bold text-lg text-primary">{{ $order->formatted_total }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Order Items -->
                                <div class="lg:col-span-1">
                                    <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                        Produk ({{ $order->items->count() }} item)
                                    </h4>
                                    <div class="space-y-2">
                                        @foreach ($order->items->take(3) as $item)
                                            <div class="flex justify-between items-center text-sm bg-gray-50 p-2 rounded">
                                                <span class="font-medium text-gray-900 flex-1 mr-2">{{ $item->product->name }}</span>
                                                <span class="badge badge-outline badge-sm">{{ $item->quantity }}x</span>
                                            </div>
                                        @endforeach
                                        @if ($order->items->count() > 3)
                                            <div class="text-center text-sm text-gray-500 italic pt-1">
                                                +{{ $order->items->count() - 3 }} produk lainnya
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="lg:col-span-1">
                                    <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4" />
                                        </svg>
                                        Aksi
                                    </h4>
                                    <div class="flex flex-col gap-2">
                                        <!-- Use OrderStatusActions component for consistent modal experience -->
                                        <livewire:components.order-status-actions :order="$order" wire:key="order-actions-{{ $order->order_id }}" />
                                        
                                        <a href="{{ route('apoteker.orders.detail', $order->order_id) }}" class="btn btn-outline btn-sm w-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Lihat Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="text-sm text-gray-600">
                        Menampilkan {{ $orders->firstItem() ?? 0 }} - {{ $orders->lastItem() ?? 0 }} dari {{ $orders->total() }} pesanan
                    </div>
                    <div class="flex justify-center">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="card bg-base-100 shadow-lg border border-gray-200">
                <div class="card-body text-center py-16">
                    <div class="flex flex-col items-center">
                        <div class="bg-gray-100 rounded-full p-6 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak ada pesanan ditemukan</h3>
                        <p class="text-gray-500 mb-4 max-w-md">
                            @if($search || $statusFilter)
                                Tidak ada pesanan yang sesuai dengan kriteria pencarian atau filter yang dipilih.
                            @else
                                Belum ada pesanan yang masuk. Pesanan baru akan muncul di sini.
                            @endif
                        </p>
                        @if($search || $statusFilter)
                            <button 
                                wire:click="$set('search', ''); $set('statusFilter', '')" 
                                class="btn btn-outline btn-sm"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reset Filter
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>