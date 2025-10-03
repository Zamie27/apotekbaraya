<div class="max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Upload Resep Dokter</h2>
        
        <!-- General Error Message -->
        @error('general')
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-800">{{ $message }}</p>
                    </div>
                </div>
            </div>
        @enderror
        
        <form wire:submit.prevent="submit" enctype="multipart/form-data" class="space-y-6">
            <!-- Doctor Name -->
            <div>
                <label for="doctor_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Dokter <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="doctor_name"
                    wire:model="doctor_name"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Masukkan nama dokter"
                >
                @error('doctor_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Patient Name -->
            <div>
                <label for="patient_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Pasien <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="patient_name"
                    wire:model="patient_name"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Masukkan nama pasien"
                >
                @error('patient_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Prescription Image -->
            <div>
                <label for="prescription_image" class="block text-sm font-medium text-gray-700 mb-2">
                    Foto Resep <span class="text-red-500">*</span>
                </label>
                
                @if (session()->has('file_uploaded'))
                    <div class="mb-3 p-3 bg-green-100 border border-green-400 text-green-700 rounded-md">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('file_uploaded') }}
                    </div>
                @endif
                
                <!-- Hidden file input -->
                <input 
                    id="prescription_image" 
                    type="file" 
                    wire:model="prescription_image"
                    class="sr-only"
                    accept="image/*"
                >
                
                <!-- Loading indicator -->
                <div wire:loading wire:target="prescription_image" class="mb-3 p-3 bg-blue-100 border border-blue-400 text-blue-700 rounded-md">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Sedang mengunggah foto resep...
                </div>
                
                <!-- Clickable upload area -->
                <label for="prescription_image" class="block cursor-pointer">
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-blue-400 hover:bg-gray-50 transition-all duration-200 group">
                        <div class="space-y-1 text-center">
                            @if ($prescription_image && is_object($prescription_image) && method_exists($prescription_image, 'temporaryUrl'))
                                <div class="mb-4">
                                    @php
                                        $hasTemporaryUrl = false;
                                        $fileName = '';
                                        try {
                                            if (method_exists($prescription_image, 'temporaryUrl')) {
                                                $tempUrl = $prescription_image->temporaryUrl();
                                                $hasTemporaryUrl = true;
                                            }
                                            if (method_exists($prescription_image, 'getClientOriginalName')) {
                                                $fileName = $prescription_image->getClientOriginalName();
                                            }
                                        } catch (\Exception $e) {
                                            $hasTemporaryUrl = false;
                                        }
                                    @endphp
                                    
                                    @if ($hasTemporaryUrl)
                                        <img src="{{ $prescription_image->temporaryUrl() }}" class="mx-auto h-32 w-auto rounded-lg shadow-md">
                                        @if ($fileName)
                                            <p class="mt-2 text-sm text-gray-600 font-medium">{{ $fileName }}</p>
                                        @endif
                                        <p class="text-xs text-green-600 mt-1">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Foto berhasil dipilih - Klik untuk mengganti
                                        </p>
                                    @else
                                        <div class="text-center">
                                            <i class="fas fa-image text-gray-400 text-4xl mb-2"></i>
                                            <p class="text-sm text-gray-600 font-medium">Foto sedang diproses...</p>
                                            <p class="text-xs text-blue-600 mt-1">
                                                <i class="fas fa-spinner fa-spin mr-1"></i>
                                                Klik untuk mengganti foto
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <svg class="mx-auto h-12 w-12 text-gray-400 group-hover:text-blue-500 transition-colors duration-200" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                
                                <div class="text-sm text-gray-600 group-hover:text-blue-600 transition-colors duration-200">
                                    <p class="font-medium text-blue-600 group-hover:text-blue-700">
                                        <i class="fas fa-cloud-upload-alt mr-2"></i>
                                        Klik untuk upload foto resep
                                    </p>
                                    <p class="mt-1">atau drag and drop file di sini</p>
                                </div>
                                
                                <p class="text-xs text-gray-500 mt-2">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Format: PNG, JPG, JPEG • Maksimal: 2MB
                                </p>
                            @endif
                        </div>
                    </div>
                </label>
                
                @error('prescription_image')
                    <p class="mt-1 text-sm text-red-600">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Delivery Method Selection -->
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body p-4 sm:p-6">
                    <h2 class="card-title text-lg sm:text-xl mb-3 sm:mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                        <span class="text-base sm:text-xl">Pilih Metode Pengambilan Obat</span>
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <!-- Store Pickup -->
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="delivery_method" value="pickup" name="delivery_method" class="radio radio-success" />
                            <div class="card bg-base-200 border-2 {{ $delivery_method === 'pickup' ? 'border-success' : 'border-transparent' }} ml-3">
                                <div class="card-body p-3 sm:p-4">
                                    <h3 class="font-semibold text-sm sm:text-base">Ambil di Toko</h3>
                                    <p class="text-xs sm:text-sm text-gray-600">Gratis - Ambil langsung di apotek</p>
                                    <div class="text-xs text-gray-500 mt-2">
                                        @php
                                        // Build complete store address
                                        $storeAddressParts = array_filter([
                                        \App\Models\StoreSetting::get('store_address', ''),
                                        \App\Models\StoreSetting::get('store_village', ''),
                                        \App\Models\StoreSetting::get('store_district', ''),
                                        \App\Models\StoreSetting::get('store_regency', ''),
                                        \App\Models\StoreSetting::get('store_province', ''),
                                        \App\Models\StoreSetting::get('store_postal_code', '')
                                        ], function($part) {
                                        return !empty(trim($part));
                                        });
                                        $fullStoreAddress = !empty($storeAddressParts) ? implode(', ', $storeAddressParts) : 'Apotek Baraya';
                                        @endphp
                                        <p>📍 {{ $fullStoreAddress }}</p>
                                        <p>⏰ {{ \App\Models\StoreSetting::get('store_hours', 'Senin-Sabtu: 08:00-20:00') }}</p>
                                        <p class="text-xs text-gray-500 mt-1">Obat dapat diambil setelah mendapat konfirmasi dari apoteker</p>
                                    </div>
                                </div>
                            </div>
                        </label>

                        <!-- Home Delivery -->
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="delivery_method" value="delivery" name="delivery_method" class="radio radio-success" />
                            <div class="card bg-base-200 border-2 {{ $delivery_method === 'delivery' ? 'border-success' : 'border-transparent' }} ml-3">
                                <div class="card-body p-3 sm:p-4">
                                    <h3 class="font-semibold text-sm sm:text-base">Kirim ke Alamat</h3>
                                    <p class="text-xs sm:text-sm text-gray-600">Rp {{ number_format(\App\Models\StoreSetting::get('shipping_rate_per_km', 2000), 0, ',', '.') }}/km - Maksimal {{ \App\Models\StoreSetting::get('max_delivery_distance', 15) }}km</p>
                                    <div class="text-xs text-gray-500 mt-2">
                                        <p>🚚 Estimasi {{ \App\Models\StoreSetting::get('delivery_estimation', '1-2 jam') }} setelah obat siap</p>
                                        @php
                                        $freeShippingMin = \App\Models\StoreSetting::get('free_shipping_minimum', 100000);
                                        @endphp
                                        @if($freeShippingMin > 0)
                                        <p>💰 Gratis ongkir min. Rp {{ number_format($freeShippingMin, 0, ',', '.') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>

                    @error('delivery_method')
                        <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Address Selection (only for delivery) -->
            @if($delivery_method === 'delivery')
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body p-4 sm:p-6">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 sm:gap-0 mb-4">
                            <h2 class="card-title text-lg sm:text-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="text-base sm:text-xl">Pilih Alamat Pengiriman</span>
                            </h2>
                            <button type="button" wire:click="toggleAddressForm" class="btn btn-success btn-sm text-xs sm:text-sm">
                                @if($show_address_form)
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Batal
                                @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Alamat
                                @endif
                            </button>
                        </div>

                        <!-- Add New Address Form -->
                        @if($show_address_form)
                        <div class="bg-base-200 p-4 sm:p-6 rounded-lg mb-4 sm:mb-6">
                            <div class="flex justify-between items-center mb-3 sm:mb-4">
                                <h3 class="text-base sm:text-lg font-semibold">Tambah Alamat Baru</h3>
                                <button wire:click="toggleAddressForm" class="btn btn-ghost btn-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Use Address Form Component -->
                            <x-address-form
                                :addressForm="$address_form"
                                :provinces="$provinces"
                                :regencies="$regencies"
                                :subDistricts="$subDistricts"
                                :villages="$villages"
                                :postalCodes="$postalCodes"
                                :addressPreview="$address_preview"
                                :editingAddressId="null"
                                wireModel="address_form"
                                cancelAction="toggleAddressForm"
                                submitAction="saveNewAddress" />
                        </div>
                        @endif

                    <!-- Address List -->
                    @if(count($addresses) > 0)
                        <div class="space-y-4 sm:space-y-6">
                            @foreach($addresses as $address)
                                <label class="cursor-pointer block mb-3 sm:mb-4">
                                    <input type="radio" wire:model.live="selected_address_id" value="{{ $address->address_id }}" name="delivery_address" class="radio radio-success" />
                                    <div class="card bg-base-200 border-2 {{ $selected_address_id == $address->address_id ? 'border-success' : 'border-transparent' }} ml-3">
                                        <div class="card-body p-3 sm:p-4">
                                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 sm:gap-0">
                                                <div class="flex-1">
                                                    <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 mb-1">
                                                        <span class="badge badge-outline text-xs">{{ ucfirst($address->label) }}</span>
                                                        @if($address->is_default)
                                                            <span class="badge badge-success badge-sm text-xs">Default</span>
                                                        @endif
                                                    </div>
                                                    <h4 class="font-semibold text-sm sm:text-base">{{ $address->recipient_name }}</h4>
                                                    <p class="text-xs sm:text-sm text-gray-600">{{ $address->recipient_phone }}</p>

                                                    @if($address->detailed_address)
                                                        <p class="text-xs sm:text-sm mt-1">{{ $address->detailed_address }}</p>
                                                    @else
                                                        <p class="text-xs sm:text-sm mt-1">{{ $address->address }}</p>
                                                    @endif

                                                    <div class="text-xs sm:text-sm text-gray-600 mt-1">
                                                        @if($address->village_name || $address->district_name || $address->regency_name || $address->province_name)
                                                            <p>
                                                                @if($address->village_name) {{ $address->village_name }}, @endif
                                                                @if($address->district_name) {{ $address->district_name }}, @endif
                                                                @if($address->regency_name) {{ $address->regency_name }}, @endif
                                                                @if($address->province_name) {{ $address->province_name }} @endif
                                                                @if($address->postal_code) {{ $address->postal_code }} @endif
                                                            </p>
                                                        @else
                                                            <p>{{ $address->village }}, {{ $address->sub_district }}, {{ $address->regency }}, {{ $address->province }} {{ $address->postal_code }}</p>
                                                        @endif
                                                    </div>

                                                    @if($address->notes)
                                                        <p class="text-xs text-gray-500 mt-1">{{ $address->notes }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6 sm:py-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 sm:h-16 sm:w-16 mx-auto text-gray-400 mb-3 sm:mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <p class="text-gray-500 mb-3 sm:mb-4 text-sm sm:text-base">Belum ada alamat tersimpan</p>
                            <button type="button" wire:click="toggleAddressForm" class="btn btn-success btn-sm sm:btn-md">Tambah Alamat Pertama</button>
                        </div>
                    @endif

                    @error('selected_address_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <!-- Distance Warning Display -->
                    @if($delivery_method === 'delivery' && !empty($distance_warning))
                        <div class="mt-4 p-4 rounded-lg {{ !$is_delivery_available ? 'bg-error/10 border border-error/20' : 'bg-warning/10 border border-warning/20' }}">
                            <div class="flex items-start gap-3">
                                @if(!$is_delivery_available)
                                    <!-- Error Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-error flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                @else
                                    <!-- Warning Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-warning flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                @endif
                                <div class="flex-1">
                                    <p class="text-sm {{ !$is_delivery_available ? 'text-error' : 'text-warning' }} font-medium">
                                        {{ !$is_delivery_available ? 'Alamat di Luar Jangkauan' : 'Peringatan Jarak' }}
                                    </p>
                                    <p class="text-sm {{ !$is_delivery_available ? 'text-error/80' : 'text-warning/80' }} mt-1">
                                        {{ $distance_warning }}
                                    </p>
                                    @if($calculated_distance)
                                        <p class="text-xs {{ !$is_delivery_available ? 'text-error/60' : 'text-warning/60' }} mt-2">
                                            Jarak yang dihitung: {{ $calculated_distance }} km dari apotek
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                    </div>
                </div>
            @endif

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Catatan Tambahan (Opsional)
                </label>
                <textarea 
                    id="notes"
                    wire:model="notes"
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Catatan khusus untuk apoteker (misal: alergi obat, kondisi khusus, dll)"
                ></textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>



            <!-- Submit Button -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 sm:gap-4">
                <a href="{{ route('customer.prescriptions.index') }}" class="btn btn-outline btn-sm sm:btn-md order-2 sm:order-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Batal
                </a>
                <button 
                    type="submit" 
                    class="btn btn-primary btn-sm sm:btn-md order-1 sm:order-2"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Upload Resep
                    </span>
                    <span wire:loading class="flex items-center">
                        <span class="loading loading-spinner loading-xs sm:loading-sm mr-1 sm:mr-2"></span>
                        Mengupload...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>