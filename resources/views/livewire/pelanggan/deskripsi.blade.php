<div class="container mx-auto px-4 py-6">
    <!-- Card Container -->
    <div class="card w-full bg-base-100 shadow-xl">
        <div class="flex flex-col md:flex-row">
            <!-- Gambar Produk -->
            <div class="w-full md:w-1/4 flex justify-center">
                <img src="/src/img/logo.png" alt="Happy Tos Keripik Tortilla" class="w-64 h-64 object-cover" />
            </div>

            <!-- Deskripsi Produk -->
            <div class="w-full md:w-1/2 p-6">
                <!-- Nama dan Harga -->
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold">Happy Tos Keripik Tortilla Hijau</h2>
                    <span class="badge badge-accent text-white">Rp 10.000</span>
                </div>

                <!-- Deskripsi -->
                <div class="mt-4 text-gray-700">
                    <p>
                        Terbuat dari biji jagung pilihan. Digoreng dengan minyak kelapa sawit bermutu. Dapat dikonsumsi kapan dan di mana saja dalam berbagai suasana.
                    </p>
                    <p class="mt-2">
                        Happy Tos Real Corn Hijau merupakan keripik renyah dan lezat yang terbuat dari biji jagung pilihan. Keripik Happy Tos diolah secara teliti dan digoreng dengan minyak kelapa sawit bermutu untuk menghasilkan keripik yang enak dengan tingkat kerenyahan yang pas.
                    </p>
                </div>
            </div>

            <!-- Keranjang dan Tombol di Kanan -->
            <div class="w-full md:w-1/4 p-6 flex flex-col items-center justify-between">
                <!-- Total Pembelian -->
                <div class="text-center mb-4">
                    <h3 class="text-lg font-semibold">Total Pembelian</h3>
                    <span class="text-xl font-bold">Rp 10.000</span>
                </div>

                <!-- Tombol Keranjang -->
                <div class="flex flex-col items-center space-y-4">
                    <!-- Tombol Tambah ke Keranjang -->
                    <button class="btn btn-primary text-white w-full">+ Keranjang</button>

                    <!-- Ikon Keranjang dengan Jumlah -->
                    <div class="flex items-center space-x-2">
                        <i data-feather="shopping-cart" class="text-xl"></i>
                        <span>Jumlah Pembelian: 1</span>
                    </div>
                </div>

                <!-- Tombol Checkout -->
                <div class="mt-6 w-full">
                    <button class="btn btn-accent text-white w-full">Checkout</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        feather.replace(); // Initialize Feather Icons
    </script>
@endpush
