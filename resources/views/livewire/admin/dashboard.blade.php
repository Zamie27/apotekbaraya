<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard Admin</h1>
                    <p class="text-gray-600 mt-2">Selamat datang di panel administrasi Apotek Baraya</p>
                </div>
                
                <!-- Time Filter -->
                <div class="flex flex-wrap gap-2">
                    <button wire:click="setTimeFilter('all')" 
                            class="btn btn-sm {{ $timeFilter === 'all' ? 'btn-primary' : 'btn-outline' }}">
                        Semua
                    </button>
                    <button wire:click="setTimeFilter('today')" 
                            class="btn btn-sm {{ $timeFilter === 'today' ? 'btn-primary' : 'btn-outline' }}">
                        Hari Ini
                    </button>
                    <button wire:click="setTimeFilter('week')" 
                            class="btn btn-sm {{ $timeFilter === 'week' ? 'btn-primary' : 'btn-outline' }}">
                        1 Minggu
                    </button>
                    <button wire:click="setTimeFilter('month')" 
                            class="btn btn-sm {{ $timeFilter === 'month' ? 'btn-primary' : 'btn-outline' }}">
                        1 Bulan
                    </button>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stat bg-base-100 shadow-xl rounded-lg">
                <div class="stat-figure text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <div class="stat-title">Total Pesanan</div>
                <div class="stat-value text-primary">{{ number_format($totalOrders) }}</div>
                <div class="stat-desc">{{ $filterLabel }}</div>
            </div>

            <div class="stat bg-base-100 shadow-xl rounded-lg">
                <div class="stat-figure text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
                <div class="stat-title">Pendapatan</div>
                <div class="stat-value text-secondary">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                <div class="stat-desc">{{ $filterLabel }}</div>
            </div>

            <div class="stat bg-base-100 shadow-xl rounded-lg">
                <div class="stat-figure text-accent">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div class="stat-title">Produk Terjual</div>
                <div class="stat-value text-accent">{{ number_format($totalProductsSold) }}</div>
                <div class="stat-desc">{{ $filterLabel }}</div>
            </div>

            <div class="stat bg-base-100 shadow-xl rounded-lg">
                <div class="stat-figure text-info">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                </div>
                <div class="stat-title">Pengguna</div>
                <div class="stat-value text-info">{{ number_format($totalCustomers) }}</div>
                <div class="stat-desc">Total pelanggan terdaftar</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Menu Utama</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Pengaturan Toko -->
                <a href="{{ route('admin.settings') }}" class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow duration-300">
                    <div class="card-body">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-primary/10 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="card-title text-lg">Pengaturan Toko</h3>
                                <p class="text-gray-600">Kelola informasi toko dan pengiriman</p>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Manajemen Refund -->
                <a href="{{ route('admin.refunds') }}" class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow duration-300">
                    <div class="card-body">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-warning/10 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="card-title text-lg">Manajemen Refund</h3>
                                <p class="text-gray-600">Kelola proses refund pesanan</p>
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

                <!-- Manajemen Pesanan -->
                <a href="{{ route('admin.orders') }}" class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow duration-300">
                    <div class="card-body">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-accent/10 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="card-title text-lg">Manajemen Pesanan</h3>
                                <p class="text-gray-600">Kelola pesanan dan pengiriman</p>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Manajemen Pengguna -->
                <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow duration-300 opacity-50">
                    <div class="card-body">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-info/10 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="card-title text-lg">Manajemen Pengguna</h3>
                                <p class="text-gray-600">Kelola akun pengguna sistem</p>
                                <span class="badge badge-warning badge-sm">Coming Soon</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Laporan -->
                <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow duration-300 opacity-50">
                    <div class="card-body">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-success/10 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 00-2-2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="card-title text-lg">Laporan</h3>
                                <p class="text-gray-600">Lihat laporan penjualan dan analitik</p>
                                <span class="badge badge-warning badge-sm">Coming Soon</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profil Admin -->
                <a href="/admin/profile" class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow duration-300">
                    <div class="card-body">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-warning/10 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="card-title text-lg">Profil Admin</h3>
                                <p class="text-gray-600">Kelola profil dan akun admin</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-xl mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Aktivitas Terbaru
                </h2>
                @if($recentActivities && $recentActivities->count() > 0)
                    <div class="space-y-4 max-h-96 overflow-y-auto">
                        @foreach($recentActivities as $activity)
                            <div class="border-l-4 pl-4 py-3 
                                @if($activity->action === 'courier_assigned') border-blue-500 bg-blue-50
                                @elseif($activity->action === 'order_shipped') border-yellow-500 bg-yellow-50
                                @elseif($activity->action === 'order_delivered') border-green-500 bg-green-50
                                @elseif($activity->action === 'delivery_cancelled') border-red-500 bg-red-50
                                @endif">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $activity->description }}
                                        </p>
                                        @if($activity->details)
                                            @php $details = is_string($activity->details) ? json_decode($activity->details, true) : $activity->details; @endphp
                                            <div class="mt-2 text-xs text-gray-600">
                                                @if(isset($details['courier_name']))
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Kurir: {{ $details['courier_name'] }}
                                                    </span>
                                                @endif
                                                @if(isset($details['delivery_proof']))
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-2">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Bukti Pengiriman
                                                    </span>
                                                @endif
                                                @if(isset($details['receipt_image']))
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 mr-2">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Struk Pembayaran
                                                    </span>
                                                @endif
                                                @if(isset($details['cancellation_reason']))
                                                    <div class="mt-1 text-red-600">
                                                        <strong>Alasan:</strong> {{ $details['cancellation_reason'] }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $activity->user ? $activity->user->name : 'System' }} • 
                                            {{ $activity->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <div class="ml-4">
                                        @if($activity->action === 'courier_assigned')
                                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                                                    <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1V8a1 1 0 00-1-1h-3z"></path>
                                                </svg>
                                            </div>
                                        @elseif($activity->action === 'order_shipped')
                                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                                </svg>
                                            </div>
                                        @elseif($activity->action === 'order_delivered')
                                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        @elseif($activity->action === 'delivery_cancelled')
                                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p>Belum ada aktivitas terbaru</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>