<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Pesanan Saya</h1>
            <div class="breadcrumbs text-sm">
                <ul>
                    <li><a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800">Home</a></li>
                    <li class="text-gray-500">Pesanan</li>
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

        <!-- Filters -->
        <div class="card bg-base-100 shadow-lg mb-6">
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Search -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Cari Pesanan</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="search" 
                            placeholder="Nomor pesanan atau nama produk..." 
                            class="input input-bordered"
                        />
                    </div>

                    <!-- Status Filter -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Filter Status</span>
                        </label>
                        <select wire:model.live="statusFilter" class="select select-bordered">
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders List -->
        @if ($orders->count() > 0)
            <div class="space-y-4">
                @foreach ($orders as $order)
                    <div class="card bg-base-100 shadow-lg">
                        <div class="card-body">
                            <!-- Order Header -->
                            <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4 mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold">{{ $order->order_number }}</h3>
                                    <p class="text-sm text-gray-600">{{ $order->created_at->format('d M Y, H:i') }}</p>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="badge {{ $order->status_badge_color }}">{{ $order->status_label }}</span>
                                        <span class="badge badge-outline">{{ $order->shipping_type_label }}</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold">{{ $order->formatted_total }}</p>
                                    @if ($order->payment)
                                        <span class="badge {{ $order->payment->status_badge_color }} badge-sm">
                                            {{ $order->payment->payment_status_label }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Order Items -->
                            <div class="space-y-2 mb-4">
                                @foreach ($order->items->take(3) as $item)
                                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                        <div class="flex-1">
                                            <h4 class="font-medium">{{ $item->product->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $item->qty }}x @ {{ $item->formatted_price }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-medium">{{ $item->formatted_total }}</p>
                                        </div>
                                    </div>
                                @endforeach
                                
                                @if ($order->items->count() > 3)
                                    <p class="text-sm text-gray-500 text-center py-2">
                                        +{{ $order->items->count() - 3 }} produk lainnya
                                    </p>
                                @endif
                            </div>

                            <!-- Order Actions -->
                            <div class="flex flex-wrap gap-2 justify-end">
                                <a href="{{ route('pelanggan.orders.show', $order->order_id) }}" class="btn btn-outline btn-sm">
                                    Detail
                                </a>
                                
                                @if ($order->canBeCancelled())
                                    <button 
                                        wire:click="cancelOrder({{ $order->order_id }})" 
                                        class="btn btn-error btn-sm"
                                        onclick="return confirm('Yakin ingin membatalkan pesanan ini?')"
                                    >
                                        Batalkan
                                    </button>
                                @endif
                                
                                @if ($order->status === 'shipped')
                                    <button 
                                        wire:click="confirmDelivery({{ $order->order_id }})" 
                                        class="btn btn-success btn-sm"
                                        onclick="return confirm('Konfirmasi bahwa pesanan sudah diterima?')"
                                    >
                                        Terima Pesanan
                                    </button>
                                @endif
                                
                                @if ($order->isCompleted())
                                    <button class="btn btn-primary btn-sm">
                                        Beli Lagi
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body text-center py-16">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">
                        @if ($statusFilter !== 'all' || !empty($search))
                            Tidak ada pesanan ditemukan
                        @else
                            Belum ada pesanan
                        @endif
                    </h3>
                    <p class="text-gray-500 mb-6">
                        @if ($statusFilter !== 'all' || !empty($search))
                            Coba ubah filter atau kata kunci pencarian
                        @else
                            Mulai berbelanja untuk membuat pesanan pertama Anda
                        @endif
                    </p>
                    
                    @if ($statusFilter !== 'all' || !empty($search))
                        <button wire:click="$set('statusFilter', 'all'); $set('search', '')" class="btn btn-outline">
                            Reset Filter
                        </button>
                    @else
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            Mulai Belanja
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>