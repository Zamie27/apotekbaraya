<div>
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Pengaturan Toko</h1>
        <p class="text-gray-600 mt-2">Kelola informasi toko dan pengaturan pengiriman</p>
    </div>

    {{-- Success/Error Messages --}}
    @if($successMessage)
    <div class="alert alert-success mb-6" wire:click="clearMessages">
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ $successMessage }}</span>
    </div>
    @endif

    @if($errorMessage)
    <div class="alert alert-error mb-6" wire:click="clearMessages">
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ $errorMessage }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Store Information Section --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-xl mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Informasi Toko
                </h2>

                {{-- Info Alert --}}
                <div class="alert alert-info mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <div>
                        <h3 class="font-bold">Geocoding Otomatis</h3>
                        <div class="text-sm">Koordinat toko akan otomatis didapatkan berdasarkan alamat detail yang Anda masukkan. Pastikan alamat lengkap dan akurat untuk hasil terbaik.</div>
                    </div>
                </div>

                {{-- Store Name --}}
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text font-medium">Nama Toko</span>
                    </label>
                    <input type="text"
                        wire:model.live="store_name"
                        class="input input-bordered w-full @error('store_name') input-error @enderror"
                        placeholder="Masukkan nama toko">
                    @error('store_name')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                    @enderror
                </div>

                {{-- Store Address (Detailed Address) --}}
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text font-medium">Alamat Toko</span>
                    </label>
                    <textarea wire:model.live="store_address"
                        class="textarea textarea-bordered w-full resize-y min-h-[3rem] @error('store_address') textarea-error @enderror"
                        placeholder="Contoh: Jl. Raya Sukamaju No. 123, RT 01/RW 02"></textarea>
                    <label class="label">
                        <span class="label-text-alt text-gray-500">Alamat lengkap dan spesifik lokasi toko</span>
                    </label>
                    @error('store_address')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                    @enderror
                </div>

                {{-- Address Details Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    {{-- Village/Kelurahan --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Desa/Kelurahan <span class="text-red-500">*</span></span>
                        </label>
                        <input type="text"
                            wire:model.live="store_village"
                            class="input input-bordered w-full @error('store_village') input-error @enderror"
                            placeholder="Nama desa atau kelurahan"
                            required>
                        @error('store_village')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                        @enderror
                    </div>

                    {{-- Sub District/Kecamatan --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Kecamatan <span class="text-red-500">*</span></span>
                        </label>
                        <input type="text"
                            wire:model.live="store_district"
                            class="input input-bordered w-full @error('store_district') input-error @enderror"
                            placeholder="Nama kecamatan"
                            required>
                        @error('store_district')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    {{-- Regency/Kabupaten --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Kabupaten/Kota <span class="text-red-500">*</span></span>
                        </label>
                        <input type="text"
                            wire:model.live="store_regency"
                            class="input input-bordered w-full @error('store_regency') input-error @enderror"
                            placeholder="Nama kabupaten atau kota"
                            required>
                        @error('store_regency')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                        @enderror
                    </div>

                    {{-- Province --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Provinsi <span class="text-red-500">*</span></span>
                        </label>
                        <input type="text"
                            wire:model.live="store_province"
                            class="input input-bordered w-full @error('store_province') input-error @enderror"
                            placeholder="Nama provinsi"
                            required>
                        @error('store_province')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                        @enderror
                    </div>
                </div>

                {{-- Postal Code --}}
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text font-medium">Kode Pos</span>
                    </label>
                    <input type="text"
                        wire:model.live="store_postal_code"
                        class="input input-bordered w-full @error('store_postal_code') input-error @enderror"
                        placeholder="5 digit kode pos">
                    @error('store_postal_code')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                    @enderror
                </div>

                {{-- Store Phone --}}
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text font-medium">Nomor Telepon</span>
                    </label>
                    <input type="text"
                        wire:model.live="store_phone"
                        class="input input-bordered w-full @error('store_phone') input-error @enderror"
                        placeholder="Contoh: 0812-3456-7890">
                    @error('store_phone')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                    @enderror
                </div>

                {{-- Store Email --}}
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text font-medium">Email Toko</span>
                    </label>
                    <input type="email"
                        wire:model.live="store_email"
                        class="input input-bordered w-full @error('store_email') input-error @enderror"
                        placeholder="contoh@apotekbaraya.com">
                    @error('store_email')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                    @enderror
                </div>

                {{-- Coordinates Section --}}
                <div class="divider">Koordinat Toko</div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    {{-- Latitude --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Latitude</span>
                        </label>
                        <input type="number"
                            step="any"
                            wire:model.live="store_latitude"
                            class="input input-bordered w-full @error('store_latitude') input-error @enderror"
                            placeholder="-6.200000">
                        @error('store_latitude')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                        @enderror
                    </div>

                    {{-- Longitude --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium">Longitude</span>
                        </label>
                        <input type="number"
                            step="any"
                            wire:model.live="store_longitude"
                            class="input input-bordered w-full @error('store_longitude') input-error @enderror"
                            placeholder="106.816666">
                        @error('store_longitude')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                        @enderror
                    </div>
                </div>

                {{-- Get Coordinates Button --}}
                <button type="button"
                    wire:click="getCoordinatesFromAddress"
                    class="btn btn-outline btn-info w-full mb-4"
                    wire:loading.attr="disabled"
                    wire:target="getCoordinatesFromAddress">
                    <span wire:loading.remove wire:target="getCoordinatesFromAddress">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Dapatkan Koordinat dari Alamat
                    </span>
                    <span wire:loading wire:target="getCoordinatesFromAddress">
                        <span class="loading loading-spinner loading-sm"></span>
                        Mengambil koordinat...
                    </span>
                </button>
            </div>
        </div>

        {{-- Shipping Settings Section --}}
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-xl mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM21 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                    Pengaturan Pengiriman
                </h2>

                {{-- Shipping Rate --}}
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text font-medium">Tarif Pengiriman per KM (Rp)</span>
                    </label>
                    <input type="number"
                        min="0"
                        wire:model.live="shipping_rate_per_km"
                        class="input input-bordered w-full @error('shipping_rate_per_km') input-error @enderror"
                        placeholder="2000">
                    @error('shipping_rate_per_km')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                    @enderror
                </div>

                {{-- Max Delivery Distance --}}
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text font-medium">Jarak Pengiriman Maksimal (KM)</span>
                    </label>
                    <input type="number"
                        min="1"
                        wire:model.live="max_delivery_distance"
                        class="input input-bordered w-full @error('max_delivery_distance') input-error @enderror"
                        placeholder="15">
                    @error('max_delivery_distance')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                    @enderror
                </div>

                {{-- Free Shipping Minimum --}}
                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text font-medium">Minimum Gratis Ongkir (Rp)</span>
                    </label>
                    <input type="number"
                        min="0"
                        wire:model.live="free_shipping_minimum"
                        class="input input-bordered w-full @error('free_shipping_minimum') input-error @enderror"
                        placeholder="100000">
                    @error('free_shipping_minimum')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                    @enderror
                </div>

                {{-- Save Settings Button --}}
                <button type="button"
                    wire:click="updateSettings"
                    class="btn btn-primary w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Simpan Pengaturan
                </button>
            </div>
        </div>
    </div>

    {{-- Distance Calculator Test Section --}}
    <div class="card bg-base-100 shadow-xl mt-8">
        <div class="card-body">
            <h2 class="card-title text-xl mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3l6-3" />
                </svg>
                Test Perhitungan Jarak
            </h2>
            <p class="text-gray-600 mb-4">Uji coba perhitungan jarak dan biaya pengiriman dari alamat tertentu ke toko</p>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Test Input --}}
                <div>
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-medium">Alamat Test</span>
                        </label>
                        <textarea wire:model.live="testAddress"
                            class="textarea textarea-bordered w-full resize-y min-h-[3rem]"
                            placeholder="Masukkan alamat untuk test perhitungan jarak"></textarea>
                    </div>

                    <div class="flex gap-2">
                        <button type="button"
                            wire:click="testDistanceCalculation"
                            class="btn btn-secondary flex-1"
                            wire:loading.attr="disabled"
                            wire:target="testDistanceCalculation">
                            <span wire:loading.remove wire:target="testDistanceCalculation">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16l2.879-2.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Hitung Jarak
                            </span>
                            <span wire:loading wire:target="testDistanceCalculation">
                                <span class="loading loading-spinner loading-sm"></span>
                                Menghitung...
                            </span>
                        </button>

                        @if($testResult)
                        <button type="button"
                            wire:click="clearTestResult"
                            class="btn btn-outline">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                        @endif
                    </div>
                </div>

                {{-- Test Results --}}
                <div>
                    @if($testResult)
                    <div class="bg-base-200 rounded-lg p-4">
                        <h3 class="font-semibold text-lg mb-3">Hasil Perhitungan</h3>

                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Alamat:</span>
                                <span class="font-medium text-right max-w-xs truncate">{{ $testResult['address'] }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Jarak:</span>
                                <span class="font-medium">{{ number_format($testResult['distance_km'], 2) }} km</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Estimasi Waktu:</span>
                                <span class="font-medium">{{ $testResult['duration_text'] }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Biaya Pengiriman:</span>
                                <span class="font-medium {{ $testResult['is_free_shipping'] ? 'text-green-600' : 'text-blue-600' }}">
                                    @if($testResult['is_free_shipping'])
                                    GRATIS
                                    @else
                                    Rp {{ number_format($testResult['shipping_cost'], 0, ',', '.') }}
                                    @endif
                                </span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Status Pengiriman:</span>
                                <span class="badge {{ $testResult['delivery_available'] ? 'badge-success' : 'badge-error' }}">
                                    {{ $testResult['delivery_available'] ? 'Tersedia' : 'Tidak Tersedia' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="bg-base-200 rounded-lg p-4 text-center text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3l6-3" />
                        </svg>
                        <p>Masukkan alamat dan klik "Hitung Jarak" untuk melihat hasil</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Listen for refresh-page event from Livewire
    document.addEventListener('livewire:init', () => {
        Livewire.on('refresh-page', () => {
            // Add a small delay to ensure the success message is visible before refresh
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        });
    });
</script>