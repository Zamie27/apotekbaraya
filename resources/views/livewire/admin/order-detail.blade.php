<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-4">
                <a href="{{ route('admin.refunds') }}" class="btn btn-ghost btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Detail Pesanan Admin</h1>
            </div>
            <div class="breadcrumbs text-sm">
                <ul>
                    <li><a href="/admin/dashboard" class="text-blue-600 hover:text-blue-800">Dashboard</a></li>
                    <li><a href="{{ route('admin.refunds') }}" class="text-blue-600 hover:text-blue-800">Manajemen Refund</a></li>
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
                <!-- Order Info & Admin Actions -->
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

                        <!-- Admin-specific Actions -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-3">Aksi Admin</h3>
                            <div class="flex flex-wrap gap-2">
                                @if (in_array($order->status, ['pending', 'waiting_payment', 'waiting_confirmation', 'cancelled']))
                                    <button class="btn btn-error btn-sm" onclick="delete_order_modal.showModal()">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Hapus Pesanan
                                    </button>
                                @endif
                                
                                @if ($order->refunds && $order->refunds->count() > 0)
                            <div class="badge badge-info">{{ $order->refunds->count() }} Refund Request</div>
                                @endif
                                
                                <button class="btn btn-info btn-sm" onclick="order_log_modal.showModal()">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Log Aktivitas
                                </button>
                            </div>
                        </div>

                        <!-- Order Status Actions -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-3">Aksi Status Pesanan</h3>
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
                                            @if (in_array($step['label'], ['Dibatalkan', 'Gagal Diantar']))
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
                                        @if (($step['label'] ?? '') === 'Dibatalkan')
                                            <!-- Cancelled status text with red color -->
                                            <h4 class="font-medium {{ $step['completed'] ? 'text-error' : 'text-gray-600' }}">
                                                {{ $step['label'] ?? '' }}
                                            </h4>
                                        @else
                                            <!-- Normal status text -->
                                            <h4 class="font-medium {{ $step['completed'] ? 'text-success' : 'text-gray-600' }}">
                                                {{ $step['label'] ?? '' }}
                                            </h4>
                                        @endif
                                        @if ($step['date'] ?? null)
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
                                        <!-- Admin-specific product info -->
                                        <div class="text-xs text-gray-400 mt-1">
                                            ID Produk: {{ $item->product->id }} | Stok Saat Ini: {{ $item->product->stock }}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium">{{ $item->formatted_total }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Refund Information -->
                @if ($order->refunds && $order->refunds->count() > 0)
                    <div class="card bg-base-100 shadow-lg border-l-4 border-info">
                        <div class="card-body">
                            <h3 class="text-lg font-semibold text-info mb-4">Informasi Refund</h3>
                            <div class="space-y-4">
                                @foreach ($order->refunds as $refund)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex justify-between items-start mb-2">
                                            <span class="badge {{ $refund->status === 'completed' ? 'badge-success' : ($refund->status === 'pending' ? 'badge-warning' : 'badge-error') }}">
                                                {{ ucfirst($refund->status) }}
                                            </span>
                                            <span class="text-sm text-gray-500">{{ $refund->created_at->format('d M Y, H:i') }}</span>
                                        </div>
                                        @if ($refund->reason)
                                            <p class="text-sm text-gray-600 mb-2"><strong>Alasan:</strong> {{ $refund->reason }}</p>
                                        @endif
                                        <p class="text-sm text-gray-600"><strong>Jumlah:</strong> {{ number_format($refund->amount, 0, ',', '.') }}</p>
                                        @if ($refund->processed_by)
                                            <p class="text-sm text-gray-600"><strong>Diproses oleh:</strong> {{ $refund->processedBy->name }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

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
                                @if($shippingAddress)
                                    <div class="font-medium">
                                        <p>{{ $shippingAddress['name'] ?? '' }}</p>
                                        <p>{{ $shippingAddress['phone'] ?? '' }}</p>
                                        <p>{{ $shippingAddress['address'] ?? '' }}</p>
                                        <p>{{ $shippingAddress['city'] ?? '' }} {{ $shippingAddress['postal_code'] ?? '' }}</p>
                                        @if($shippingAddress['notes'] ?? '')
                                            <p class="text-sm text-gray-500">Catatan: {{ $shippingAddress['notes'] ?? '' }}</p>
                                        @endif
                                    </div>
                                @else
                                    <p class="font-medium text-gray-500">Alamat tidak tersedia</p>
                                @endif
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
                            <!-- Admin-specific customer info -->
                            <div>
                                <span class="text-sm text-gray-600">Total Pesanan:</span>
                                <p class="font-medium">{{ $order->user->orders ? $order->user->orders->count() : 0 }} pesanan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Order Modal -->
    <dialog id="delete_order_modal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg text-error">Hapus Pesanan</h3>
            <div class="py-4">
                <div class="alert alert-warning mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <span><strong>Peringatan!</strong> Tindakan ini akan menghapus pesanan secara permanen dari database dan tidak dapat dibatalkan.</span>
                </div>
                <p class="mb-4">Pesanan yang dapat dihapus hanya yang berstatus: <strong>Pending, Menunggu Pembayaran, Menunggu Konfirmasi, atau Dibatalkan</strong>.</p>
                <p class="mb-4">Apakah Anda yakin ingin menghapus pesanan <strong>{{ $order->order_number }}</strong>?</p>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Alasan Penghapusan <span class="text-error">*</span></span>
                    </label>
                    <textarea 
                        class="textarea textarea-bordered" 
                        placeholder="Masukkan alasan penghapusan pesanan (minimal 10 karakter)..."
                        rows="3"
                        id="delete_reason"
                        required
                    ></textarea>
                </div>
            </div>
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn">Batal</button>
                    <button 
                        type="button" 
                        class="btn btn-error" 
                        onclick="confirmDeleteOrder()"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Ya, Hapus Pesanan
                    </button>
                </form>
            </div>
        </div>
    </dialog>

    <!-- Order Log Modal -->
    <dialog id="order_log_modal" class="modal">
        <div class="modal-box w-11/12 max-w-5xl">
            <h3 class="font-bold text-lg">Log Aktivitas Pesanan</h3>
            <div class="py-4">
                <div class="space-y-6">
                    <!-- Confirmation Details -->
                    <div class="card bg-base-200">
                        <div class="card-body">
                            <h4 class="font-semibold text-lg mb-3">Detail Konfirmasi Pesanan</h4>
                            @if($order->confirmed_by)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <span class="text-sm text-gray-600">Dikonfirmasi oleh:</span>
                                        <p class="font-medium">{{ $order->confirmedBy->name }}</p>
                                        <p class="text-sm text-gray-500">{{ ucfirst($order->confirmedBy->role->name) }}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-600">Waktu Konfirmasi:</span>
                                        <p class="font-medium">{{ $order->confirmed_at ? $order->confirmed_at->format('d M Y, H:i') : '-' }}</p>
                                    </div>
                                </div>
                            @else
                                <p class="text-gray-500 italic">Pesanan belum dikonfirmasi</p>
                            @endif
                        </div>
                    </div>

                    <!-- Photo Upload Component -->
                    <livewire:admin.order-photo-upload :order="$order" wire:key="photo-upload-{{ $order->order_id }}" />

                    <!-- Activity Timeline -->
                    <div class="card bg-base-200">
                        <div class="card-body">
                            <h4 class="font-semibold text-lg mb-3">Log Aktivitas</h4>
                            <div class="space-y-4">
                                <!-- Order Created -->
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 w-8 h-8 bg-primary rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium">Pesanan Dibuat</p>
                                        <p class="text-sm text-gray-600">Oleh: {{ $order->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
                                    </div>
                                </div>

                                <!-- Order Confirmed -->
                                @if($order->confirmed_by)
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 w-8 h-8 bg-success rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium">Pesanan Dikonfirmasi</p>
                                        <p class="text-sm text-gray-600">Dikonfirmasi oleh: {{ $order->confirmedBy->name }}</p>
                                        <p class="text-sm text-gray-600">{{ ucfirst($order->confirmedBy->role->name) }}</p>
                                        <p class="text-xs text-gray-500">{{ $order->confirmed_at->format('d M Y, H:i') }}</p>
                                    </div>
                                </div>
                                @endif

                                <!-- Receipt Photo Uploaded -->
                                @if($order->receipt_photo && $order->receiptPhotoUploadedBy)
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 w-8 h-8 bg-info rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium">Foto Struk Diupload</p>
                                        <p class="text-sm text-gray-600">Diupload oleh: {{ $order->receiptPhotoUploadedBy->name }}</p>
                                        <p class="text-sm text-gray-600">{{ ucfirst($order->receiptPhotoUploadedBy->role->name) }}</p>
                                        <p class="text-xs text-gray-500">{{ $order->receipt_photo_uploaded_at ? $order->receipt_photo_uploaded_at->format('d M Y, H:i') : '-' }}</p>
                                        <div class="mt-2">
                                            <button onclick="receipt_photo_modal.showModal()" class="btn btn-xs btn-outline">
                                                Lihat Foto
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Delivery Photo Uploaded -->
                                @if($order->delivery_photo && $order->deliveryPhotoUploadedBy)
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 w-8 h-8 bg-warning rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium">Foto {{ $order->shipping_type === 'pickup' ? 'Pengambilan' : 'Pengiriman' }} Diupload</p>
                                        <p class="text-sm text-gray-600">Diupload oleh: {{ $order->deliveryPhotoUploadedBy->name }}</p>
                                        <p class="text-sm text-gray-600">{{ ucfirst($order->deliveryPhotoUploadedBy->role->name) }}</p>
                                        @if($order->delivery && $order->delivery->courier)
                                            <p class="text-sm text-gray-600">Kurir: {{ $order->delivery->courier->name }}</p>
                                        @endif
                                        <p class="text-xs text-gray-500">{{ $order->delivery_photo_uploaded_at ? $order->delivery_photo_uploaded_at->format('d M Y, H:i') : '-' }}</p>
                                        <div class="mt-2">
                                            <button onclick="delivery_photo_modal.showModal()" class="btn btn-xs btn-outline">
                                                Lihat Foto
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Order Completed -->
                                @if($order->status === 'completed')
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 w-8 h-8 bg-success rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium">Pesanan Selesai</p>
                                        <p class="text-xs text-gray-500">{{ $order->updated_at->format('d M Y, H:i') }}</p>
                                    </div>
                                </div>
                                @endif

                                <!-- Order Cancelled -->
                                @if($order->status === 'cancelled')
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 w-8 h-8 bg-error rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium">Pesanan Dibatalkan</p>
                                        @if($order->cancelledBy)
                                            <p class="text-sm text-gray-600">Dibatalkan oleh: {{ $order->cancelledBy->name }}</p>
                                            <p class="text-sm text-gray-600">{{ ucfirst($order->cancelledBy->role->name) }}</p>
                                        @endif
                                        @if($order->cancel_reason)
                                            <p class="text-sm text-gray-600">Alasan: {{ $order->cancel_reason }}</p>
                                        @endif
                                        <p class="text-xs text-gray-500">{{ $order->updated_at->format('d M Y, H:i') }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn">Tutup</button>
                </form>
            </div>
        </div>
    </dialog>

    <!-- Modal Foto Pengiriman -->
    <dialog id="delivery_photo_modal" class="modal">
        <div class="modal-box max-w-2xl">
            <h3 class="font-bold text-lg mb-4">Foto Bukti Pengiriman/Pengambilan</h3>
            <div class="flex justify-center">
                <img id="delivery_photo_preview" src="" alt="Foto Pengiriman" class="max-w-full max-h-96 rounded-lg shadow-lg">
            </div>
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn">Tutup</button>
                </form>
            </div>
        </div>
    </dialog>

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
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />'}
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

        // Confirm delete order function
        function confirmDeleteOrder() {
            const reason = document.getElementById('delete_reason').value.trim();
            
            if (reason.length < 10) {
                alert('Alasan penghapusan harus minimal 10 karakter!');
                return;
            }
            
            if (confirm('Apakah Anda benar-benar yakin ingin menghapus pesanan ini? Tindakan ini tidak dapat dibatalkan!')) {
                @this.call('deleteOrder', reason);
                document.getElementById('delete_order_modal').close();
            }
        }

        // Show delivery photo function
        function showDeliveryPhoto(photoUrl) {
            const modal = document.getElementById('delivery_photo_modal');
            const preview = document.getElementById('delivery_photo_preview');
            preview.src = photoUrl;
            modal.showModal();
        }
    </script>
</div>