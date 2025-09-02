<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-4">
                <a href="{{ route('apoteker.orders') }}" class="btn btn-ghost btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Detail Pesanan</h1>
            </div>
            <div class="breadcrumbs text-sm">
                <ul>
                    <li><a href="{{ route('apoteker.dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a></li>
                    <li><a href="{{ route('apoteker.orders') }}" class="text-blue-600 hover:text-blue-800">Pesanan</a></li>
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
                <!-- Order Info & Actions -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-4 mb-6">
                            <div>
                                <h2 class="text-xl font-semibold">{{ $order->order_number }}</h2>
                                <p class="text-gray-600">{{ $order->created_at->format('d M Y, H:i') }}</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    <strong>Pelanggan:</strong> {{ $order->user->name }} ({{ $order->user->email }})
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="badge {{ $order->status_badge_color }} badge-lg mb-2">{{ $order->status_label }}</span>
                                @if ($order->payment)
                                    <br>
                                    <span class="badge {{ $order->payment->status_badge_color }}">{{ $order->payment->payment_status_label }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Order Status Actions -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-3">Aksi Pesanan</h3>
                            <livewire:components.order-status-actions :order="$order" wire:key="order-actions-{{ $order->order_id }}" />
                        </div>

                        <!-- Order Status Workflow -->
                        <div>
                            <h3 class="text-lg font-semibold mb-3">Alur Status Pesanan</h3>
                            <livewire:components.order-status-workflow :order="$order" :show-details="true" wire:key="order-workflow-{{ $order->order_id }}" />
                        </div>
                    </div>
                </div>

                <!-- Order Timeline -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h3 class="text-lg font-semibold mb-4">Timeline Pesanan</h3>
                        <div class="space-y-4">
                            @foreach ($timeline as $step)
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0">
                                        @if ($step['completed'])
                                            @if ($step['label'] === 'Dibatalkan')
                                                <!-- Cancelled status with red styling -->
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
                                        @if ($step['label'] === 'Dibatalkan')
                                            <!-- Cancelled status text with red color -->
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
                            @foreach ($order->items as $item)
                                <div class="flex justify-between items-center py-3 border-b border-gray-100 last:border-b-0">
                                    <div class="flex-1">
                                        <h4 class="font-medium">{{ $item->product->name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $item->quantity }}x @ {{ $item->formatted_price }}</p>
                                        @if ($item->product->description)
                                            <p class="text-xs text-gray-500 mt-1">{{ Str::limit($item->product->description, 100) }}</p>
                                        @endif
                                        @if ($item->product->stock < 10)
                                            <div class="badge badge-warning badge-sm mt-1">Stok Rendah: {{ $item->product->stock }}</div>
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

                <!-- Cancellation Info -->
                @if ($order->status === 'cancelled')
                    <div class="card bg-base-100 shadow-lg border-l-4 border-error">
                        <div class="card-body">
                            <h3 class="text-lg font-semibold text-error mb-4">Informasi Pembatalan</h3>
                            <div class="space-y-3">
                                @if ($order->cancel_reason)
                                    <div>
                                        <span class="text-sm text-gray-600">Alasan:</span>
                                        <p class="font-medium">{{ $order->cancel_reason }}</p>
                                    </div>
                                @endif
                                @if ($order->cancelled_by)
                                    <div>
                                        <span class="text-sm text-gray-600">Dibatalkan oleh:</span>
                                        <p class="font-medium">{{ $order->cancelledBy->name }} ({{ $order->cancelledBy->role }})</p>
                                    </div>
                                @endif
                                @if ($order->cancelled_at)
                                    <div>
                                        <span class="text-sm text-gray-600">Waktu pembatalan:</span>
                                        <p class="font-medium">{{ $order->cancelled_at->format('d M Y, H:i') }}</p>
                                    </div>
                                @endif
                            </div>
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
                                    <p class="text-sm text-gray-500">{{ $order->delivery->courier->phone }}</p>
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
                                @if ($order->payment->transaction_id)
                                    <div>
                                        <span class="text-sm text-gray-600">ID Transaksi:</span>
                                        <p class="font-medium text-xs">{{ $order->payment->transaction_id }}</p>
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
                                <p class="font-medium">{{ $order->user->name }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Email:</span>
                                <p class="font-medium">{{ $order->user->email }}</p>
                            </div>
                            @if ($order->user->phone)
                                <div>
                                    <span class="text-sm text-gray-600">Telepon:</span>
                                    <p class="font-medium">{{ $order->user->phone }}</p>
                                </div>
                            @endif
                            <div>
                                <span class="text-sm text-gray-600">Bergabung:</span>
                                <p class="font-medium">{{ $order->user->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Listen for order updates -->
    <script>
        document.addEventListener('livewire:init', () => {
            // Listen for order updates from OrderStatusActions component
            Livewire.on('orderUpdated', () => {
                @this.handleOrderUpdate();
            });
            
            // Listen for notification events with auto-hide
            Livewire.on('show-notification', (event) => {
                // Handle both array and object event formats
                let notificationData;
                if (Array.isArray(event) && event.length > 0) {
                    notificationData = event[0];
                } else if (typeof event === 'object' && event !== null) {
                    notificationData = event;
                } else {
                    console.error('Invalid notification event format:', event);
                    return;
                }
                
                const { type, message, autoHide, delay } = notificationData;
                
                // Validate required fields
                if (!type || !message) {
                    console.error('Missing required notification fields:', notificationData);
                    return;
                }
                
                // Remove any existing notifications to prevent stacking
                const existingNotifications = document.querySelectorAll('.notification-toast');
                existingNotifications.forEach(notification => {
                    notification.remove();
                });
                
                // Create notification element
                const notification = document.createElement('div');
                notification.className = `alert alert-${type === 'success' ? 'success' : 'error'} fixed top-4 right-4 z-50 max-w-md shadow-lg notification-toast`;
                notification.innerHTML = `
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            ${type === 'success' ? 
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />' :
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />'
                            }
                        </svg>
                        <span class="ml-2">${message}</span>
                        <button class="btn btn-sm btn-ghost ml-auto" onclick="this.parentElement.parentElement.remove()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                `;
                
                // Add to page
                document.body.appendChild(notification);
                
                // Auto-hide if specified
                if (autoHide && delay) {
                    setTimeout(() => {
                        if (notification.parentElement) {
                            notification.style.transition = 'opacity 0.3s ease-out';
                            notification.style.opacity = '0';
                            setTimeout(() => {
                                if (notification.parentElement) {
                                    notification.remove();
                                }
                            }, 300);
                        }
                    }, delay);
                }
            });
        });
    </script>
</div>