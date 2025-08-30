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

                {{-- Store Hours --}}
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text font-medium">Jam Operasional <span class="text-red-500">*</span></span>
                    </label>
                    <input type="text"
                        wire:model.live="store_hours"
                        class="input input-bordered w-full @error('store_hours') input-error @enderror"
                        placeholder="Contoh: Senin-Sabtu: 08:00-20:00">
                    <label class="label">
                        <span class="label-text-alt text-gray-500">Jam buka toko untuk layanan pickup</span>
                    </label>
                    @error('store_hours')
                    <label class="label">
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    </label>
                    @enderror
                </div>
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