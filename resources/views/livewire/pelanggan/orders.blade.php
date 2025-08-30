<div class="orders-page">
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
                                class="input input-bordered" />
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
            <div class="space-y-6">
                @foreach ($orders as $order)
                <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="card-body">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Main Order Info -->
                            <div class="lg:col-span-2 space-y-4">
                                <!-- Order Header -->
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                                    <div>
                                        <h3 class="text-xl font-semibold text-gray-800">{{ $order->order_number }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ $order->created_at->format('d M Y, H:i') }}</p>
                                        <div class="flex items-center gap-2 mt-3">
                                            <span class="badge {{ $order->status_badge_color }} gap-2 font-medium">
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
                                    </div>
                                </div>

                                <!-- Order Items -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h4 class="font-medium text-gray-800 mb-3">Produk Pesanan</h4>
                                    <div class="space-y-3">
                                        @foreach ($order->items->take(3) as $item)
                                        <div class="flex justify-between items-center py-2 border-b border-gray-200 last:border-b-0">
                                            <div class="flex-1">
                                                <h5 class="font-medium text-gray-800">{{ $item->product->name }}</h5>
                                                <p class="text-sm text-gray-600">{{ $item->qty }}x @ {{ $item->formatted_price }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-medium text-gray-800">{{ $item->formatted_total }}</p>
                                            </div>
                                        </div>
                                        @endforeach

                                        @if ($order->items->count() > 3)
                                        <p class="text-sm text-gray-500 text-center py-2 italic">
                                            +{{ $order->items->count() - 3 }} produk lainnya
                                        </p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Order Summary & Actions -->
                            <div class="space-y-4">
                                <!-- Order Summary -->
                                <div class="bg-base-200 rounded-lg p-4">
                                    <h4 class="font-semibold text-gray-800 mb-3">Ringkasan Pesanan</h4>
                                    <div class="space-y-2 text-sm">
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
                                        <div class="divider my-2"></div>
                                        <div class="flex justify-between font-bold text-lg">
                                            <span>Total</span>
                                            <span class="text-primary">{{ $order->formatted_total }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Order Actions -->
                                <div class="space-y-2">
                                    <a href="{{ route('pelanggan.orders.show', $order->order_id) }}" class="btn btn-outline btn-sm w-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Lihat Detail
                                    </a>

                                    @if ($order->canBeCancelled())
                                    <button
                                        wire:click="openCancelModal('{{ $order->order_id }}')"
                                        class="btn btn-error btn-sm w-full"
                                        type="button">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Batalkan Pesanan
                                    </button>
                                    @endif

                                    @if ($order->status === 'shipped')
                                    <button
                                        wire:click="confirmDelivery({{ $order->order_id }})"
                                        class="btn btn-success btn-sm w-full"
                                        onclick="return confirm('Konfirmasi bahwa pesanan sudah diterima?')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Terima Pesanan
                                    </button>
                                    @endif

                                    @if ($order->isCompleted())
                                    <button class="btn btn-primary btn-sm w-full">
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
                    <a href="{{ route('home') }}" class="btn btn-success">
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
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">Batalkan Pesanan</h3>
                <p class="mb-4">Mengapa Anda ingin membatalkan pesanan ini?</p>

                <form wire:submit="cancelOrder" class="space-y-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Pilih alasan pembatalan:</span>
                        </label>
                        <select class="select select-bordered w-full @error('cancelReason') select-error @enderror" x-model="cancelReason" wire:model="cancelReason">
                            <option value="">Pilih alasan...</option>
                            <option value="salah_pesan">Salah membuat pesanan</option>
                            <option value="ganti_barang">Ingin mengganti barang</option>
                            <option value="ganti_alamat">Ingin mengganti alamat pengiriman</option>
                            <option value="tidak_jadi">Tidak jadi membeli</option>
                            <option value="masalah_pembayaran">Masalah dengan pembayaran</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                        @error('cancelReason')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                        @enderror
                    </div>

                    <div class="form-control" x-show="cancelReason === 'lainnya'">
                        <label class="label">
                            <span class="label-text">Jelaskan alasan lainnya:</span>
                        </label>
                        <textarea
                            class="textarea textarea-bordered @error('cancelReasonOther') textarea-error @enderror"
                            placeholder="Masukkan alasan pembatalan..."
                            rows="3"
                            x-model="cancelReasonOther"
                            wire:model="cancelReasonOther"></textarea>
                        @error('cancelReasonOther')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                        @enderror
                    </div>

                    <div class="modal-action">
                        <button
                            wire:click="closeCancelModal"
                            class="btn btn-ghost"
                            type="button">
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="btn btn-error"
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