<div>
    {{-- Product Description Page - Accessible by guests --}}
    @if (session()->has('message'))
    <div class="alert alert-success mb-4">
        {{ session('message') }}
    </div>
    @endif

    @if (session()->has('error'))
    <div class="alert alert-error mb-4">
        {{ session('error') }}
    </div>
    @endif

    <div class="container mx-auto my-6 sm:my-10 px-3 sm:px-4 md:flex md:gap-10 items-start">
        <!-- Gambar Produk 1:1 -->
        <div class="md:w-auto w-full flex justify-center mb-4 sm:mb-6 md:mb-0">
            <div class="relative w-full max-w-[380px] sm:max-w-[500px] md:max-w-[640px] aspect-square rounded-box shadow-md bg-gray-100 overflow-hidden">
                <img
                    src="{{ $product->primary_image_url }}"
                    alt="{{ $product->name }}"
                    class="object-cover w-full h-full" />

                {{-- Product Badges --}}
                <div class="absolute top-2 sm:top-4 left-2 sm:left-4">
                    <span class="badge badge-success badge-sm sm:badge-md">{{ $product->category->name }}</span>
                </div>

                @if($product->requires_prescription)
                <div class="absolute top-2 sm:top-4 right-2 sm:right-4">
                    <span class="badge badge-warning badge-sm sm:badge-md">Resep Dokter</span>
                </div>
                @endif

                @if($product->is_on_discount)
                <div class="absolute top-8 sm:top-12 right-2 sm:right-4">
                    <span class="badge badge-error badge-sm sm:badge-md">-{{ $product->discount_percentage }}%</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Deskripsi Produk -->
        <div class="flex-1 w-full">
            <div class="text-xs sm:text-sm text-gray-500 mb-3 sm:mb-4">
                <a href="/" class="hover:underline">Home</a> &raquo;
                <a href="/" class="hover:underline">Produk</a> &raquo;
                <span class="text-gray-700">{{ $product->name }}</span>
            </div>

            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-green-600 mb-2">{{ $product->name }}</h1>

            {{-- Price Display --}}
            <div class="flex items-center gap-2 sm:gap-3 mb-3 sm:mb-4 flex-wrap">
                @if($product->is_on_discount)
                <p class="text-sm sm:text-lg text-gray-400 line-through">{{ $product->formatted_price }}</p>
                <p class="text-lg sm:text-2xl font-bold text-success">{{ $product->formatted_final_price }}</p>
                @else
                <p class="text-lg sm:text-2xl font-bold text-gray-700">{{ $product->formatted_price }}</p>
                @endif
                <span class="text-sm sm:text-lg text-gray-500">/ {{ $product->unit }}</span>
            </div>

            {{-- Stock Status --}}
            <div class="flex items-center gap-2 sm:gap-4 mb-3 sm:mb-4 flex-wrap">
                <span class="text-xs sm:text-sm {{ $product->is_available ? 'text-success' : 'text-error' }} font-medium">
                    {{ $product->is_available ? '✓ Tersedia' : '✗ Stok Habis' }}
                </span>
                <span class="text-xs sm:text-sm text-gray-500">Satuan: {{ $product->unit }}</span>
            </div>

            <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6">
                {{ $product->description ?: 'Deskripsi produk akan segera tersedia.' }}
            </p>

            {{-- Specifications --}}
            @if($product->specifications)
            <div class="mb-4 sm:mb-6">
                <h3 class="text-sm sm:text-base font-semibold text-gray-800 mb-2">Spesifikasi:</h3>
                <div class="bg-gray-50 p-3 sm:p-4 rounded-lg">
                    @foreach(collect($product->specifications)->reject(function($v, $k){ return str($k)->lower()->value() === 'lainnya'; }) as $key => $value)
                        <div class="flex justify-between py-1 border-b border-gray-200 last:border-b-0">
                            <span class="text-xs sm:text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $key) }}:</span>
                            <span class="text-xs sm:text-sm text-gray-800 font-medium">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Quantity and Add to Cart --}}
            @if($product->is_available)
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4 mb-4 sm:mb-6">
                <div class="flex border rounded-box overflow-hidden w-fit">
                    <button
                        wire:click="decreaseQuantity"
                        class="px-2 sm:px-3 py-1 bg-gray-100 hover:bg-gray-200 {{ $quantity <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ $quantity <= 1 ? 'disabled' : '' }}>
                        -
                    </button>
                    <span class="px-3 sm:px-4 py-1 bg-white min-w-[40px] sm:min-w-[50px] text-center text-sm sm:text-base">{{ $quantity }}</span>
                    <button
                        wire:click="increaseQuantity"
                        class="px-2 sm:px-3 py-1 bg-gray-100 hover:bg-gray-200 {{ $quantity >= $product->stock ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ $quantity >= $product->stock ? 'disabled' : '' }}>
                        +
                    </button>
                </div>
                @php
                    $user = auth()->user();
                    $isCustomer = $user && method_exists($user, 'hasRole') ? $user->hasRole('pelanggan') : false;
                @endphp
                @if($product->requires_prescription && $isCustomer)
                    <a href="{{ route('customer.prescriptions.create') }}" class="btn btn-warning btn-md sm:btn-lg py-3 sm:py-2 uppercase flex-1 sm:flex-none">
                        Unggah Resep Dokter
                    </a>
                @else
                    <button
                        wire:click="addToCart"
                        class="btn btn-success btn-md sm:btn-lg py-3 sm:py-2 uppercase flex-1 sm:flex-none"
                        wire:loading.attr="disabled"
                        wire:loading.class="loading">
                        <span wire:loading.remove class="text-xs sm:text-sm">Tambah ke Keranjang</span>
                        <span wire:loading class="text-xs sm:text-sm">Menambahkan...</span>
                    </button>
                @endif
            </div>
            @else
            <div class="mb-4 sm:mb-6">
                <button class="btn btn-disabled btn-md sm:btn-lg py-3 sm:py-2 uppercase w-full sm:w-auto" disabled>
                    Stok Habis
                </button>
            </div>
            @endif

            {{-- Prescription Warning --}}
            @if($product->requires_prescription)
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 sm:p-4 rounded-box shadow-sm mb-4 sm:mb-6">
                <h2 class="text-sm sm:text-base font-bold text-yellow-600 mb-2">⚠️ Perhatian</h2>
                <p class="text-xs sm:text-sm text-yellow-700">
                    Produk ini memerlukan resep dokter. Pastikan Anda memiliki resep yang valid sebelum melakukan pemesanan.
                </p>
            </div>
            @endif

            <div class="bg-green-50 border-l-4 border-green-500 p-3 sm:p-4 rounded-box shadow-sm">
                <h2 class="text-sm sm:text-base font-bold text-green-600 mb-2">Kenapa belanja di Apotek Baraya?</h2>
                <ul class="list-disc list-inside text-xs sm:text-sm text-gray-700 space-y-1">
                    <li>Pengiriman cepat dan aman</li>
                    <li>Produk asli dan terjamin kualitasnya</li>
                    <li>Bisa COD di area tertentu</li>
                    <li>Konsultasi langsung dengan apoteker</li>
                </ul>
            </div>
        </div>
    </div>
</div>