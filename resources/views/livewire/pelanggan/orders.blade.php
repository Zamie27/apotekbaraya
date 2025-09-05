<div class="orders-page">
    <div class="container mx-auto px-3 sm:px-4 py-4 sm:py-8">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="mb-6 sm:mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">Pesanan Saya</h1>
                <div class="breadcrumbs text-xs sm:text-sm">
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
            <div class="card bg-base-100 shadow-lg mb-4 sm:mb-6">
                <div class="card-body p-4 sm:p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <!-- Search -->
                        <div class="form-control">
                            <label class="label py-1">
                                <span class="label-text text-sm">Cari Pesanan</span>
                            </label>
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="search"
                                placeholder="Nomor pesanan atau nama produk..."
                                class="input input-bordered input-sm sm:input-md text-sm" />
                        </div>

                        <!-- Status Filter -->
                        <div class="form-control">
                            <label class="label py-1">
                                <span class="label-text text-sm">Filter Status</span>
                            </label>
                            <select wire:model.live="statusFilter" class="select select-bordered select-sm sm:select-md text-sm">
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
            <div class="space-y-4 sm:space-y-6">
                @foreach ($orders as $order)
                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="card-body p-4 sm:p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
                            <!-- Main Order Info -->
                            <div class="lg:col-span-2 space-y-3 sm:space-y-4">
                                <!-- Order Header -->
                                <div class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-start sm:gap-4">
                                    <div>
                                        <h3 class="text-lg sm:text-xl font-semibold text-gray-800">{{ $order->order_number }}</h3>
                                        <p class="text-xs sm:text-sm text-gray-600 mt-1">{{ $order->created_at->format('d M Y, H:i') }}</p>
                                        <div class="flex flex-wrap items-center gap-2 mt-2 sm:mt-3">
                                            <span class="badge {{ $order->status_badge_color }} gap-1 sm:gap-2 font-medium text-xs sm:text-sm">
                                                @if($order->status === 'pending')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                @elseif($order->status === 'confirmed')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                @elseif($order->status === 'processing')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                </svg>
                                                @elseif($order->status === 'shipped')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                                </svg>
                                                @elseif($order->status === 'delivered')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                @elseif($order->status === 'cancelled')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                @elseif($order->status === 'failed')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                </svg>
                                                @endif
                                                {{ $order->status_label }}
                                            </span>
                                            <span class="badge badge-outline gap-1">
                                                @if($order->shipping_type === 'pickup')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                                @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                                </svg>
                                                @endif
                                                {{ $order->shipping_type_label }}
                                            </span>
                                        </div>
                                        
                                        {{-- Failed delivery information --}}
                                        @if($order->status === 'failed' && ($order->failed_by_courier_id || $order->failed_reason))
                                        <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                                            <div class="flex items-start gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                </svg>
                                                <div class="flex-1">
                                                    <h5 class="text-sm font-medium text-red-800 mb-1">Pengiriman Gagal</h5>
                                                    @if($order->failed_by_courier_id && $order->failedByCourier)
                                                    <p class="text-xs text-red-700 mb-1">
                                                        <span class="font-medium">Kurir:</span> {{ $order->failedByCourier->name }}
                                                        @if($order->failedByCourier->phone)
                                                        <span class="text-red-600">({{ $order->failedByCourier->phone }})</span>
                                                        @endif
                                                    </p>
                                                    @endif
                                                    @if($order->failed_reason)
                                                    <p class="text-xs text-red-700">
                                                        <span class="font-medium">Alasan:</span> {{ $order->failed_reason }}
                                                    </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Order Items -->
                                <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                                    <h4 class="font-medium text-gray-800 mb-2 sm:mb-3 text-sm sm:text-base">Produk Pesanan</h4>
                                    <div class="space-y-2 sm:space-y-3">
                                        @foreach ($order->items->take(3) as $item)
                                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center py-2 border-b border-gray-200 last:border-b-0 gap-1 sm:gap-0">
                                            <div class="flex-1">
                                                <h5 class="font-medium text-gray-800 text-sm sm:text-base">{{ $item->product->name }}</h5>
                                                <p class="text-xs sm:text-sm text-gray-600">{{ $item->qty }}x @ {{ $item->formatted_price }}</p>
                                            </div>
                                            <div class="text-left sm:text-right">
                                                <p class="font-medium text-gray-800 text-sm sm:text-base">{{ $item->formatted_total }}</p>
                                            </div>
                                        </div>
                                        @endforeach

                                        @if ($order->items->count() > 3)
                                        <p class="text-xs sm:text-sm text-gray-500 text-center py-2 italic">
                                            +{{ $order->items->count() - 3 }} produk lainnya
                                        </p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Order Summary & Actions -->
                            <div class="space-y-3 sm:space-y-4">
                                <!-- Order Summary -->
                                <div class="bg-base-200 rounded-lg p-3 sm:p-4">
                                    <h4 class="font-semibold text-gray-800 mb-2 sm:mb-3 text-sm sm:text-base">Ringkasan Pesanan</h4>
                                    <div class="space-y-1 sm:space-y-2 text-xs sm:text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Subtotal</span>
                                            <span class="font-medium">{{ $order->formatted_subtotal }}</span>
                                        </div>
                                        @if ($order->delivery_fee > 0)
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Biaya Pengiriman</span>
                                            <span class="font-medium">{{ $order->formatted_delivery_fee }}</span>
                                        </div>
                                        @endif
                                        @if ($order->discount_amount > 0)
                                        <div class="flex justify-between text-success">
                                            <span>Diskon</span>
                                            <span class="font-medium">-{{ $order->formatted_discount }}</span>
                                        </div>
                                        @endif
                                        <div class="divider my-1 sm:my-2"></div>
                                        <div class="flex justify-between font-bold text-base sm:text-lg">
                                            <span>Total</span>
                                            <span class="text-primary">{{ $order->formatted_total }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Order Actions -->
                                <div class="space-y-2">
                                    <a href="{{ route('pelanggan.orders.show', $order->order_id) }}" class="btn btn-outline btn-xs sm:btn-sm w-full text-xs sm:text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Lihat Detail
                                    </a>

                                    @if ($order->canBeCancelled())
                                    <button
                                        wire:click="openCancelModal('{{ $order->order_id }}')"
                                        class="btn btn-error btn-xs sm:btn-sm w-full text-xs sm:text-sm"
                                        type="button">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Batalkan Pesanan
                                    </button>
                                    @endif

                                    @if ($order->status === 'shipped')
                                    <button
                                        wire:click="confirmDelivery({{ $order->order_id }})"
                                        class="btn btn-success btn-xs sm:btn-sm w-full text-xs sm:text-sm"
                                        onclick="return confirm('Konfirmasi bahwa pesanan sudah diterima?')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Terima Pesanan
                                    </button>
                                    @endif

                                    @if ($order->isCompleted())
                                    <button class="btn btn-primary btn-xs sm:btn-sm w-full text-xs sm:text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 3H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17M17 13v4a2 2 0 01-2 2H9a2 2 0 01-2-2v-4m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01" />
                                        </svg>
                                        Beli Lagi
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6 sm:mt-8">
                {{ $orders->links() }}
            </div>
            @else
            <!-- Empty State -->
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body text-center py-8 sm:py-16 px-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 sm:h-24 sm:w-24 mx-auto text-gray-400 mb-4 sm:mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-600 mb-2">
                        @if ($statusFilter !== 'all' || !empty($search))
                        Tidak ada pesanan ditemukan
                        @else
                        Belum ada pesanan
                        @endif
                    </h3>
                    <p class="text-sm sm:text-base text-gray-500 mb-4 sm:mb-6 max-w-md mx-auto">
                        @if ($statusFilter !== 'all' || !empty($search))
                        Coba ubah filter atau kata kunci pencarian
                        @else
                        Mulai berbelanja untuk membuat pesanan pertama Anda
                        @endif
                    </p>

                    @if ($statusFilter !== 'all' || !empty($search))
                    <button wire:click="$set('statusFilter', 'all'); $set('search', '')" class="btn btn-outline btn-sm sm:btn-md">
                        Reset Filter
                    </button>
                    @else
                    <a href="{{ route('home') }}" class="btn btn-success btn-sm sm:btn-md">
                        Mulai Belanja
                    </a>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Cancel Order Modal -->
        @if ($showCancelModal)
        <div class="modal modal-open" x-data="{ cancelReason: @entangle('cancelReason'), cancelReasonOther: @entangle('cancelReasonOther') }">
            <div class="modal-box w-11/12 max-w-md">
                <h3 class="font-bold text-base sm:text-lg mb-3 sm:mb-4">Batalkan Pesanan</h3>
                <p class="mb-3 sm:mb-4 text-sm sm:text-base">Mengapa Anda ingin membatalkan pesanan ini?</p>

                <form wire:submit="cancelOrder" class="space-y-3 sm:space-y-4">
                    <div class="form-control">
                        <label class="label py-1">
                            <span class="label-text text-sm">Pilih alasan pembatalan:</span>
                        </label>
                        <select class="select select-bordered select-sm sm:select-md w-full text-sm @error('cancelReason') select-error @enderror" x-model="cancelReason" wire:model="cancelReason">
                            <option value="">Pilih alasan...</option>
                            <option value="salah_pesan">Salah membuat pesanan</option>
                            <option value="ganti_barang">Ingin mengganti barang</option>
                            <option value="ganti_alamat">Ingin mengganti alamat pengiriman</option>
                            <option value="tidak_jadi">Tidak jadi membeli</option>
                            <option value="masalah_pembayaran">Masalah dengan pembayaran</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                        @error('cancelReason')
                        <label class="label py-1">
                            <span class="label-text-alt text-error text-xs">{{ $message }}</span>
                        </label>
                        @enderror
                    </div>

                    <div class="form-control" x-show="cancelReason === 'lainnya'">
                        <label class="label py-1">
                            <span class="label-text text-sm">Jelaskan alasan lainnya:</span>
                        </label>
                        <textarea
                            class="textarea textarea-bordered textarea-sm sm:textarea-md text-sm @error('cancelReasonOther') textarea-error @enderror"
                            placeholder="Masukkan alasan pembatalan..."
                            rows="3"
                            x-model="cancelReasonOther"
                            wire:model="cancelReasonOther"></textarea>
                        @error('cancelReasonOther')
                        <label class="label py-1">
                            <span class="label-text-alt text-error text-xs">{{ $message }}</span>
                        </label>
                        @enderror
                    </div>

                    <div class="modal-action gap-2">
                        <button
                            wire:click="closeCancelModal"
                            class="btn btn-ghost btn-sm sm:btn-md text-sm"
                            type="button">
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="btn btn-error btn-sm sm:btn-md text-sm"
                            x-bind:disabled="!cancelReason || (cancelReason === 'lainnya' && (!cancelReasonOther || cancelReasonOther.length < 3))">
                            Ya, Batalkan Pesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>