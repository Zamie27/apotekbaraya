<div class="min-h-screen bg-base-100">
    <div class="container mx-auto px-2 sm:px-4 py-4 sm:py-6">
        {{-- Page Header --}}
        <div class="mb-4 sm:mb-8">
            <div class="flex items-center gap-2 sm:gap-3 mb-3">
                <div class="p-2 sm:p-3 bg-primary/10 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-8 sm:w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl sm:text-3xl font-bold text-gray-900">Pengaturan Toko</h1>
                    <p class="text-sm sm:text-base text-gray-600 mt-1 hidden sm:block">Kelola informasi toko dan konfigurasi pengiriman</p>
                </div>
            </div>
        
        {{-- Breadcrumb --}}
        <div class="text-sm breadcrumbs">
            <ul>
                <li><a href="{{ route('admin.dashboard') }}" class="text-primary hover:text-primary-focus">Dashboard</a></li>
                <li class="text-gray-500">Pengaturan Toko</li>
            </ul>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if($successMessage)
    <div class="alert alert-success mb-6 shadow-lg" wire:click="clearMessages">
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="ml-2">{{ $successMessage }}</span>
        </div>
        <button class="btn btn-sm btn-ghost" wire:click="clearMessages">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
    @endif

    @if($errorMessage)
    <div class="alert alert-error mb-6 shadow-lg" wire:click="clearMessages">
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="ml-2">{{ $errorMessage }}</span>
        </div>
        <button class="btn btn-sm btn-ghost" wire:click="clearMessages">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
    @endif

    {{-- Main Content with Tabs --}}
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body p-0">
            {{-- Tab Navigation --}}
            <div class="tabs tabs-boxed bg-base-200 p-1 mb-4 sm:mb-6 flex flex-wrap gap-1">
                <button id="tab-store-info" class="tab tab-active flex-shrink-0 flex-1 sm:flex-initial" onclick="showTab('store-info')">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span class="text-xs sm:text-sm font-medium">Informasi Toko</span>
                </button>
                <button id="tab-shipping" class="tab flex-shrink-0 flex-1 sm:flex-initial" onclick="showTab('shipping')">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM21 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                    <span class="text-xs sm:text-sm font-medium">Pengaturan Pengiriman</span>
                </button>
            </div>

            {{-- Tab Content --}}
            <div class="p-6 lg:p-8">
                {{-- Store Information Tab --}}
                <div id="content-store-info" class="tab-content">
                    <div class="mb-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Informasi Dasar Toko</h3>
                        <p class="text-gray-600 text-sm">Kelola informasi dasar dan kontak toko Anda</p>
                    </div>

                    <div class="max-w-2xl">
                        <div class="bg-base-50 p-4 sm:p-6 rounded-lg border border-base-200 space-y-6">
                            {{-- Basic Information Section --}}
                            <div>
                                <h4 class="font-medium text-gray-900 mb-4 flex items-center text-sm sm:text-base">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-2 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Informasi Dasar
                                </h4>
                                
                                {{-- Store Name --}}
                                <div class="form-control mb-4">
                                    <label class="label">
                                        <span class="label-text font-medium text-sm sm:text-base">Nama Toko <span class="text-red-500">*</span></span>
                                    </label>
                                    <input type="text"
                                        wire:model="store_name"
                                        class="input input-bordered w-full text-sm sm:text-base @error('store_name') input-error @enderror"
                                        placeholder="Masukkan nama toko">
                                    @error('store_name')
                                    <label class="label">
                                        <span class="label-text-alt text-error text-xs">{{ $message }}</span>
                                    </label>
                                    @enderror
                                </div>

                                {{-- Store Hours --}}
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-medium text-sm sm:text-base">Jam Operasional <span class="text-red-500">*</span></span>
                                    </label>
                                    <input type="text"
                                        wire:model="store_hours"
                                        class="input input-bordered w-full text-sm sm:text-base @error('store_hours') input-error @enderror"
                                        placeholder="Contoh: Senin-Sabtu: 08:00-20:00">
                                    <label class="label">
                                        <span class="label-text-alt text-gray-500 text-xs sm:text-sm">Jam buka toko untuk layanan pickup</span>
                                    </label>
                                    @error('store_hours')
                                    <label class="label">
                                        <span class="label-text-alt text-error text-xs">{{ $message }}</span>
                                    </label>
                                    @enderror
                                </div>
                            </div>

                            {{-- Contact Information --}}
                            <div>
                                <h4 class="font-medium text-gray-900 mb-4 flex items-center text-sm sm:text-base">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-2 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    Kontak
                                </h4>
                                
                                {{-- Store Phone --}}
                                <div class="form-control mb-4">
                                    <label class="label">
                                        <span class="label-text font-medium text-sm sm:text-base">Nomor Telepon <span class="text-red-500">*</span></span>
                                    </label>
                                    <input type="text"
                                        wire:model="store_phone"
                                        class="input input-bordered w-full text-sm sm:text-base @error('store_phone') input-error @enderror"
                                        placeholder="Contoh: +6285171739232">
                                    <label class="label">
                                        <span class="label-text-alt text-gray-500 text-xs sm:text-sm">Nomor telepon toko untuk kontak pelanggan. Gunakan format +62, jangan 08</span>
                                    </label>
                                    @error('store_phone')
                                    <label class="label">
                                        <span class="label-text-alt text-error text-xs">{{ $message }}</span>
                                    </label>
                                    @enderror
                                </div>

                                {{-- Store WhatsApp --}}
                                <div class="form-control mb-4">
                                    <label class="label">
                                        <span class="label-text font-medium text-sm sm:text-base">Nomor WhatsApp</span>
                                    </label>
                                    <input type="text"
                                        wire:model="store_whatsapp"
                                        class="input input-bordered w-full text-sm sm:text-base @error('store_whatsapp') input-error @enderror"
                                        placeholder="Contoh: +6285171739232">
                                    <label class="label">
                                        <span class="label-text-alt text-gray-500 text-xs sm:text-sm">Nomor WhatsApp untuk kontak pelanggan (opsional). Gunakan format +62, jangan 08</span>
                                    </label>
                                    @error('store_whatsapp')
                                    <label class="label">
                                        <span class="label-text-alt text-error text-xs">{{ $message }}</span>
                                    </label>
                                    @enderror
                                </div>

                                {{-- Store Email --}}
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-medium text-sm sm:text-base">Email Toko <span class="text-red-500">*</span></span>
                                    </label>
                                    <input type="email"
                                        wire:model="store_email"
                                        class="input input-bordered w-full text-sm sm:text-base @error('store_email') input-error @enderror"
                                        placeholder="contoh@apotekbaraya.com">
                                    @error('store_email')
                                    <label class="label">
                                        <span class="label-text-alt text-error text-xs">{{ $message }}</span>
                                    </label>
                                    @enderror
                                </div>
                            </div>

                            {{-- Address Information --}}
                            <div>
                                <h4 class="font-medium text-gray-900 mb-4 flex items-center text-sm sm:text-base">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-2 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Alamat Lengkap
                                </h4>
                                
                                {{-- Store Address (Detailed Address) --}}
                                <div class="form-control mb-4">
                                    <label class="label">
                                        <span class="label-text font-medium text-sm sm:text-base">Alamat Toko <span class="text-red-500">*</span></span>
                                    </label>
                                    <textarea wire:model="store_address"
                                        class="textarea textarea-bordered w-full resize-y min-h-[4rem] text-sm sm:text-base @error('store_address') textarea-error @enderror"
                                        placeholder="Contoh: Jl. Raya Sukamaju No. 123, RT 01/RW 02"></textarea>
                                    <label class="label">
                                        <span class="label-text-alt text-gray-500 text-xs sm:text-sm">Alamat lengkap dan spesifik lokasi toko</span>
                                    </label>
                                    @error('store_address')
                                    <label class="label">
                                        <span class="label-text-alt text-error text-xs">{{ $message }}</span>
                                    </label>
                                    @enderror
                                </div>

                                {{-- Address Details Grid --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    {{-- Village/Kelurahan --}}
                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text font-medium text-sm sm:text-base">Desa/Kelurahan <span class="text-red-500">*</span></span>
                                        </label>
                                        <input type="text"
                                            wire:model="store_village"
                                            class="input input-bordered w-full text-sm sm:text-base @error('store_village') input-error @enderror"
                                            placeholder="Nama desa atau kelurahan"
                                            required>
                                        @error('store_village')
                                        <label class="label">
                                            <span class="label-text-alt text-error text-xs">{{ $message }}</span>
                                        </label>
                                        @enderror
                                    </div>

                                    {{-- Sub District/Kecamatan --}}
                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text font-medium text-sm sm:text-base">Kecamatan <span class="text-red-500">*</span></span>
                                        </label>
                                        <input type="text"
                                            wire:model="store_district"
                                            class="input input-bordered w-full text-sm sm:text-base @error('store_district') input-error @enderror"
                                            placeholder="Nama kecamatan"
                                            required>
                                        @error('store_district')
                                        <label class="label">
                                            <span class="label-text-alt text-error text-xs">{{ $message }}</span>
                                        </label>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    {{-- Regency/Kabupaten --}}
                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text font-medium text-sm sm:text-base">Kabupaten/Kota <span class="text-red-500">*</span></span>
                                        </label>
                                        <input type="text"
                                            wire:model="store_regency"
                                            class="input input-bordered w-full text-sm sm:text-base @error('store_regency') input-error @enderror"
                                            placeholder="Nama kabupaten atau kota"
                                            required>
                                        @error('store_regency')
                                        <label class="label">
                                            <span class="label-text-alt text-error text-xs">{{ $message }}</span>
                                        </label>
                                        @enderror
                                    </div>

                                    {{-- Province --}}
                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text font-medium text-sm sm:text-base">Provinsi <span class="text-red-500">*</span></span>
                                        </label>
                                        <input type="text"
                                            wire:model="store_province"
                                            class="input input-bordered w-full text-sm sm:text-base @error('store_province') input-error @enderror"
                                            placeholder="Nama provinsi"
                                            required>
                                        @error('store_province')
                                        <label class="label">
                                            <span class="label-text-alt text-error text-xs">{{ $message }}</span>
                                        </label>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Postal Code --}}
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text font-medium text-sm sm:text-base">Kode Pos <span class="text-red-500">*</span></span>
                                    </label>
                                    <input type="text"
                                        wire:model="store_postal_code"
                                        class="input input-bordered w-full text-sm sm:text-base @error('store_postal_code') input-error @enderror"
                                        placeholder="5 digit kode pos">
                                    @error('store_postal_code')
                                    <label class="label">
                                        <span class="label-text-alt text-error text-xs">{{ $message }}</span>
                                    </label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Shipping Settings Tab --}}
                <div id="content-shipping" class="tab-content hidden">
                    <div class="mb-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Konfigurasi Pengiriman</h3>
                        <p class="text-gray-600 text-sm">Atur tarif dan kebijakan pengiriman untuk layanan antar obat</p>
                    </div>

                    <div class="max-w-2xl">
                        <div class="bg-base-50 p-4 sm:p-6 rounded-lg border border-base-200 space-y-6">
                            {{-- Shipping Rate --}}
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium flex items-center text-sm sm:text-base">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-2 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                        </svg>
                                        Tarif Pengiriman per KM <span class="text-red-500">*</span>
                                    </span>
                                </label>
                                <div class="join w-full">
                                    <span class="join-item btn btn-sm sm:btn-md btn-outline no-animation">Rp</span>
                                    <input type="number"
                                        min="0"
                                        step="500"
                                        wire:model="shipping_rate_per_km"
                                        class="join-item input input-bordered flex-1 text-sm sm:text-base @error('shipping_rate_per_km') input-error @enderror"
                                        placeholder="2000">
                                    <span class="join-item btn btn-sm sm:btn-md btn-outline no-animation">/km</span>
                                </div>
                                <label class="label">
                                    <span class="label-text-alt text-gray-500 text-xs sm:text-sm">Biaya pengiriman yang dikenakan per kilometer jarak</span>
                                </label>
                                @error('shipping_rate_per_km')
                                <label class="label">
                                    <span class="label-text-alt text-error text-xs">{{ $message }}</span>
                                </label>
                                @enderror
                            </div>

                            {{-- Max Delivery Distance --}}
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium flex items-center text-sm sm:text-base">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-2 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                        </svg>
                                        Jarak Pengiriman Maksimal <span class="text-red-500">*</span>
                                    </span>
                                </label>
                                <div class="join w-full">
                                    <input type="number"
                                        min="1"
                                        max="50"
                                        wire:model="max_delivery_distance"
                                        class="join-item input input-bordered flex-1 text-sm sm:text-base @error('max_delivery_distance') input-error @enderror"
                                        placeholder="15">
                                    <span class="join-item btn btn-sm sm:btn-md btn-outline no-animation">KM</span>
                                </div>
                                <label class="label">
                                    <span class="label-text-alt text-gray-500 text-xs sm:text-sm">Batas maksimal jarak pengiriman yang dapat dilayani</span>
                                </label>
                                @error('max_delivery_distance')
                                <label class="label">
                                    <span class="label-text-alt text-error text-xs">{{ $message }}</span>
                                </label>
                                @enderror
                            </div>

                            {{-- Free Shipping Minimum --}}
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-medium flex items-center text-sm sm:text-base">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-2 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                                        </svg>
                                        Minimum Gratis Ongkir <span class="text-red-500">*</span>
                                    </span>
                                </label>
                                <div class="join w-full">
                                    <span class="join-item btn btn-sm sm:btn-md btn-outline no-animation">Rp</span>
                                    <input type="number"
                                        min="0"
                                        step="10000"
                                        wire:model="free_shipping_minimum"
                                        class="join-item input input-bordered flex-1 text-sm sm:text-base @error('free_shipping_minimum') input-error @enderror"
                                        placeholder="100000">
                                </div>
                                <label class="label">
                                    <span class="label-text-alt text-gray-500 text-xs sm:text-sm">Minimal pembelian untuk mendapat gratis ongkos kirim</span>
                                </label>
                                @error('free_shipping_minimum')
                                <label class="label">
                                    <span class="label-text-alt text-error text-xs">{{ $message }}</span>
                                </label>
                                @enderror
                            </div>

                            {{-- Info Box --}}
                            <div class="alert alert-info">
                                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <h4 class="font-medium text-sm sm:text-base">Informasi Pengiriman</h4>
                                    <p class="text-xs sm:text-sm mt-1">Pengaturan ini akan mempengaruhi perhitungan ongkos kirim otomatis berdasarkan jarak dari toko ke alamat pelanggan.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Save Button (Fixed at bottom) --}}
                <div class="mt-8 pt-6 border-t border-base-200">
                    <div class="flex justify-start">
                        <button type="button"
                            wire:click="updateSettings"
                            class="btn btn-primary px-4 sm:px-6 text-sm sm:text-base">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            Simpan Pengaturan
                        </button>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

