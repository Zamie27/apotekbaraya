<div>
    <!-- Header Section -->
    <div class="container mx-auto px-4 py-6">
        <div class="text-center">
            <h1 class="text-3xl font-bold mb-2">Pencarian Produk</h1>
            <p class="text-base-content/70">Temukan obat dan produk kesehatan yang Anda butuhkan</p>
        </div>
    </div>

    <!-- Search Section -->
    <div class="container mx-auto px-4 py-8">
        <!-- Search Input -->
        <div class="max-w-2xl mx-auto mb-8">
            <div class="relative">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="query"
                    placeholder="Ketik nama obat, kategori, atau deskripsi produk..."
                    class="w-full px-4 py-3 pl-12 pr-12 text-lg border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                    autocomplete="off">

                <!-- Search Icon -->
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </div>


            </div>

            <!-- Search Info -->
            @if($query && strlen($query) < 2)
                <p class="mt-2 text-sm text-amber-600 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                Minimal 2 karakter untuk pencarian
                </p>
                @endif
        </div>

        <!-- Loading Indicator -->
        @if($isSearching)
        <div class="flex justify-center items-center py-8">
            <div class="flex items-center space-x-2 text-green-600">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm font-medium">Mencari produk...</span>
            </div>
        </div>
        @endif

        <!-- Search Results -->
        @if($query && strlen($query) >= 2 && !$isSearching)
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900">
                    Hasil Pencarian "{{ $query }}"
                </h2>
                <span class="text-sm text-gray-500">
                    {{ count($results) }} produk ditemukan
                </span>
            </div>

            @if(count($results) > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2 sm:gap-4 mb-6 sm:mb-8 mx-auto">
                @foreach($results as $product)
                <div class="card w-64 bg-base-100 shadow-xl group hover:shadow-2xl transition overflow-hidden">
                    <figure class="relative">
                        <a href="/produk/{{ $product->product_id }}">
                            <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="object-cover w-full h-64" />
                        </a>

                        <div class="absolute left-0 right-0 bottom-0 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-in-out">
                            @if(auth()->check())
                                <livewire:add-to-cart-button 
                                    :product-id="$product->product_id" 
                                    button-text="TAMBAH KE KERANJANG" 
                                    button-class="btn btn-success w-full font-bold rounded-none"
                                    :key="'search-popular-cart-'.$product->product_id" 
                                />
                            @else
                                <a href="/login" class="btn btn-success w-full font-bold rounded-none">
                                    TAMBAH KE KERANJANG
                                </a>
                            @endif
                        </div>

                        {{-- Category Badge --}}
                        <div class="absolute top-2 left-2">
                            <span class="badge badge-success badge-sm">{{ $product->category->name }}</span>
                        </div>

                        {{-- Prescription Required Badge --}}
                        @if($product->requires_prescription)
                        <div class="absolute top-2 right-2">
                            <span class="badge badge-warning badge-sm">Resep Dokter</span>
                        </div>
                        @endif

                        {{-- Discount Badge --}}
                        @if($product->is_on_discount)
                        <div class="absolute top-8 right-2">
                            <span class="badge badge-error badge-sm">-{{ $product->discount_percentage }}%</span>
                        </div>
                        @endif
                    </figure>
                    <div class="card-body p-2 sm:p-3 md:p-4">
                        <a href="/produk/{{ $product->product_id }}" class="card-title text-xs sm:text-sm md:text-base font-bold truncate hover:text-success line-clamp-2" title="{{ $product->name }}">
                            {{ $product->name }}
                        </a>

                        {{-- Price Display --}}
                        <div class="space-y-0.5 sm:space-y-1">
                            @if($product->is_on_discount)
                                {{-- Discount Price Layout --}}
                                <div class="flex flex-col gap-0.5 sm:gap-1">
                                    <div class="flex items-center gap-1 sm:gap-2">
                                        <p class="text-xs text-gray-400 line-through">{{ $product->formatted_price }}</p>
                                        <p class="text-xs sm:text-sm font-semibold text-success">{{ $product->formatted_final_price }}</p>
                                    </div>
                                    <span class="text-xs text-gray-500">/ {{ $product->unit }}</span>
                                </div>
                                {{-- Savings Info --}}
                                <div class="flex items-center gap-1">
                                    <span class="text-xs text-green-600 font-medium">💰 Hemat {{ $product->formatted_savings }}</span>
                                </div>
                            @else
                                {{-- Regular Price Layout --}}
                                <div class="flex flex-col gap-0.5">
                                    <p class="text-xs sm:text-sm font-semibold text-gray-600">{{ $product->formatted_price }}</p>
                                    <span class="text-xs text-gray-500">/ {{ $product->unit }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Stock Status --}}
                        <div class="flex items-center justify-between mt-1">
                            <span class="text-xs {{ $product->is_available ? 'text-success' : 'text-error' }}">
                                {{ $product->is_available ? 'Tersedia' : 'Habis' }}
                            </span>
                            <span class="text-xs text-gray-500">{{ $product->stock }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <!-- No Results -->
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M16 16s-1.5-2-4-2-4 2-4 2"></path>
                    <circle cx="9" cy="9" r="1" fill="currentColor"></circle>
                    <circle cx="15" cy="9" r="1" fill="currentColor"></circle>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Produk tidak ditemukan</h3>
                <p class="text-gray-500 mb-4">Coba gunakan kata kunci yang berbeda atau lebih umum</p>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 max-w-md mx-auto">
                    <h4 class="font-medium text-blue-900 mb-2">Tips Pencarian:</h4>
                    <ul class="text-sm text-blue-700 space-y-1 text-left">
                        <li>• Gunakan kata kunci yang lebih umum</li>
                        <li>• Periksa ejaan kata kunci</li>
                        <li>• Coba cari berdasarkan kategori obat</li>
                        <li>• Gunakan nama generik obat</li>
                    </ul>
                </div>
            </div>
            @endif
        </div>
        @endif

        <!-- Popular Products (when no search) -->
        @if(!$query || strlen($query) < 2)
            <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Produk Populer</h2>

            @if($popularProducts && count($popularProducts) > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2 sm:gap-4 mb-6 sm:mb-8 mx-auto">
                @foreach($popularProducts as $product)
                <div class="card w-64 bg-base-100 shadow-xl group hover:shadow-2xl transition overflow-hidden">
                    <figure class="relative">
                        <a href="/produk/{{ $product->product_id }}">
                            <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="object-cover w-full h-64" />
                        </a>

                        <div class="absolute left-0 right-0 bottom-0 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-in-out">
                            @if(auth()->check())
                                <livewire:add-to-cart-button 
                                    :product-id="$product->product_id" 
                                    button-text="TAMBAH KE KERANJANG" 
                                    button-class="btn btn-success w-full font-bold rounded-none"
                                    :key="'search-cart-'.$product->product_id" 
                                />
                            @else
                                <a href="/login" class="btn btn-success w-full font-bold rounded-none">
                                    TAMBAH KE KERANJANG
                                </a>
                            @endif
                        </div>

                        {{-- Popular Badge --}}
                        <div class="absolute top-2 left-2">
                            <span class="badge badge-warning badge-sm">Populer</span>
                        </div>

                        {{-- Category Badge --}}
                        <div class="absolute top-8 left-2">
                            <span class="badge badge-success badge-sm">{{ $product->category->name }}</span>
                        </div>

                        {{-- Prescription Required Badge --}}
                        @if($product->requires_prescription)
                        <div class="absolute top-2 right-2">
                            <span class="badge badge-warning badge-sm">Resep Dokter</span>
                        </div>
                        @endif

                        {{-- Discount Badge --}}
                        @if($product->is_on_discount)
                        <div class="absolute top-8 right-2">
                            <span class="badge badge-error badge-sm">-{{ $product->discount_percentage }}%</span>
                        </div>
                        @endif
                    </figure>
                    <div class="card-body p-2 sm:p-3 md:p-4">
                        <a href="/produk/{{ $product->product_id }}" class="card-title text-xs sm:text-sm md:text-base font-bold truncate hover:text-success line-clamp-2" title="{{ $product->name }}">
                            {{ $product->name }}
                        </a>

                        {{-- Price Display --}}
                        <div class="space-y-0.5 sm:space-y-1">
                            @if($product->is_on_discount)
                                {{-- Discount Price Layout --}}
                                <div class="flex flex-col gap-0.5 sm:gap-1">
                                    <div class="flex items-center gap-1 sm:gap-2">
                                        <p class="text-xs text-gray-400 line-through">{{ $product->formatted_price }}</p>
                                        <p class="text-xs sm:text-sm font-semibold text-success">{{ $product->formatted_final_price }}</p>
                                    </div>
                                    <span class="text-xs text-gray-500">/ {{ $product->unit }}</span>
                                </div>
                                {{-- Savings Info --}}
                                <div class="flex items-center gap-1">
                                    <span class="text-xs text-green-600 font-medium">💰 Hemat {{ $product->formatted_savings }}</span>
                                </div>
                            @else
                                {{-- Regular Price Layout --}}
                                <div class="flex flex-col gap-0.5">
                                    <p class="text-xs sm:text-sm font-semibold text-gray-600">{{ $product->formatted_price }}</p>
                                    <span class="text-xs text-gray-500">/ {{ $product->unit }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Stock Status --}}
                        <div class="flex items-center justify-between mt-1">
                            <span class="text-xs {{ $product->is_available ? 'text-success' : 'text-error' }}">
                                {{ $product->is_available ? 'Tersedia' : 'Habis' }}
                            </span>
                            <span class="text-xs text-gray-500">{{ $product->stock }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <x-icons.cube class="w-24 h-24 mx-auto mb-4 text-gray-300" />
                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada produk populer</h3>
                <p class="text-gray-500">Produk populer akan muncul di sini</p>
            </div>
            @endif
    </div>
    @endif
</div>
</div>