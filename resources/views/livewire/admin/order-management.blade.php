<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Manajemen Pesanan</h1>
        <p class="text-gray-600">Kelola semua pesanan pelanggan dari satu tempat</p>
    </div>

    <!-- Order Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="stat bg-base-100 shadow rounded-lg">
            <div class="stat-title text-xs">Total Pesanan</div>
            <div class="stat-value text-lg text-primary">{{ number_format($orderStats['total']) }}</div>
        </div>
        <div class="stat bg-base-100 shadow rounded-lg">
            <div class="stat-title text-xs">Menunggu</div>
            <div class="stat-value text-lg text-warning">{{ number_format($orderStats['pending']) }}</div>
        </div>
        <div class="stat bg-base-100 shadow rounded-lg">
            <div class="stat-title text-xs">Diproses</div>
            <div class="stat-value text-lg text-info">{{ number_format($orderStats['processing']) }}</div>
        </div>
        <div class="stat bg-base-100 shadow rounded-lg">
            <div class="stat-title text-xs">Dikirim</div>
            <div class="stat-value text-lg text-primary">{{ number_format($orderStats['shipped']) }}</div>
        </div>
        <div class="stat bg-base-100 shadow rounded-lg">
            <div class="stat-title text-xs">Selesai</div>
            <div class="stat-value text-lg text-success">{{ number_format($orderStats['delivered']) }}</div>
        </div>
        <div class="stat bg-base-100 shadow rounded-lg">
            <div class="stat-title text-xs">Dibatalkan</div>
            <div class="stat-value text-lg text-error">{{ number_format($orderStats['cancelled']) }}</div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card bg-base-100 shadow-lg mb-6">
        <div class="card-body">
            <div class="flex flex-col lg:flex-row gap-4">
                <!-- Search -->
                <div class="flex-1">
                    <label class="label">
                        <span class="label-text font-medium">Cari Pesanan</span>
                    </label>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Cari berdasarkan nomor pesanan, nama, atau email pelanggan..."
                        class="input input-bordered w-full"
                    >
                </div>

                <!-- Status Filter -->
                <div class="lg:w-48">
                    <label class="label">
                        <span class="label-text font-medium">Filter Status</span>
                    </label>
                    <select wire:model.live="statusFilter" class="select select-bordered w-full">
                        <option value="all">Semua Status</option>
                        <option value="pending">Menunggu</option>
                        <option value="processing">Diproses</option>
                        <option value="shipped">Dikirim</option>
                        <option value="delivered">Selesai</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                </div>

                <!-- Per Page -->
                <div class="lg:w-32">
                    <label class="label">
                        <span class="label-text font-medium">Per Halaman</span>
                    </label>
                    <select wire:model.live="perPage" class="select select-bordered w-full">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card bg-base-100 shadow-lg">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr class="bg-base-200">
                            <th>
                                <button 
                                    wire:click="sortBy('order_number')" 
                                    class="flex items-center gap-1 font-semibold hover:text-primary"
                                >
                                    No. Pesanan
                                    @if($sortBy === 'order_number')
                                        <span class="text-xs">
                                            @if($sortDirection === 'asc') ↑ @else ↓ @endif
                                        </span>
                                    @endif
                                </button>
                            </th>
                            <th>
                                <button 
                                    wire:click="sortBy('created_at')" 
                                    class="flex items-center gap-1 font-semibold hover:text-primary"
                                >
                                    Tanggal
                                    @if($sortBy === 'created_at')
                                        <span class="text-xs">
                                            @if($sortDirection === 'asc') ↑ @else ↓ @endif
                                        </span>
                                    @endif
                                </button>
                            </th>
                            <th>Pelanggan</th>
                            <th>
                                <button 
                                    wire:click="sortBy('total_price')" 
                                    class="flex items-center gap-1 font-semibold hover:text-primary"
                                >
                                    Total
                                    @if($sortBy === 'total_price')
                                        <span class="text-xs">
                                            @if($sortDirection === 'asc') ↑ @else ↓ @endif
                                        </span>
                                    @endif
                                </button>
                            </th>
                            <th>Status</th>
                            <th>Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr class="hover">
                                <td>
                                    <div class="font-mono text-sm font-medium">
                                        {{ $order->order_number }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm">
                                        {{ $order->created_at->format('d M Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $order->created_at->format('H:i') }}
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="avatar placeholder">
                                            <div class="bg-neutral text-neutral-content rounded-full w-8">
                                                <span class="text-xs">{{ substr($order->user->name ?? '', 0, 1) }}</span>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-medium text-sm">{{ $order->user->name ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-500">{{ $order->user->email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="font-semibold text-sm">
                                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $order->items ? $order->items->sum('qty') : 0 }} item
                                    </div>
                                </td>
                                <td>
                                    <div class="badge {{ $this->getStatusBadgeClass($order->status) }} badge-sm">
                                        {{ $this->getStatusLabel($order->status) }}
                                    </div>
                                </td>
                                <td>
                                    @if($order->payment)
                                        <div class="badge {{ $order->payment->status === 'paid' ? 'badge-success' : 'badge-warning' }} badge-sm">
                                            {{ $order->payment->status === 'paid' ? 'Lunas' : 'Belum Lunas' }}
                                        </div>
                                    @else
                                        <div class="badge badge-ghost badge-sm">Belum Ada</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex gap-1">
                                        <!-- View Detail -->
                                        @if($order->order_id)
                                        <a 
                                            href="{{ route('admin.orders.detail', ['orderId' => $order->order_id]) }}" 
                                            class="btn btn-sm btn-ghost"
                                            title="Lihat Detail"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Detail
                                        </a>
                                        @else
                                        <span class="btn btn-sm btn-ghost btn-disabled" title="ID tidak valid">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Detail
                                        </span>
                                        @endif

                                        <!-- Quick Status Update -->
                                        @if($order->status === 'pending')
                                            <button 
                                                wire:click="updateOrderStatus({{ $order->order_id }}, 'processing')" 
                                                class="btn btn-info btn-xs"
                                                title="Proses Pesanan"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </button>
                                        @elseif($order->status === 'processing')
                                            <button 
                                                wire:click="updateOrderStatus({{ $order->order_id }}, 'shipped')" 
                                                class="btn btn-primary btn-xs"
                                                title="Kirim Pesanan"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                                </svg>
                                            </button>
                                        @elseif($order->status === 'shipped')
                                            <button 
                                                wire:click="updateOrderStatus({{ $order->order_id }}, 'delivered')" 
                                                class="btn btn-success btn-xs"
                                                title="Selesaikan Pesanan"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="text-gray-500">Tidak ada pesanan ditemukan</p>
                                        @if($search || $statusFilter !== 'all')
                                            <button 
                                                wire:click="$set('search', ''); $set('statusFilter', 'all')" 
                                                class="btn btn-sm btn-ghost"
                                            >
                                                Reset Filter
                                            </button>
                                        @endif
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

    <!-- Loading State -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex items-center gap-3">
            <span class="loading loading-spinner loading-md"></span>
            <span>Memuat data...</span>
        </div>
    </div>
</div>

@script
<script>
    // Listen for toast notifications
    $wire.on('toast', (event) => {
        const { type, message } = event[0];
        
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} fixed top-4 right-4 w-auto max-w-sm z-50 shadow-lg`;
        toast.innerHTML = `
            <svg class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                ${type === 'success' 
                    ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                    : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                }
            </svg>
            <span>${message}</span>
        `;
        
        document.body.appendChild(toast);
        
        // Remove toast after 3 seconds
        setTimeout(() => {
            toast.remove();
        }, 3000);
    });
</script>
@endscript