<script>
    // Tab functionality
    function showTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
            content.style.display = 'none';
        });
        
        // Remove active class from all tabs
        document.querySelectorAll('.tab').forEach(tab => {
            tab.classList.remove('tab-active');
        });
        
        // Show selected tab content
        const targetContent = document.getElementById('content-' + tabName);
        if (targetContent) {
            targetContent.classList.remove('hidden');
            targetContent.style.display = 'block';
        }
        
        // Add active class to selected tab
        const targetTab = document.getElementById('tab-' + tabName);
        if (targetTab) {
            targetTab.classList.add('tab-active');
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Ensure store-info tab is visible by default
        showTab('store-info');
        
        // Add click event listeners to tabs
        document.getElementById('tab-store-info')?.addEventListener('click', function(e) {
            e.preventDefault();
            showTab('store-info');
        });
        
        document.getElementById('tab-shipping')?.addEventListener('click', function(e) {
            e.preventDefault();
            showTab('shipping');
        });
    });

    // Also initialize when Livewire loads
    document.addEventListener('livewire:navigated', function() {
        showTab('store-info');
    });

    // Initialize when Livewire component loads
    document.addEventListener('livewire:init', function() {
        showTab('store-info');
    });

    // Ensure default tab is shown after Livewire updates (like after form submission)
    document.addEventListener('livewire:updated', function() {
        setTimeout(function() {
            showTab('store-info');
        }, 100);
    });

    // Handle settings saved event with notification and page refresh
    document.addEventListener('livewire:init', function() {
        Livewire.on('settings-saved', function(data) {
            // Show toast notification
            showToastNotification(data[0].message, data[0].type);
            
            // Refresh page after short delay to show notification
            setTimeout(function() {
                window.location.reload();
            }, 1500);
        });
    });

    // Toast notification function
    function showToastNotification(message, type = 'success') {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} fixed top-4 right-4 z-50 max-w-sm shadow-lg transform transition-all duration-300 translate-x-full opacity-0`;
        
        toast.innerHTML = `
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="ml-2">${message}</span>
            </div>
        `;
        
        // Add to body
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');
        }, 100);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }
</script>