@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Manajemen Pengiriman</h1>
        <p class="mt-2 text-gray-600">Kelola pengiriman pesanan yang ditugaskan kepada Anda</p>
    </div>

    <!-- Filters -->
    <div class="mb-6 bg-white rounded-lg shadow p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Pengiriman</label>
                <input type="text" wire:model.live="search" 
                       placeholder="Cari berdasarkan nomor pesanan atau nama pelanggan..."
                       class="input input-bordered w-full">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Status</label>
                <select wire:model.live="statusFilter" class="select select-bordered w-full">
                    <option value="all">Semua Status</option>
                    <option value="pending">Menunggu Pengiriman</option>
                    <option value="ready_to_ship">Siap Diantar</option>
                    <option value="in_transit">Dalam Perjalanan</option>
                    <option value="delivered">Terkirim</option>
                    <option value="failed">Gagal Kirim</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Deliveries List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($deliveries->count() > 0)
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th>No. Pesanan</th>
                            <th>Pelanggan</th>
                            <th>Total Pesanan</th>
                            <th>Status</th>
                            <th>Estimasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deliveries as $delivery)
                        <tr>
                            <td>
                                <div class="font-semibold text-primary">
                                    {{ $delivery->order->order_number }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $delivery->order->created_at->format('d/m/Y H:i') }}
                                </div>
                            </td>
                            <td>
                                <div class="font-medium">{{ $delivery->order->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $delivery->order->user->email }}</div>
                            </td>
                            <td>
                                <div class="font-semibold text-lg">
                                    Rp {{ number_format($delivery->order->total_amount, 0, ',', '.') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $delivery->order->items->count() }} item
                                </div>
                                <div class="text-xs text-gray-400">
                                    Ongkir: {{ $delivery->formatted_fee }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $statusClass = match($delivery->status) {
                                        'pending' => 'badge-warning',
                                        'ready_to_ship' => 'badge-accent',
                                        'in_transit' => 'badge-info',
                                        'delivered' => 'badge-success',
                                        'failed' => 'badge-error',
                                        default => 'badge-neutral'
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">
                                    {{ $delivery->delivery_status_label }}
                                </span>
                            </td>
                            <td>
                                @if($delivery->estimated_delivery)
                                    <div class="text-sm">
                                        {{ $delivery->estimated_delivery->format('d/m/Y H:i') }}
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex gap-2">
                                    <!-- Detail Button -->
                                    <a href="{{ route('kurir.deliveries.detail', $delivery->delivery_id) }}"
                                       class="btn btn-sm btn-outline btn-info">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Detail
                                    </a>
                                    
                                    <!-- Status Display Only -->
                                    @php
                                        $actionStatusClass = match($delivery->status) {
                                            'pending' => 'badge-warning',
                                            'ready_to_ship' => 'badge-accent',
                                            'in_transit' => 'badge-info',
                                            'delivered' => 'badge-success',
                                            'failed' => 'badge-error',
                                            default => 'badge-neutral'
                                        };
                                        
                                        $actionStatusText = match($delivery->status) {
                                            'pending' => 'Menunggu',
                                            'ready_to_ship' => ($delivery->order->shipping_type === 'pickup' ? 'Siap Diambil' : 'Siap Diantar'),
                                            'in_transit' => 'Dalam Perjalanan',
                                            'delivered' => 'Selesai',
                                            'failed' => 'Dibatalkan',
                                            default => ucfirst(str_replace('_', ' ', $delivery->status))
                                        };
                                    @endphp
                                    <span class="badge {{ $actionStatusClass }}">
                                        {{ $actionStatusText }}
                                    </span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4">
                {{ $deliveries->links() }}
            </div>
        @else
            <div class="p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2m13-8V4a1 1 0 00-1-1H7a1 1 0 00-1 1v1m8 0V4.5a.5.5 0 00-.5-.5h-3a.5.5 0 00-.5.5V5" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada pengiriman</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if($search || $statusFilter !== 'all')
                        Tidak ada pengiriman yang sesuai dengan filter.
                    @else
                        Belum ada pengiriman yang ditugaskan kepada Anda.
                    @endif
                </p>
            </div>
        @endif
    </div>

    <!-- Note: Modal removed as all delivery actions are now handled in Detail Pengiriman page -->


</div>
