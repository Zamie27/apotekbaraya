<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Dashboard Admin</h1>
            <p class="text-gray-600 mt-2">Selamat datang di panel administrasi Apotek Baraya</p>
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
                <div class="stat-value text-primary">0</div>
                <div class="stat-desc">Hari ini</div>
            </div>

            <div class="stat bg-base-100 shadow-xl rounded-lg">
                <div class="stat-figure text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
                <div class="stat-title">Pendapatan</div>
                <div class="stat-value text-secondary">Rp 0</div>
                <div class="stat-desc">Bulan ini</div>
            </div>

            <div class="stat bg-base-100 shadow-xl rounded-lg">
                <div class="stat-figure text-accent">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div class="stat-title">Produk</div>
                <div class="stat-value text-accent">0</div>
                <div class="stat-desc">Total produk</div>
            </div>

            <div class="stat bg-base-100 shadow-xl rounded-lg">
                <div class="stat-figure text-info">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                </div>
                <div class="stat-title">Pengguna</div>
                <div class="stat-value text-info">0</div>
                <div class="stat-desc">Total pengguna</div>
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
                <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow duration-300 opacity-50">
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
                                <span class="badge badge-warning badge-sm">Coming Soon</span>
                            </div>
                        </div>
                    </div>
                </div>

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
                <div class="text-center py-8 text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p>Belum ada aktivitas terbaru</p>
                </div>
            </div>
        </div>
    </div>
</div>