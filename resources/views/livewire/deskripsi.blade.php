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

    <div class="container mx-auto my-10 px-4 md:flex md:gap-10 items-start">
        <!-- Gambar Produk dengan ukuran fix -->
        <div class="md:w-auto w-full flex justify-center mb-6 md:mb-0">
            <div class="relative">
                <img
                    src="{{ $product->primary_image_url }}"
                    alt="{{ $product->name }}"
                    class="rounded-box object-cover w-[400px] h-[400px] shadow-md bg-gray-100" />

                {{-- Product Badges --}}
                <div class="absolute top-4 left-4">
                    <span class="badge badge-success">{{ $product->category->name }}</span>
                </div>

                @if($product->requires_prescription)
                <div class="absolute top-4 right-4">
                    <span class="badge badge-warning">Resep Dokter</span>
                </div>
                @endif

                @if($product->is_on_discount)
                <div class="absolute top-12 right-4">
                    <span class="badge badge-error">-{{ $product->discount_percentage }}%</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Deskripsi Produk -->
        <div class="flex-1 w-full">
            <div class="text-sm text-gray-500 mb-4">
                <a href="/" class="hover:underline">Home</a> &raquo;
                <a href="/" class="hover:underline">Produk</a> &raquo;
                <span class="text-gray-700">{{ $product->name }}</span>
            </div>

            <h1 class="text-3xl font-bold text-green-600 mb-2">{{ $product->name }}</h1>

            {{-- Price Display --}}
            <div class="flex items-center gap-3 mb-4">
                @if($product->is_on_discount)
                <p class="text-lg text-gray-400 line-through">{{ $product->formatted_price }}</p>
                <p class="text-2xl font-bold text-success">{{ $product->formatted_final_price }}</p>
                @else
                <p class="text-2xl font-bold text-gray-700">{{ $product->formatted_price }}</p>
                @endif
                <span class="text-lg text-gray-500">/ {{ $product->unit }}</span>
            </div>

            {{-- Stock Status --}}
            <div class="flex items-center gap-4 mb-4">
                <span class="text-sm {{ $product->is_available ? 'text-success' : 'text-error' }} font-medium">
                    {{ $product->is_available ? '✓ Tersedia' : '✗ Stok Habis' }}
                </span>
                <span class="text-sm text-gray-500">Stok: {{ $product->stock }} {{ $product->unit }}</span>
            </div>

            <p class="text-gray-600 mb-6">
                {{ $product->description ?: 'Deskripsi produk akan segera tersedia.' }}
            </p>

            {{-- Specifications --}}
            @if($product->specifications)
            <div class="mb-6">
                <h3 class="font-semibold text-gray-800 mb-2">Spesifikasi:</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    @foreach($product->specifications as $key => $value)
                    <div class="flex justify-between py-1 border-b border-gray-200 last:border-b-0">
                        <span class="text-gray-600 capitalize">{{ str_replace('_', ' ', $key) }}:</span>
                        <span class="text-gray-800 font-medium">{{ $value }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Quantity and Add to Cart --}}
            @if($product->is_available)
            <div class="flex items-center gap-4 mb-6">
                <div class="flex border rounded-box overflow-hidden">
                    <button
                        wire:click="decreaseQuantity"
                        class="px-3 py-1 bg-gray-100 hover:bg-gray-200 {{ $quantity <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ $quantity <= 1 ? 'disabled' : '' }}>
                        -
                    </button>
                    <span class="px-4 py-1 bg-white min-w-[50px] text-center">{{ $quantity }}</span>
                    <button
                        wire:click="increaseQuantity"
                        class="px-3 py-1 bg-gray-100 hover:bg-gray-200 {{ $quantity >= $product->stock ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ $quantity >= $product->stock ? 'disabled' : '' }}>
                        +
                    </button>
                </div>
                <button wire:click="addToCart" class="btn btn-success uppercase">
                    Tambah ke Keranjang
                </button>
            </div>
            @else
            <div class="mb-6">
                <button class="btn btn-disabled uppercase" disabled>
                    Stok Habis
                </button>
            </div>
            @endif

            {{-- Prescription Warning --}}
            @if($product->requires_prescription)
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-box shadow-sm mb-6">
                <h2 class="font-bold text-yellow-600 mb-2">⚠️ Perhatian</h2>
                <p class="text-yellow-700">
                    Produk ini memerlukan resep dokter. Pastikan Anda memiliki resep yang valid sebelum melakukan pemesanan.
                </p>
            </div>
            @endif

            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-box shadow-sm">
                <h2 class="font-bold text-green-600 mb-2">Kenapa belanja di Apotek Baraya?</h2>
                <ul class="list-disc list-inside text-gray-700">
                    <li>Pengiriman cepat dan aman</li>
                    <li>Produk asli dan terjamin kualitasnya</li>
                    <li>Bisa COD di area tertentu</li>
                    <li>Konsultasi langsung dengan apoteker</li>
                </ul>
            </div>
        </div>
    </div>
</div>