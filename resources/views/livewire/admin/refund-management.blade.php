<div class="container mx-auto px-4 py-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Manajemen Refund</h1>
            <p class="text-gray-600 mt-2">Kelola proses refund untuk pesanan yang dibatalkan</p>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
        <div class="alert alert-success mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-error mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stat bg-base-100 shadow-xl rounded-lg">
                <div class="stat-figure text-warning">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="stat-title">Menunggu Refund</div>
                <div class="stat-value text-warning">{{ $refundStats['total_pending'] ?? 0 }}</div>
                <div class="stat-desc">Rp {{ number_format($refundStats['total_amount_pending'] ?? 0, 0, ',', '.') }}</div>
            </div>

            <div class="stat bg-base-100 shadow-xl rounded-lg">
                <div class="stat-figure text-success">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="stat-title">Refund Selesai</div>
                <div class="stat-value text-success">{{ $refundStats['total_completed'] ?? 0 }}</div>
                <div class="stat-desc">Rp {{ number_format($refundStats['total_amount_completed'] ?? 0, 0, ',', '.') }}</div>
            </div>

            <div class="stat bg-base-100 shadow-xl rounded-lg">
                <div class="stat-figure text-info">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
                <div class="stat-title">Total Refund</div>
                <div class="stat-value text-info">{{ ($refundStats['total_pending'] ?? 0) + ($refundStats['total_completed'] ?? 0) }}</div>
                <div class="stat-desc">Rp {{ number_format(($refundStats['total_amount_pending'] ?? 0) + ($refundStats['total_amount_completed'] ?? 0), 0, ',', '.') }}</div>
            </div>

            <div class="stat bg-base-100 shadow-xl rounded-lg">
                <div class="stat-figure text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <div class="stat-title">Tingkat Penyelesaian</div>
                <div class="stat-value text-primary">
                    @php
                        $total = $refundStats['total_pending'] + $refundStats['total_completed'];
                        $percentage = $total > 0 ? round(($refundStats['total_completed'] / $total) * 100) : 0;
                    @endphp
                    {{ $percentage }}%
                </div>
                <div class="stat-desc">Dari total refund</div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="card bg-base-100 shadow-xl mb-6">
            <div class="card-body p-4">
                <div class="flex flex-col lg:flex-row gap-4 items-center justify-between">
                    <!-- Search -->
                    <div class="form-control w-full lg:w-auto">
                        <div class="input-group">
                            <input 
                                type="text" 
                                wire:model.live.debounce.300ms="search" 
                                placeholder="Cari nomor pesanan atau nama pelanggan..." 
                                class="input input-bordered w-full lg:w-80"
                            >
                            <button class="btn btn-square btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="flex flex-col sm:flex-row gap-2">
                        <select wire:model.live="statusFilter" class="select select-bordered">
                            <option value="all">Semua Status</option>
                            <option value="pending">Menunggu Refund</option>
                            <option value="completed">Refund Selesai</option>
                        </select>

                        <select wire:model.live="perPage" class="select select-bordered">
                            <option value="10">10 per halaman</option>
                            <option value="25">25 per halaman</option>
                            <option value="50">50 per halaman</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr>
                                <th>
                                    <button wire:click="sortBy('order_number')" class="flex items-center gap-1 hover:text-primary">
                                        Nomor Pesanan
                                        @if($sortBy === 'order_number')
                                            @if($sortDirection === 'asc')
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                                            @else
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"/></svg>
                                            @endif
                                        @endif
                                    </button>
                                </th>
                                <th>
                                    <button wire:click="sortBy('created_at')" class="flex items-center gap-1 hover:text-primary">
                                        Tanggal Pesanan
                                        @if($sortBy === 'created_at')
                                            @if($sortDirection === 'asc')
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                                            @else
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"/></svg>
                                            @endif
                                        @endif
                                    </button>
                                </th>
                                <th>Pelanggan</th>
                                <th>
                                    <button wire:click="sortBy('total_price')" class="flex items-center gap-1 hover:text-primary">
                                        Total Refund
                                        @if($sortBy === 'total_price')
                                            @if($sortDirection === 'asc')
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                                            @else
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"/></svg>
                                            @endif
                                        @endif
                                    </button>
                                </th>
                                <th>Status Refund</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                            <tr>
                                <td>
                                    <div class="font-medium">{{ $order->order_number }}</div>
                                    <div class="text-sm text-gray-500">{{ $order->payment->payment_method ?? 'N/A' }}</div>
                                </td>
                                <td>
                                    <div class="font-medium">{{ $order->created_at->format('d M Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ $order->created_at->format('H:i') }}</div>
                                </td>
                                <td>
                                    <div class="font-medium">{{ $order->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $order->user->email }}</div>
                                </td>
                                <td>
                                    <div class="font-medium">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                                    <div class="text-sm text-gray-500">{{ $order->orderItems ? $order->orderItems->sum('qty') : 0 }} item</div>
                                </td>
                                <td>
                                    @if($order->refund_status === 'completed')
                                        <div class="badge badge-success gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Refund Selesai
                                        </div>
                                    @else
                                        <div class="badge badge-warning gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Menunggu Refund
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex gap-2">
                                        @if($order->refund_status !== 'completed')
                                            <button 
                                                wire:click="openRefundModal({{ $order->order_id }})" 
                                                class="btn btn-sm btn-primary"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                                </svg>
                                                Proses Refund
                                            </button>
                                        @endif
                                        
                                        <a href="/admin/orders/{{ $order->order_id }}" class="btn btn-sm btn-ghost">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Detail
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-8">
                                    <div class="text-gray-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <p>Tidak ada data refund yang ditemukan</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($orders->hasPages())
                <div class="p-4 border-t">
                    {{ $orders->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Refund Modal -->
    @if($showRefundModal && $selectedOrder)
    <div class="modal modal-open">
        <div class="modal-box max-w-2xl">
            <h3 class="font-bold text-lg mb-4">Konfirmasi Refund</h3>
            
            <!-- Order Details -->
            <div class="bg-base-200 rounded-lg p-4 mb-4">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium">Nomor Pesanan:</span>
                        <div>{{ $selectedOrder->order_number }}</div>
                    </div>
                    <div>
                        <span class="font-medium">Pelanggan:</span>
                        <div>{{ $selectedOrder->user->name }}</div>
                    </div>
                    <div>
                        <span class="font-medium">Total Refund:</span>
                        <div class="text-lg font-bold text-primary">Rp {{ number_format($selectedOrder->total_price, 0, ',', '.') }}</div>
                    </div>
                    <div>
                        <span class="font-medium">Metode Pembayaran:</span>
                        <div>{{ $selectedOrder->payment->payment_method ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="mb-4">
                <h4 class="font-medium mb-2">Item Pesanan:</h4>
                <div class="space-y-2 max-h-40 overflow-y-auto">
                    @foreach($selectedOrder->orderItems as $item)
                    <div class="flex justify-between items-center text-sm bg-base-100 p-2 rounded">
                        <div>
                            <div class="font-medium">{{ $item->product->name }}</div>
                            <div class="text-gray-500">{{ $item->quantity }}x Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                        </div>
                        <div class="font-medium">Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Notes -->
            <div class="form-control mb-4">
                <label class="label">
                    <span class="label-text font-medium">Catatan Refund (Opsional)</span>
                </label>
                <textarea 
                    wire:model="refundNotes" 
                    class="textarea textarea-bordered" 
                    placeholder="Tambahkan catatan untuk refund ini..."
                    rows="3"
                ></textarea>
            </div>

            <!-- Warning -->
            <div class="alert alert-warning mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
                <div>
                    <h3 class="font-bold">Perhatian!</h3>
                    <div class="text-xs">Pastikan Anda telah melakukan refund manual di dashboard Midtrans sebelum menandai refund sebagai selesai.</div>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="modal-action">
                <button wire:click="closeRefundModal" class="btn btn-ghost">Batal</button>
                <button wire:click="markRefundCompleted" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Tandai Refund Selesai
                </button>
            </div>
        </div>
    </div>
    @endif
</div>