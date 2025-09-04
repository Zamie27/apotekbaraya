<div>
    {{-- Welcome Section --}}
    @if($isAuthenticated)
    <div class="bg-gradient-to-r from-green-50 to-blue-50 p-3 sm:p-4 rounded-lg my-4 sm:my-6 border border-green-200">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg sm:text-xl font-semibold text-gray-800">Selamat datang kembali, {{ $currentUser->name }}! 👋</h2>
                <p class="text-sm sm:text-base text-gray-600 mt-1">Temukan obat dan produk kesehatan yang Anda butuhkan</p>
            </div>
        </div>
    </div>
    @else
    <div class="bg-gradient-to-r from-blue-50 to-green-50 p-3 sm:p-4 rounded-lg my-4 sm:my-6 border border-blue-200">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg sm:text-xl font-semibold text-gray-800">Selamat datang di Apotek Baraya! 🏥</h2>
                <p class="text-sm sm:text-base text-gray-600 mt-1">Jelajahi produk kesehatan terpercaya untuk keluarga Anda</p>
            </div>
            <div class="hidden md:block">
                <a href="/login" class="btn btn-success btn-sm">Masuk</a>
            </div>
        </div>
    </div>
    @endif

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

    <div class="relative flex justify-center w-full mx-1 sm:mx-2 mb-3">
        <div class="absolute left-0 z-10 top-1/2 -translate-y-1/2 backdrop-blur-sm bg-base-200/60 rounded-full">
            <button
                id="scrollLeftBtn"
                onclick="scrollKategori(-300)"
                class="btn btn-circle btn-sm sm:btn-md bg-base-200 shadow-md">
                <svg class="w-4 h-4 sm:w-6 sm:h-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 8 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 1 1.3 6.326a.91.91 0 0 0 0 1.348L7 13" />
                </svg>
            </button>
        </div>

        <div
            id="scrollKategori"
            class="flex max-w-full gap-2 sm:gap-4 px-3 sm:px-4 py-2 overflow-x-auto snap-x scroll-smooth no-scrollbar">
            {{-- Show All Products Button --}}
            <a href="/kategori"
                wire:click="filterByCategory(null)"
                class="rounded-lg btn {{ $selectedCategory === null ? 'btn-success' : 'btn-outline btn-success' }} text-white btn-sm sm:btn-md whitespace-nowrap">
                Semua Kategori
            </a>

            {{-- Category Buttons from Database --}}
            @foreach($categories as $category)
            <a href="/kategori/{{ $category->slug }}"
                class="rounded-lg btn btn-success btn-sm sm:btn-md text-white whitespace-nowrap hover:scale-105 transition-transform">
                {{ $category->name }}
            </a>
            @endforeach
        </div>

        <div class="absolute right-0 z-10 top-1/2 -translate-y-1/2 backdrop-blur-sm bg-base-200/60 rounded-full">
            <button
                id="scrollRightBtn"
                onclick="scrollKategori(300)"
                class="btn btn-circle btn-sm sm:btn-md bg-base-200 shadow-md">
                <svg class="w-4 h-4 sm:w-6 sm:h-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 8 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 13 5.7-5.326a.909.909 0 0 0 0-1.348L1 1" />
                </svg>
            </button>
        </div>
    </div>

    <!-- card -->
    <div class="container grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2 sm:gap-3 md:gap-4 lg:gap-6 mx-auto my-3 sm:my-5 px-1 sm:px-2 md:px-4">
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
                            :key="'dashboard-cart-'.$product->product_id" 
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
            <div class="text-gray-500">
                <svg class="w-10 h-10 sm:w-12 sm:h-12 md:w-16 md:h-16 mx-auto mb-3 sm:mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-8V4a1 1 0 00-1-1H7a1 1 0 00-1 1v1m8 0V4.5"></path>
                </svg>
                <h3 class="text-sm sm:text-base md:text-lg font-medium text-gray-900 mb-1 sm:mb-2">Tidak ada produk</h3>
                <p class="text-xs sm:text-sm md:text-base text-gray-500">{{ $selectedCategory ? 'Tidak ada produk dalam kategori ini.' : 'Belum ada produk yang tersedia.' }}</p>
            </div>
        </div>
        @endforelse
    </div>

    <script>
        const scrollContainer = document.getElementById('scrollKategori');
        const scrollLeftBtn = document.getElementById('scrollLeftBtn');
        const scrollRightBtn = document.getElementById('scrollRightBtn');

        function updateButtonVisibility() {
            const scrollWidth = scrollContainer.scrollWidth;
            const clientWidth = scrollContainer.clientWidth;
            const scrollLeft = scrollContainer.scrollLeft;

            if (scrollWidth <= clientWidth) {
                scrollLeftBtn.classList.add('hidden');
                scrollRightBtn.classList.add('hidden');
            } else {
                scrollLeftBtn.classList.toggle('hidden', scrollLeft <= 0);
                scrollRightBtn.classList.toggle('hidden', (scrollLeft + clientWidth) >= scrollWidth - 1);
            }
        }

        function scrollKategori(amount) {
            scrollContainer.scrollBy({
                left: amount,
                behavior: 'smooth'
            });
        }

        window.addEventListener('load', updateButtonVisibility);
        window.addEventListener('resize', updateButtonVisibility);
        scrollContainer.addEventListener('scroll', updateButtonVisibility);
    </script>

</div>