<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Dashboard Apoteker</h1>
            <p class="text-gray-600 mt-2">Selamat datang di panel manajemen Apotek Baraya</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stat bg-base-100 shadow-xl rounded-lg">
                <div class="stat-title">Total Pesanan</div>
                <div class="stat-value text-primary">{{ $totalOrders }}</div>
                <div class="stat-desc">Semua pesanan</div>
            </div>
            <div class="stat bg-base-100 shadow-xl rounded-lg">
                <div class="stat-title">Menunggu Konfirmasi</div>
                <div class="stat-value text-warning">{{ $waitingConfirmation }}</div>
                <div class="stat-desc">Perlu ditindaklanjuti</div>
            </div>
            <div class="stat bg-base-100 shadow-xl rounded-lg">
                <div class="stat-title">Dikonfirmasi</div>
                <div class="stat-value text-success">{{ $confirmed }}</div>
                <div class="stat-desc">Sudah dikonfirmasi</div>
            </div>
            <div class="stat bg-base-100 shadow-xl rounded-lg">
                <div class="stat-title">Diproses</div>
                <div class="stat-value text-info">{{ $processing }}</div>
                <div class="stat-desc">Sedang disiapkan</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Menu Utama</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Manajemen Pesanan -->
                <a href="{{ route('apoteker.orders') }}" class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow duration-300">
                    <div class="card-body">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-primary/10 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="card-title text-lg">Manajemen Pesanan</h3>
                                <p class="text-gray-600">Konfirmasi dan kelola pesanan pelanggan</p>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Manajemen Resep -->
                <a href="{{ route('apoteker.prescriptions.index') }}" class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow duration-300">
                    <div class="card-body">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-info/10 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="card-title text-lg">Manajemen Resep</h3>
                                <p class="text-gray-600">Kelola resep dokter dari pelanggan</p>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Manajemen Produk -->
                <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow duration-300 opacity-50">
                    <div class="card-body">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-secondary/10 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="card-title text-lg">Manajemen Produk</h3>
                                <p class="text-gray-600">Kelola produk dan stok obat</p>
                                <span class="badge badge-warning badge-sm">Coming Soon</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Laporan -->
                <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow duration-300 opacity-50">
                    <div class="card-body">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-accent/10 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="card-title text-lg">Laporan</h3>
                                <p class="text-gray-600">Lihat laporan penjualan dan stok</p>
                                <span class="badge badge-warning badge-sm">Coming Soon</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-gray-900">Pesanan Menunggu Konfirmasi</h2>
                <a href="{{ route('apoteker.orders') }}" class="btn btn-primary btn-sm">
                    Lihat Semua Pesanan
                </a>
            </div>
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    @if($recentOrders->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="table table-zebra w-full">
                                <thead>
                                    <tr>
                                        <th>No. Pesanan</th>
                                        <th>Pelanggan</th>
                                        <th>Total</th>
                                        <th>Status Pembayaran</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                    <tr>
                                        <td>
                                            <div class="font-semibold text-primary">{{ $order->order_number }}</div>
                                        </td>
                                        <td>
                                            <div class="font-medium">{{ $order->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $order->user->email }}</div>
                                        </td>
                                        <td>
                                            <div class="font-semibold">{{ $order->formatted_total }}</div>
                                        </td>
                                        <td>
                                            @if($order->payment)
                                                @php
                                                    $badgeClass = match($order->payment->status) {
                                                        'completed' => 'badge-success',
                                                        'pending' => 'badge-warning',
                                                        'failed' => 'badge-error',
                                                        'cancelled' => 'badge-error',
                                                        default => 'badge-ghost'
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ $order->payment->payment_status_label }}</span>
                                            @else
                                                <span class="badge badge-ghost">Belum ada pembayaran</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-sm">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                                        </td>
                                        <td>
                                            <a href="{{ route('apoteker.orders.detail', $order->order_id) }}" class="btn btn-sm btn-primary">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada pesanan menunggu konfirmasi</h3>
                            <p class="text-gray-600">Semua pesanan sudah dikonfirmasi atau belum ada pesanan baru</p>
                            <a href="{{ route('apoteker.orders') }}" class="btn btn-primary mt-4">
                                Lihat Semua Pesanan
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>