<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-4">
                <a href="{{ route('pelanggan.orders') }}" class="btn btn-ghost btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Detail Pesanan</h1>
            </div>
            <div class="breadcrumbs text-sm">
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Info -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h2 class="text-xl font-semibold">{{ $order->order_number }}</h2>
                                <p class="text-gray-600">{{ $order->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="badge {{ $order->status_badge_color }} badge-lg">{{ $order->status_label }}</span>
                                @if ($order->payment)
                                    <div class="mt-2">
                                        <span class="badge {{ $order->payment->status_badge_color }}">{{ $order->payment->payment_status_label }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Order Actions -->
                        <div class="flex flex-wrap gap-2">
                            @if ($order->canBeCancelled())
                                <button 
                                    wire:click="cancelOrder" 
                                    class="btn btn-error btn-sm"
                                    onclick="return confirm('Yakin ingin membatalkan pesanan ini?')"
                                >
                                    Batalkan Pesanan
                                </button>
                            @endif
                            
                            @if ($order->status === 'shipped')
                                <button 
                                    wire:click="confirmDelivery" 
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

                <!-- Order Timeline -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h3 class="text-lg font-semibold mb-4">Status Pesanan</h3>
                        <div class="space-y-4">
                            @foreach ($timeline as $step)
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0">
                                        @if ($step['completed'])
                                            <div class="w-8 h-8 bg-success rounded-full flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-medium {{ $step['completed'] ? 'text-success' : 'text-gray-600' }}">
                                            {{ $step['label'] }}
                                        </h4>
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
                            @foreach ($order->items as $item)
                                <div class="flex justify-between items-center py-3 border-b border-gray-100 last:border-b-0">
                                    <div class="flex-1">
                                        <h4 class="font-medium">{{ $item->product->name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $item->qty }}x @ {{ $item->formatted_price }}</p>
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
                                <span>{{ $order->formatted_subtotal }}</span>
                            </div>
                            @if ($order->delivery_fee > 0)
                                <div class="flex justify-between">
                                    <span>Biaya Pengiriman</span>
                                    <span>{{ $order->formatted_delivery_fee }}</span>
                                </div>
                            @endif
                            @if ($order->discount_amount > 0)
                                <div class="flex justify-between text-success">
                                    <span>Diskon</span>
                                    <span>-{{ $order->formatted_discount }}</span>
                                </div>
                            @endif
                            <div class="divider my-2"></div>
                            <div class="flex justify-between font-bold text-lg">
                                <span>Total</span>
                                <span>{{ $order->formatted_total }}</span>
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
                                <p class="font-medium">{{ $order->shipping_type_label }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Alamat:</span>
                                <p class="font-medium">{{ $shippingAddress }}</p>
                            </div>
                            @if ($order->delivery && $order->delivery->courier)
                                <div>
                                    <span class="text-sm text-gray-600">Kurir:</span>
                                    <p class="font-medium">{{ $order->delivery->courier->name }}</p>
                                </div>
                            @endif
                            @if ($order->notes)
                                <div>
                                    <span class="text-sm text-gray-600">Catatan:</span>
                                    <p class="font-medium">{{ $order->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Payment Info -->
                @if ($order->payment)
                    <div class="card bg-base-100 shadow-lg">
                        <div class="card-body">
                            <h3 class="text-lg font-semibold mb-4">Informasi Pembayaran</h3>
                            <div class="space-y-3">
                                <div>
                                    <span class="text-sm text-gray-600">Metode:</span>
                                    <p class="font-medium">{{ $order->payment->payment_method_label }}</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-600">Status:</span>
                                    <span class="badge {{ $order->payment->status_badge_color }}">{{ $order->payment->payment_status_label }}</span>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-600">Jumlah:</span>
                                    <p class="font-medium">{{ $order->payment->formatted_amount }}</p>
                                </div>
                                @if ($order->payment->paid_at)
                                    <div>
                                        <span class="text-sm text-gray-600">Dibayar:</span>
                                        <p class="font-medium">{{ $order->payment->paid_at->format('d M Y, H:i') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>