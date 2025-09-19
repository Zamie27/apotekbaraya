<div class="container mx-auto px-3 sm:px-4 py-4 sm:py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 mb-4">
                <h1 class="text-xl sm:text-3xl font-bold text-gray-800">Detail Pesanan</h1>
            </div>
            <div class="breadcrumbs text-xs sm:text-sm">
                <ul>
                    <li><a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800">Home</a></li>
                    <li><a href="{{ route('pelanggan.orders') }}" class="text-blue-600 hover:text-blue-800">Pesanan</a></li>
                    <li class="text-gray-500">{{ $order->order_number }}</li>
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

        @if (session()->has('info'))
        <div class="alert alert-info mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('info') }}</span>
        </div>
        @endif

        {{-- Payment Status Check Message --}}
        @if ($isCheckingPaymentStatus || $paymentStatusMessage)
        <div class="alert alert-info mb-6" id="payment-status-alert">
            @if ($isCheckingPaymentStatus)
            <span class="loading loading-spinner loading-sm"></span>
            @else
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            @endif
            <span>{{ $paymentStatusMessage }}</span>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-4 sm:space-y-6">
                <!-- Order Info -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body p-4 sm:p-6">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 sm:gap-4 mb-4">
                            <div>
                                <h2 class="text-lg sm:text-xl font-semibold">{{ $order->order_number }}</h2>
                                <p class="text-sm sm:text-base text-gray-600">{{ $order->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <div class="">
                                <span class="badge {{ $order->status_badge_color }} badge-md sm:badge-lg text-xs sm:text-sm">{{ $order->status_label }}</span>
                            </div>
                        </div>

                        <!-- Order Actions -->
                        <div class="flex flex-wrap gap-2">
                            {{-- Payment Button --}}
                            @if ($order->payment && $order->payment->status === 'pending' && !$order->isPaymentExpired())
                            <button
                                wire:click="continuePayment"
                                class="btn btn-primary btn-sm text-xs sm:text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 0h10a2 2 0 002-2v-3a2 2 0 00-2-2H9a2 2 0 00-2 2v3a2 2 0 002 2z" />
                                </svg>
                                <span class="hidden sm:inline">Bayar Pesanan</span>
                                <span class="sm:hidden">Bayar</span>
                            </button>
                            @endif

                            {{-- Check Payment Status Button --}}
                            @if ($order->payment && $order->payment->status === 'pending')
                            <button
                                wire:click="checkPaymentStatus"
                                wire:loading.attr="disabled"
                                wire:target="checkPaymentStatus"
                                class="btn btn-info btn-sm text-xs sm:text-sm">
                                <span wire:loading.remove wire:target="checkPaymentStatus">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <span class="hidden sm:inline">Cek Status Pembayaran</span>
                                    <span class="sm:hidden">Cek Status</span>
                                </span>
                                <span wire:loading wire:target="checkPaymentStatus" class="loading loading-spinner loading-xs mr-1"></span>
                                <span wire:loading wire:target="checkPaymentStatus">Mengecek...</span>
                            </button>
                            @endif

                            {{-- Note: Konfirmasi pesanan hanya dilakukan oleh apoteker, bukan pelanggan --}}

                            {{-- Cancel Order Button --}}
                            @if ($order->canBeCancelled())
                            <button
                                wire:click="openCancelModal"
                                class="btn btn-error btn-sm text-xs sm:text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span class="hidden sm:inline">Batalkan Pesanan</span>
                                <span class="sm:hidden">Batal</span>
                            </button>
                            @endif

                            {{-- Confirm Delivery Button --}}
                            @if ($order->status === 'shipped')
                            <button
                                wire:click="confirmDelivery"
                                class="btn btn-success btn-sm text-xs sm:text-sm"
                                onclick="return confirm('Konfirmasi bahwa pesanan sudah diterima?')">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="hidden sm:inline">Terima Pesanan</span>
                                <span class="sm:hidden">Terima</span>
                            </button>
                            @endif

                            {{-- Reorder Button --}}
                            @if ($order->isCompleted())
                            <button class="btn btn-primary btn-sm text-xs sm:text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01" />
                                </svg>
                                <span class="hidden sm:inline">Beli Lagi</span>
                                <span class="sm:hidden">Beli Lagi</span>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Order Timeline -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4">Status Pesanan</h3>
                        <div class="space-y-3 sm:space-y-4">
                            @foreach ($timeline as $step)
                            <div class="flex items-start gap-2 sm:gap-4">
                                <div class="flex-shrink-0">
                                    @if (isset($step['is_cancelled']) && $step['is_cancelled'])
                                    {{-- Cancelled status with red X icon --}}
                                    <div class="w-6 h-6 sm:w-8 sm:h-8 bg-error rounded-full flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                    @elseif (isset($step['is_failed']) && $step['is_failed'])
                                    {{-- Failed status with red X icon --}}
                                    <div class="w-6 h-6 sm:w-8 sm:h-8 bg-error rounded-full flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                    @elseif ($step['completed'])
                                    {{-- Completed status with green check --}}
                                    <div class="w-6 h-6 sm:w-8 sm:h-8 bg-success rounded-full flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    @else
                                    {{-- Pending status with gray clock --}}
                                    <div class="w-6 h-6 sm:w-8 sm:h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-sm sm:text-base {{ isset($step['is_cancelled']) && $step['is_cancelled'] ? 'text-error' : ($step['completed'] ? 'text-success' : 'text-gray-600') }}">
                                        {{ $step['label'] }}
                                    </h4>
                                    @if ($step['date'])
                                    <p class="text-xs sm:text-sm text-gray-500">{{ $step['date']->format('d M Y, H:i') }}</p>
                                    @endif

                                    {{-- Show failed delivery information --}}
                                    @if (isset($step['is_failed']) && $step['is_failed'])
                                        <div class="mt-2 p-3 bg-error/10 border border-error/20 rounded-lg">
                                            @if (isset($step['failed_reason']) && $step['failed_reason'])
                                                <div class="flex items-start gap-2 mb-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-error mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z" />
                                                    </svg>
                                                    <div>
                                                        <p class="text-xs sm:text-sm font-medium text-error">Alasan Pembatalan:</p>
                                                        <p class="text-xs sm:text-sm text-gray-700">{{ $step['failed_reason'] }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                            @if (isset($step['failed_by_courier']) && $step['failed_by_courier'])
                                                <div class="flex items-start gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    <div>
                                                        <p class="text-xs sm:text-sm font-medium text-gray-600">Kurir:</p>
                                                        <p class="text-xs sm:text-sm text-gray-700">{{ $step['failed_by_courier'] }}</p>
                                                        @if (isset($step['failed_by_courier_phone']) && $step['failed_by_courier_phone'])
                                                            <p class="text-xs sm:text-sm text-gray-500">{{ $step['failed_by_courier_phone'] }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- Show delivery proof link if available --}}
                                    @if (isset($step['show_proof_link']) && $step['show_proof_link'])
                                    <button
                                        type="button"
                                        wire:click="showDeliveryProof('{{ $step['delivery_proof'] }}')"
                                        class="text-blue-600 hover:text-blue-800 text-xs sm:text-sm font-medium mt-1 flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        @if($order->shipping_type === 'pickup')
                                        <span class="hidden sm:inline">Lihat Bukti Pengambilan</span>
                                        <span class="sm:hidden">Lihat Bukti</span>
                                        @else
                                        <span class="hidden sm:inline">Lihat Bukti Pengiriman</span>
                                        <span class="sm:hidden">Lihat Bukti</span>
                                        @endif
                                    </button>
                                    @endif

                                    {{-- Show cancel reason if this is a cancelled step --}}
                                    @if (isset($step['is_cancelled']) && $step['is_cancelled'] && isset($step['cancel_reason']) && $step['cancel_reason'])
                                    <div class="mt-2 p-2 sm:p-3 bg-error/10 border border-error/20 rounded-lg">
                                        <p class="text-xs sm:text-sm font-medium text-error mb-1">Alasan Pembatalan:</p>
                                        <p class="text-xs sm:text-sm text-gray-700">{{ $step['cancel_reason'] }}</p>
                                    </div>
                                    @endif

                                    {{-- Show failure reason if this is a failed step --}}
                                    @if (isset($step['is_failed']) && $step['is_failed'] && isset($step['failure_reason']) && $step['failure_reason'])
                                    <div class="mt-2 p-2 sm:p-3 bg-error/10 border border-error/20 rounded-lg">
                                        <p class="text-xs sm:text-sm font-medium text-error mb-1">Alasan Kegagalan:</p>
                                        <p class="text-xs sm:text-sm text-gray-700">{{ $step['failure_reason'] }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4">Produk Pesanan</h3>
                        <div class="space-y-3 sm:space-y-4">
                            @foreach ($order->items as $item)
                            <div class="flex items-start space-x-3 sm:space-x-4 p-3 sm:p-4 border border-gray-200 rounded-lg">
                                <div class="flex-shrink-0">
                                    @if ($item->product->image)
                                    <img src="{{ Storage::url($item->product->image) }}" alt="{{ $item->product->name }}" class="w-12 h-12 sm:w-16 sm:h-16 object-cover rounded-lg">
                                    @else
                                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                        </svg>
                                    </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-sm sm:text-base text-gray-800">{{ $item->product->name }}</h4>
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 mt-1">
                                        <p class="text-xs sm:text-sm text-gray-600">{{ $item->qty }}x @ {{ $item->formatted_price }}</p>
                                    </div>
                                    @if ($item->product->description)
                                    <p class="text-xs sm:text-sm text-gray-500 mt-1 line-clamp-2">{{ Str::limit($item->product->description, 80) }}</p>
                                    @endif
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="font-medium text-sm sm:text-base text-gray-800">{{ $item->formatted_total }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-4 sm:space-y-6">
                <!-- Order Summary -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4">Ringkasan Pesanan</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm sm:text-base">Subtotal</span>
                                <span class="text-sm sm:text-base">{{ $order->formatted_subtotal }}</span>
                            </div>
                            @if ($order->delivery_fee > 0)
                            <div class="flex justify-between">
                                <span class="text-sm sm:text-base">Biaya Pengiriman</span>
                                <span class="text-sm sm:text-base">{{ $order->formatted_delivery_fee }}</span>
                            </div>
                            @endif
                            @if ($order->discount_amount > 0)
                            <div class="flex justify-between text-success">
                                <span class="text-sm sm:text-base">Diskon</span>
                                <span class="text-sm sm:text-base">-{{ $order->formatted_discount }}</span>
                            </div>
                            @endif
                            <div class="divider my-2"></div>
                            <div class="flex justify-between font-bold text-base sm:text-lg">
                                <span>Total</span>
                                <span>{{ $order->formatted_total }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Info -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4">Informasi Pengiriman</h3>
                        <div class="space-y-3">
                            <div>
                                <span class="text-xs sm:text-sm text-gray-600">Metode:</span>
                                <p class="font-medium text-sm sm:text-base">{{ $order->shipping_type_label }}</p>
                            </div>
                            <div>
                                <span class="text-xs sm:text-sm text-gray-600">Alamat:</span>
                                <p class="font-medium text-sm sm:text-base break-words">{{ $shippingAddress }}</p>
                            </div>
                            @if ($order->delivery && $order->delivery->courier)
                            <div>
                                <span class="text-xs sm:text-sm text-gray-600">Kurir:</span>
                                <p class="font-medium text-sm sm:text-base">{{ $order->delivery->courier->name }}</p>
                            </div>
                            @endif
                            @if ($order->notes)
                            <div>
                                <span class="text-xs sm:text-sm text-gray-600">Catatan:</span>
                                <p class="font-medium text-sm sm:text-base break-words">{{ $order->notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Payment Info -->
                @if ($order->payment)
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body p-4 sm:p-6">
                        <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4">Informasi Pembayaran</h3>
                        <div class="space-y-3">
                            <div>
                                <span class="text-xs sm:text-sm text-gray-600">Metode:</span>
                                <p class="font-medium text-sm sm:text-base">{{ $order->payment->payment_method_label }}</p>
                            </div>
                            <div>
                                <span class="text-xs sm:text-sm text-gray-600">Status:</span>
                                <span class="badge badge-sm sm:badge-md {{ $order->payment->status_badge_color }}"><span class="text-xs sm:text-sm">{{ $order->payment->payment_status_label }}</span></span>
                            </div>
                            <div>
                                <span class="text-xs sm:text-sm text-gray-600">Jumlah:</span>
                                <p class="font-medium text-sm sm:text-base">{{ $order->payment->formatted_amount }}</p>
                            </div>

                            {{-- Payment Reference --}}
                            @if ($order->payment->transaction_id)
                            <div>
                                <span class="text-xs sm:text-sm text-gray-600">ID Transaksi:</span>
                                <p class="font-medium font-mono text-xs sm:text-sm break-all">{{ $order->payment->transaction_id }}</p>
                            </div>
                            @endif

                            {{-- Payment Expiry --}}
                            @if ($order->payment->status === 'pending' && $order->payment_expired_at)
                            <div>
                                <span class="text-xs sm:text-sm text-gray-600">Batas Waktu:</span>
                                @if ($order->isPaymentExpired())
                                <p class="font-medium text-red-600 text-sm sm:text-base">Kedaluwarsa pada {{ $order->payment_expired_at->format('d M Y, H:i') }}</p>
                                @else
                                <p class="font-medium text-orange-600 text-sm sm:text-base">{{ $order->payment_expired_at->format('d M Y, H:i') }}</p>
                                <p class="text-xs text-gray-500">Sisa waktu: {{ $order->payment_expired_at->diffForHumans() }}</p>
                                @endif
                            </div>
                            @endif

                            {{-- Payment Date --}}
                            @if ($order->payment->paid_at)
                            <div>
                                <span class="text-xs sm:text-sm text-gray-600">Dibayar:</span>
                                <p class="font-medium text-sm sm:text-base">{{ $order->payment->paid_at->format('d M Y, H:i') }}</p>
                            </div>
                            @endif

                            {{-- Payment Instructions --}}
                            @if ($order->payment->status === 'pending' && $order->payment_instructions && !$order->isPaymentExpired())
                            <div class="mt-4 p-2 sm:p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <h4 class="text-xs sm:text-sm font-semibold text-blue-800 mb-2">Instruksi Pembayaran:</h4>
                                <div class="text-xs sm:text-sm text-blue-700 space-y-1">
                                    @foreach ($order->payment_instructions as $instruction)
                                    <p>• {{ $instruction }}</p>
                                    @endforeach
                                </div>
                            </div>
                            @endif



                            {{-- Payment Status Info --}}
                            @if ($order->isPaymentExpired())
                            <div class="mt-4">
                                <div class="alert alert-warning">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                    <span>Pembayaran telah kedaluwarsa. Silakan buat pesanan baru.</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif


            </div>
        </div>
    </div>

    {{-- Cancel Order Modal --}}
    @if($showCancelModal)
        <div class="modal modal-open">
            <div class="modal-box w-11/12 max-w-md">
                <h3 class="font-bold text-base sm:text-lg mb-3 sm:mb-4">Batalkan Pesanan</h3>
                <p class="mb-3 sm:mb-4 text-sm sm:text-base">Mengapa Anda ingin membatalkan pesanan ini?</p>

                <form wire:submit.prevent="cancelOrder" class="space-y-3 sm:space-y-4">
                    <div class="form-control">
                        <label class="label py-1">
                            <span class="label-text text-sm">Pilih alasan pembatalan:</span>
                        </label>
                        <select class="select select-bordered select-sm sm:select-md w-full text-sm @error('cancelReason') select-error @enderror" wire:model.live="cancelReason">
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

                    @if($cancelReason === 'lainnya')
                    <div class="form-control">
                        <label class="label py-1">
                            <span class="label-text text-sm">Jelaskan alasan lainnya:</span>
                        </label>
                        <textarea
                            class="textarea textarea-bordered textarea-sm sm:textarea-md text-sm @error('cancelReasonOther') textarea-error @enderror"
                            placeholder="Masukkan alasan pembatalan..."
                            rows="3"
                            wire:model.live="cancelReasonOther"></textarea>
                        @error('cancelReasonOther')
                        <label class="label py-1">
                            <span class="label-text-alt text-error text-xs">{{ $message }}</span>
                        </label>
                        @enderror
                    </div>
                    @endif

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
                            @if(!$this->canSubmitCancel) disabled @endif
                            wire:loading.attr="disabled"
                            wire:target="cancelOrder">
                            <span wire:loading.remove wire:target="cancelOrder">Ya, Batalkan Pesanan</span>
                            <span wire:loading wire:target="cancelOrder" class="loading loading-spinner loading-xs mr-2"></span>
                            <span wire:loading wire:target="cancelOrder">Membatalkan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Note: Modal konfirmasi pesanan dihapus karena pelanggan tidak boleh mengkonfirmasi pesanan --}}
    {{-- Konfirmasi pesanan hanya dilakukan oleh apoteker melalui dashboard apoteker --}}

    {{-- Delivery Proof Modal --}}
    <dialog id="delivery_proof_modal" class="modal" @if($showDeliveryProofModal) open @endif>
        <div class="modal-box max-w-2xl">
            <h3 class="font-bold text-lg mb-4">
                @if($order->shipping_type === 'pickup')
                Bukti Pengambilan
                @else
                Bukti Pengiriman
                @endif
            </h3>

            @if($deliveryProofImage)
            <div class="flex justify-center">
                <img
                    src="{{ Storage::url($deliveryProofImage) }}"
                    alt="@if($order->shipping_type === 'pickup') Bukti Pengambilan @else Bukti Pengiriman @endif"
                    class="max-w-full h-auto rounded-lg shadow-lg"
                    style="max-height: 70vh;">
            </div>
            @else
            <div class="text-center py-8">
                <p class="text-gray-500">
                    @if($order->shipping_type === 'pickup')
                    Bukti pengambilan tidak tersedia
                    @else
                    Bukti pengiriman tidak tersedia
                    @endif
                </p>
            </div>
            @endif

            <div class="modal-action">
                <button
                    type="button"
                    class="btn btn-primary"
                    wire:click="closeDeliveryProofModal">
                    Tutup
                </button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button wire:click="closeDeliveryProofModal">close</button>
        </form>
    </dialog>
</div>

@script
<script>
    // Auto-refresh payment status every 30 seconds for pending payments
    function initPaymentStatusCheck() {
        try {
            if ($wire.order &&
                $wire.order.payment &&
                $wire.order.payment.status === 'pending' &&
                $wire.order.status !== 'cancelled') {
                setInterval(() => {
                    if ($wire.order && $wire.order.status !== 'cancelled') {
                        $wire.checkPaymentStatus();
                    }
                }, 30000);
            }
        } catch (error) {
            console.log('Payment status check initialization skipped:', error.message);
        }
    }

    // Check payment status immediately when returning from payment page
    function checkPaymentOnReturn() {
        try {
            // Check if user just returned from payment (detect success/info flash messages)
            const hasPaymentMessage = document.querySelector('.alert-success, .alert-info');
            const urlParams = new URLSearchParams(window.location.search);
            const fromPayment = urlParams.get('from_payment');
            
            if ((hasPaymentMessage || fromPayment) && 
                $wire.order &&
                $wire.order.payment &&
                $wire.order.payment.status === 'pending' &&
                $wire.order.status !== 'cancelled') {
                
                console.log('User returned from payment, checking status...');
                setTimeout(() => {
                    $wire.checkPaymentStatus();
                }, 1000); // Small delay to ensure component is ready
            }
        } catch (error) {
            console.log('Payment return check skipped:', error.message);
        }
    }

    // Initialize payment check when component is ready
    document.addEventListener('livewire:initialized', () => {
        initPaymentStatusCheck();
        checkPaymentOnReturn();
    });

    // Also check when page becomes visible (user switches back to tab)
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            try {
                if ($wire.order &&
                    $wire.order.payment &&
                    $wire.order.payment.status === 'pending' &&
                    $wire.order.status !== 'cancelled') {
                    $wire.checkPaymentStatus();
                }
            } catch (error) {
                console.log('Visibility change check skipped:', error.message);
            }
        }
    });

    // Handle clear payment status message event
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('clear-payment-status-message', () => {
            setTimeout(() => {
                try {
                    $wire.set('paymentStatusMessage', '');
                } catch (error) {
                    console.log('Clear payment status message failed:', error.message);
                }
            }, 3000); // Clear after 3 seconds
        });
    });

</script>
@endscript