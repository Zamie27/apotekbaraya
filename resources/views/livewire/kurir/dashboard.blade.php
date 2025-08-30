<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard Kurir</h1>
        <p class="mt-2 text-gray-600">Selamat datang di dashboard kurir Apotek Baraya</p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Pending Deliveries -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Menunggu Pengiriman</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $pendingDeliveries }}</p>
                </div>
            </div>
        </div>

        <!-- In Transit -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Dalam Perjalanan</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $inTransitDeliveries }}</p>
                </div>
            </div>
        </div>

        <!-- Completed Today -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Selesai Hari Ini</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $completedToday }}</p>
                </div>
            </div>
        </div>

        <!-- Total Deliveries -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Pengiriman</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalDeliveries }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Menu Utama</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Manage Deliveries -->
            <a href="{{ route('kurir.deliveries') }}" 
               class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                <div class="p-2 bg-blue-500 rounded-lg text-white mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-gray-900">Manajemen Pengiriman</h3>
                    <p class="text-sm text-gray-600">Kelola status pengiriman dan upload foto konfirmasi</p>
                </div>
            </a>

            <!-- Profile -->
            <a href="{{ route('kurir.profile') }}" 
               class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                <div class="p-2 bg-green-500 rounded-lg text-white mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-gray-900">Profil</h3>
                    <p class="text-sm text-gray-600">Kelola informasi profil dan pengaturan akun</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Deliveries -->
    @if($recentDeliveries->count() > 0)
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Pengiriman Terbaru</h2>
            <a href="{{ route('kurir.deliveries') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                Lihat Semua
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Status</th>
                        <th>Estimasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentDeliveries as $delivery)
                    <tr>
                        <td>
                            <div class="font-semibold text-primary">{{ $delivery->order->order_number }}</div>
                            <div class="text-sm text-gray-500">{{ $delivery->order->created_at->format('d/m/Y') }}</div>
                        </td>
                        <td>{{ $delivery->order->user->name }}</td>
                        <td>
                            @php
                                $statusClass = match($delivery->delivery_status) {
                                    'pending' => 'badge-warning',
                                    'in_transit' => 'badge-info',
                                    'delivered' => 'badge-success',
                                    'failed' => 'badge-error',
                                    default => 'badge-neutral'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ $delivery->delivery_status_label }}</span>
                        </td>
                        <td>
                            @if($delivery->estimated_delivery)
                                {{ $delivery->estimated_delivery->format('d/m/Y H:i') }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>