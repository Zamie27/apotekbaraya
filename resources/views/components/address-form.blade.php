@props([
    'addressForm' => [],
    'provinces' => [],
    'regencies' => [],
    'subDistricts' => [],
    'villages' => [],
    'postalCodes' => [],
    'addressPreview' => '',
    'editingAddressId' => null,
    'wireModel' => 'addressForm',
    'showTitle' => true,
    'title' => 'Alamat Pengiriman'
])

<div class="space-y-3 sm:space-y-4">
    @if($showTitle)
        <h3 class="text-base sm:text-lg font-semibold text-gray-900">{{ $title }}</h3>
    @endif

    <!-- Label Alamat -->
    <div>
        <label for="{{ $wireModel }}.label" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">
            Label Alamat <span class="text-red-500">*</span>
        </label>
        <select wire:model="{{ $wireModel }}.label" 
                id="{{ $wireModel }}.label"
                class="w-full px-2 sm:px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">Pilih Label</option>
            <option value="rumah">Rumah</option>
            <option value="kantor">Kantor</option>
            <option value="kost">Kost</option>
            <option value="lainnya">Lainnya</option>
        </select>
        @error($wireModel . '.label')
            <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Nama Penerima -->
    <div>
        <label for="{{ $wireModel }}.recipient_name" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">
            Nama Penerima <span class="text-red-500">*</span>
        </label>
        <input type="text" 
               wire:model="{{ $wireModel }}.recipient_name" 
               id="{{ $wireModel }}.recipient_name"
               class="w-full px-2 sm:px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
               placeholder="Masukkan nama penerima">
        @error($wireModel . '.recipient_name')
            <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Nomor Telepon -->
    <div>
        <label for="{{ $wireModel }}.phone" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">
            Nomor Telepon <span class="text-red-500">*</span>
        </label>
        <input type="tel" 
               wire:model="{{ $wireModel }}.phone" 
               id="{{ $wireModel }}.phone"
               class="w-full px-2 sm:px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
               placeholder="Contoh: 08123456789">
        @error($wireModel . '.phone')
            <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Cascading Dropdown Section -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
        <!-- Provinsi -->
        <div>
            <label for="{{ $wireModel }}.province_key" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">
                Provinsi <span class="text-red-500">*</span>
            </label>
            <select wire:model="{{ $wireModel }}.province_key" 
                    wire:change="updateRegencies"
                    id="{{ $wireModel }}.province_key"
                    class="w-full px-2 sm:px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Pilih Provinsi</option>
                @if(isset($provinces))
                    @foreach($provinces as $province)
                        <option value="{{ $province['key'] }}">{{ $province['name'] }}</option>
                    @endforeach
                @endif
            </select>
            @error($wireModel . '.province_key')
                <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Kabupaten -->
        <div>
            <label for="{{ $wireModel }}.regency_key" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">
                Kabupaten <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <select wire:model="{{ $wireModel }}.regency_key" 
                        wire:change="updateSubDistricts"
                        id="{{ $wireModel }}.regency_key"
                        class="w-full px-2 sm:px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        {{ empty($regencies) ? 'disabled' : '' }}>
                    <option value="">{{ empty($regencies) ? 'Pilih provinsi terlebih dahulu...' : 'Pilih Kabupaten' }}</option>
                    @if(isset($regencies))
                        @foreach($regencies as $regency)
                            <option value="{{ $regency['key'] }}">{{ $regency['name'] }}</option>
                        @endforeach
                    @endif
                </select>
                <div wire:loading wire:target="updateRegencies" class="absolute right-2 sm:right-3 top-1/2 transform -translate-y-1/2">
                    <svg class="animate-spin h-3 w-3 sm:h-4 sm:w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
            @error($wireModel . '.regency_key')
                <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Kecamatan -->
        <div>
            <label for="{{ $wireModel }}.sub_district_key" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">
                Kecamatan <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <select wire:model="{{ $wireModel }}.sub_district_key" 
                        wire:change="updateVillages"
                        id="{{ $wireModel }}.sub_district_key"
                        class="w-full px-2 sm:px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        {{ empty($subDistricts) ? 'disabled' : '' }}>
                    <option value="">{{ empty($subDistricts) ? 'Pilih kabupaten terlebih dahulu...' : 'Pilih Kecamatan' }}</option>
                    @if(isset($subDistricts))
                        @foreach($subDistricts as $subDistrict)
                            <option value="{{ $subDistrict['key'] }}">{{ $subDistrict['name'] }}</option>
                        @endforeach
                    @endif
                </select>
                <div wire:loading wire:target="updateSubDistricts" class="absolute right-2 sm:right-3 top-1/2 transform -translate-y-1/2">
                    <svg class="animate-spin h-3 w-3 sm:h-4 sm:w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
            @error($wireModel . '.sub_district_key')
                <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Desa -->
        <div>
            <label for="{{ $wireModel }}.village_key" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">
                Desa <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <select wire:model="{{ $wireModel }}.village_key" 
                        wire:change="updatePostalCodes"
                        id="{{ $wireModel }}.village_key"
                        class="w-full px-2 sm:px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        {{ empty($villages) ? 'disabled' : '' }}>
                    <option value="">{{ empty($villages) ? 'Pilih kecamatan terlebih dahulu...' : 'Pilih Desa' }}</option>
                    @if(isset($villages))
                        @foreach($villages as $village)
                            <option value="{{ $village['key'] }}">{{ $village['name'] }}</option>
                        @endforeach
                    @endif
                </select>
                <div wire:loading wire:target="updateVillages" class="absolute right-2 sm:right-3 top-1/2 transform -translate-y-1/2">
                    <svg class="animate-spin h-3 w-3 sm:h-4 sm:w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
            @error($wireModel . '.village_key')
                <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Kode Pos -->
    <div>
        <label for="{{ $wireModel }}.postal_code" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">
            Kode Pos <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <select wire:model="{{ $wireModel }}.postal_code" 
                    id="{{ $wireModel }}.postal_code"
                    class="w-full px-2 sm:px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    {{ empty($postalCodes) ? 'disabled' : '' }}>
                <option value="">{{ empty($postalCodes) ? 'Pilih desa terlebih dahulu...' : 'Pilih Kode Pos' }}</option>
                @if(isset($postalCodes))
                    @foreach($postalCodes as $postalCode)
                        <option value="{{ $postalCode['key'] }}">{{ $postalCode['name'] }}</option>
                    @endforeach
                @endif
            </select>
            <div wire:loading wire:target="updatePostalCodes" class="absolute right-2 sm:right-3 top-1/2 transform -translate-y-1/2">
                <svg class="animate-spin h-3 w-3 sm:h-4 sm:w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
        @error($wireModel . '.postal_code')
            <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Detail Alamat -->
    <div>
        <label for="{{ $wireModel }}.detailed_address" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">
            Detail Alamat <span class="text-red-500">*</span>
        </label>
        <textarea wire:model="{{ $wireModel }}.detailed_address" 
                  id="{{ $wireModel }}.detailed_address"
                  rows="3"
                  class="w-full px-2 sm:px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Contoh: Jl. Merdeka No. 123, RT 01/RW 02, Perumahan ABC"></textarea>
        <p class="mt-1 text-xs text-gray-500">
            Masukkan detail alamat seperti nama jalan, nomor rumah, RT/RW, patokan, dll.
        </p>
        @error($wireModel . '.detailed_address')
            <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Catatan -->
    <div>
        <label for="{{ $wireModel }}.notes" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">
            Catatan (Opsional)
        </label>
        <textarea wire:model="{{ $wireModel }}.notes" 
                  id="{{ $wireModel }}.notes"
                  rows="2"
                  class="w-full px-2 sm:px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Catatan tambahan untuk kurir (opsional)"></textarea>
        @error($wireModel . '.notes')
            <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Set as Default -->
    <div class="flex items-center">
        <input type="checkbox" 
               wire:model="{{ $wireModel }}.is_default" 
               id="{{ $wireModel }}.is_default"
               class="h-3 w-3 sm:h-4 sm:w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
        <label for="{{ $wireModel }}.is_default" class="ml-2 block text-xs sm:text-sm text-gray-700">
            Jadikan sebagai alamat utama
        </label>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 pt-4">
        <button type="button" 
                wire:click="{{ $attributes->get('cancelAction', 'hideAddressForm') }}"
                class="w-full sm:w-auto px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Batal
        </button>
        <button type="button" 
                wire:click="{{ $attributes->get('submitAction', 'saveAddress') }}"
                class="w-full sm:w-auto px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:bg-gray-400 disabled:cursor-not-allowed"
                {{ empty($postalCodes) || empty($addressForm['postal_code'] ?? '') ? 'disabled' : '' }}>
            <span wire:loading.remove wire:target="{{ $attributes->get('submitAction', 'saveAddress') }}">
                {{ $editingAddressId ? 'Update Alamat' : 'Simpan Alamat' }}
            </span>
            <span wire:loading wire:target="{{ $attributes->get('submitAction', 'saveAddress') }}">
                <svg class="animate-spin -ml-1 mr-2 h-3 w-3 sm:h-4 sm:w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Menyimpan...
            </span>
        </button>
    </div>
    
    <!-- Warning message when postal code is not selected -->
    @if(empty($postalCodes) || empty($addressForm['postal_code'] ?? ''))
        <div class="mt-2 p-2 sm:p-3 bg-yellow-50 border border-yellow-200 rounded-md">
            <div class="flex items-center">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-yellow-400 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-xs sm:text-sm text-yellow-700">
                    <strong>Perhatian:</strong> Silakan pilih kode pos terlebih dahulu sebelum menyimpan alamat.
                </p>
            </div>
        </div>
    @endif
</div>