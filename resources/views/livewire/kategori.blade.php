<div>
    {{-- Flash Messages --}}
    @if (session()->has('success'))
    <div class="alert alert-success mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if (session()->has('error'))
    <div class="alert alert-error mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- Filter and Search Section --}}
    <div class="bg-white p-3 sm:p-4 rounded-lg shadow-sm border my-4 sm:my-6">
        <div class="flex flex-col gap-3 sm:gap-4">
            {{-- Search Input --}}
            <div class="w-full">
                <div class="relative">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari produk..."
                        class="input input-bordered w-full pl-10 text-sm sm:text-base">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <!-- Search Info -->
                @if($search && strlen($search) < 2)
                    <p class="mt-1 text-xs text-amber-600 flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    Minimal 2 karakter
                    </p>
                    @endif
            </div>
        </div>
    </div>

    {{-- Category Navigation --}}
    <div class="mb-4 sm:mb-6">
        <div class="relative flex justify-center w-full mx-1 sm:mx-2">
            <div class="absolute left-0 z-10 top-1/2 -translate-y-1/2 backdrop-blur-sm bg-base-200/60 rounded-full">
                <button
                    id="scrollLeftBtnKategori"
                    onclick="scrollKategoriPage(-300)"
                    class="btn btn-circle btn-sm sm:btn-md bg-base-200 shadow-md">
                    <svg class="w-4 h-4 sm:w-6 sm:h-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 8 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 1 1.3 6.326a.91.91 0 0 0 0 1.348L7 13" />
                    </svg>
                </button>
            </div>

            <div
                id="scrollKategoriPage"
                class="flex max-w-full gap-2 px-3 sm:px-4 py-2 overflow-x-auto snap-x scroll-smooth scrollbar-hide">
                <a href="/kategori" class="btn btn-xs sm:btn-sm {{ !$categoryId ? 'btn-success' : 'btn-outline btn-success' }} whitespace-nowrap">
                    Semua Kategori
                </a>
                @foreach($categories as $cat)
                <a href="/kategori/{{ $cat->slug }}" class="btn btn-xs sm:btn-sm {{ $categoryId == $cat->category_id ? 'btn-success' : 'btn-outline btn-success' }} whitespace-nowrap">
                    {{ $cat->name }} ({{ $cat->products_count }})
                </a>
                @endforeach
            </div>

            <div class="absolute right-0 z-10 top-1/2 -translate-y-1/2 backdrop-blur-sm bg-base-200/60 rounded-full">
                <button
                    id="scrollRightBtnKategori"
                    onclick="scrollKategoriPage(300)"
                    class="btn btn-circle btn-sm sm:btn-md bg-base-200 shadow-md">
                    <svg class="w-4 h-4 sm:w-6 sm:h-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 8 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 13 5.7-5.326a.909.909 0 0 0 0-1.348L1 1" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Search Results Info --}}
    @if($search && strlen($search) >= 2)
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900">
                @if($category)
                Hasil Pencarian "{{ $search }}" di {{ $category->name }}
                @else
                Hasil Pencarian "{{ $search }}"
                @endif
            </h2>
            <span class="text-sm text-gray-500">
                {{ $products->total() }} produk ditemukan
            </span>
        </div>
    </div>
    @endif

    {{-- Products Per Page Selector --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-700">Tampilkan:</span>
            <select wire:model.live="perPage" class="select select-bordered select-sm w-20">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span class="text-sm text-gray-700">produk per halaman</span>
        </div>

        @if($products->total() > 0)
        <div class="text-sm text-gray-500">
            Menampilkan {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} produk
        </div>
        @endif
    </div>

    {{-- Products Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2 sm:gap-3 md:gap-4 mb-4 sm:mb-6 md:mb-8">
        @forelse($products as $product)
        <div class="card w-full bg-base-100 shadow-xl group hover:shadow-2xl transition overflow-hidden">
            <figure class="relative pt-2">
                <a href="/produk/{{ $product->product_id }}">
                    <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="object-cover w-full h-40 sm:h-48 lg:h-64" />
                </a>

                <div class="absolute left-0 right-0 bottom-0 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-in-out">
                    @if($isAuthenticated)
                        <livewire:add-to-cart-button 
                            :product-id="$product->product_id" 
                            button-text="TAMBAH" 
                            button-class="btn btn-success w-full font-bold rounded-none text-xs sm:text-sm"
                            :key="'kategori-cart-'.$product->product_id" 
                        />
                    @else
                        <a href="/login" class="btn btn-success w-full font-bold rounded-none text-xs sm:text-sm">
                            TAMBAH
                        </a>
                    @endif
                </div>

                {{-- Category Badge --}}
                <div class="absolute top-1 sm:top-2 left-1 sm:left-2">
                    <span class="badge badge-success badge-xs sm:badge-sm text-xs">{{ $product->category->name }}</span>
                </div>

                {{-- Prescription Required Badge --}}
                @if($product->requires_prescription)
                <div class="absolute top-1 sm:top-2 right-1 sm:right-2">
                    <span class="badge badge-warning badge-xs sm:badge-sm text-xs">Resep Dokter</span>
                </div>
                @endif

                {{-- Discount Badge --}}
                @if($product->is_on_discount)
                <div class="absolute top-6 sm:top-8 right-1 sm:right-2">
                    <span class="badge badge-error badge-xs sm:badge-sm text-xs">-{{ $product->discount_percentage }}%</span>
                </div>
                @endif
            </figure>
            <div class="card-body p-1.5 sm:p-2 md:p-3 lg:p-4">
                <a href="/produk/{{ $product->product_id }}" class="card-title text-xs sm:text-sm md:text-base font-bold line-clamp-2 hover:text-success" title="{{ $product->name }}">
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
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-6 sm:py-8 md:py-12 px-4">
            @if($search && strlen($search) >= 2)
            <!-- No Search Results -->
            <div class="text-center">
                <svg class="mx-auto h-10 w-10 sm:h-12 sm:w-12 md:h-16 md:w-16 text-gray-300 mb-3 sm:mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M16 16s-1.5-2-4-2-4 2-4 2"></path>
                    <circle cx="9" cy="9" r="1" fill="currentColor"></circle>
                    <circle cx="15" cy="9" r="1" fill="currentColor"></circle>
                </svg>
                <h3 class="text-sm sm:text-base md:text-lg font-medium text-gray-900 mb-1 sm:mb-2">Produk tidak ditemukan</h3>
                <p class="text-xs sm:text-sm md:text-base text-gray-500 mb-3 sm:mb-4">
                    @if($category)
                    Tidak ada produk yang cocok dengan pencarian "{{ $search }}" di kategori {{ $category->name }}
                    @else
                    Tidak ada produk yang cocok dengan pencarian "{{ $search }}"
                    @endif
                </p>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 max-w-md mx-auto mb-4">
                    <h4 class="font-medium text-blue-900 mb-2">Tips Pencarian:</h4>
                    <ul class="text-sm text-blue-700 space-y-1 text-left">
                        <li>• Gunakan kata kunci yang lebih umum</li>
                        <li>• Periksa ejaan kata kunci</li>
                        <li>• Coba gunakan sinonim atau kata lain</li>
                        <li>• Kurangi jumlah kata kunci</li>
                    </ul>
                </div>

                <button wire:click="clearSearch" class="btn btn-outline btn-sm">Hapus Pencarian</button>
            </div>
            @else
            <!-- No Products in Category -->
            <div class="text-gray-500">
                <x-icons.shopping-bag class="w-24 h-24 mx-auto mb-4 text-gray-300" />

                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada produk ditemukan</h3>
                @if($category)
                <p class="text-gray-500">Tidak ada produk dalam kategori {{ $category->name }}</p>
                @else
                <p class="text-gray-500">Belum ada produk yang tersedia</p>
                @endif
            </div>
            @endif
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($products->hasPages())
    <div class="mt-6 sm:mt-8 flex justify-center px-4">
        <nav class="flex items-center space-x-1" aria-label="Pagination">
            {{-- Previous Page Link --}}
            @if($products->onFirstPage())
            <span class="px-2 sm:px-3 py-2 text-gray-400 cursor-not-allowed">
                <svg class="h-4 w-4 sm:h-5 sm:w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                </svg>
            </span>
            @else
            <button wire:click="previousPage" class="px-2 sm:px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors">
                <svg class="h-4 w-4 sm:h-5 sm:w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                </svg>
            </button>
            @endif

            {{-- Pagination Elements --}}
            @php
            $start = max(1, $products->currentPage() - 2);
            $end = min($products->lastPage(), $products->currentPage() + 2);
            @endphp

            {{-- First Page --}}
            @if($start > 1)
            <button wire:click="gotoPage(1)" class="px-2 sm:px-3 py-2 text-xs sm:text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">1</button>
            @if($start > 2)
            <span class="px-2 sm:px-3 py-2 text-xs sm:text-sm text-gray-500">...</span>
            @endif
            @endif

            {{-- Page Numbers --}}
            @for($page = $start; $page <= $end; $page++)
                @if($page==$products->currentPage())
                <span class="px-2 sm:px-3 py-2 text-xs sm:text-sm font-semibold text-success border-b-2 border-success">{{ $page }}</span>
                @else
                <button wire:click="gotoPage({{ $page }})" class="px-2 sm:px-3 py-2 text-xs sm:text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">{{ $page }}</button>
                @endif
                @endfor

                {{-- Last Page --}}
                @if($end < $products->lastPage())
                    @if($end < $products->lastPage() - 1)
                        <span class="px-2 sm:px-3 py-2 text-xs sm:text-sm text-gray-500">...</span>
                        @endif
                        <button wire:click="gotoPage({{ $products->lastPage() }})" class="px-2 sm:px-3 py-2 text-xs sm:text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">{{ $products->lastPage() }}</button>
                        @endif

                        {{-- Next Page Link --}}
                        @if($products->hasMorePages())
                        <button wire:click="nextPage" class="px-2 sm:px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors">
                            <svg class="h-4 w-4 sm:h-5 sm:w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        @else
                        <span class="px-2 sm:px-3 py-2 text-gray-400 cursor-not-allowed">
                            <svg class="h-4 w-4 sm:h-5 sm:w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        @endif
        </nav>
    </div>
    @endif



    <style>
        .scrollbar-hide {
            -ms-overflow-style: none;
            /* Internet Explorer 10+ */
            scrollbar-width: none;
            /* Firefox */
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
            /* Safari and Chrome */
        }
    </style>

    <script>
        const scrollContainerKategori = document.getElementById('scrollKategoriPage');
        const scrollLeftBtnKategori = document.getElementById('scrollLeftBtnKategori');
        const scrollRightBtnKategori = document.getElementById('scrollRightBtnKategori');

        function updateButtonVisibilityKategori() {
            const scrollWidth = scrollContainerKategori.scrollWidth;
            const clientWidth = scrollContainerKategori.clientWidth;
            const scrollLeft = scrollContainerKategori.scrollLeft;

            if (scrollWidth <= clientWidth) {
                scrollLeftBtnKategori.classList.add('hidden');
                scrollRightBtnKategori.classList.add('hidden');
            } else {
                scrollLeftBtnKategori.classList.toggle('hidden', scrollLeft <= 0);
                scrollRightBtnKategori.classList.toggle('hidden', (scrollLeft + clientWidth) >= scrollWidth - 1);
            }
        }

        function scrollKategoriPage(amount) {
            scrollContainerKategori.scrollBy({
                left: amount,
                behavior: 'smooth'
            });
        }

        window.addEventListener('load', updateButtonVisibilityKategori);
        window.addEventListener('resize', updateButtonVisibilityKategori);
        scrollContainerKategori.addEventListener('scroll', updateButtonVisibilityKategori);
    </script>
</div>