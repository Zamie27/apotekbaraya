<div class="max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Upload Resep Dokter</h2>

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
                    placeholder="Masukkan nama dokter">
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
                    placeholder="Masukkan nama pasien">
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
                    accept="image/*">

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
                                @try
                                @if (method_exists($prescription_image, 'temporaryUrl'))
                                <img src="{{ $prescription_image->temporaryUrl() }}" class="mx-auto h-32 w-auto rounded-lg shadow-md">
                                @endif
                                @if (method_exists($prescription_image, 'getClientOriginalName'))
                                <p class="mt-2 text-sm text-gray-600 font-medium">{{ $prescription_image->getClientOriginalName() }}</p>
                                @endif
                                <p class="text-xs text-green-600 mt-1">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Foto berhasil dipilih - Klik untuk mengganti
                                </p>
                                @catch(\Exception $e)
                                <div class="text-center">
                                    <i class="fas fa-image text-gray-400 text-4xl mb-2"></i>
                                    <p class="text-sm text-gray-600 font-medium">Foto sedang diproses...</p>
                                    <p class="text-xs text-blue-600 mt-1">
                                        <i class="fas fa-spinner fa-spin mr-1"></i>
                                        Klik untuk mengganti foto
                                    </p>
                                </div>
                                @endtry
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
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800">Pilih Metode Pengambilan Obat</h3>

                <!-- Pickup Option -->
                <div
                    wire:click="$set('delivery_method', 'pickup')"
                    class="border-2 rounded-lg p-4 transition-all duration-200 cursor-pointer {{ $delivery_method === 'pickup' ? 'border-blue-500 bg-blue-50 shadow-md' : 'border-gray-200 hover:border-gray-300' }}">
                    <div class="flex items-start space-x-3">
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h4 class="text-base font-medium {{ $delivery_method === 'pickup' ? 'text-blue-900' : 'text-gray-900' }}">
                                    <i class="fas fa-store mr-2 {{ $delivery_method === 'pickup' ? 'text-blue-600' : 'text-gray-500' }}"></i>
                                    Ambil di Toko
                                </h4>
                                <span class="text-sm font-bold text-green-600 bg-green-100 px-2 py-1 rounded-full">GRATIS</span>
                            </div>
                            <div class="mt-2 text-sm {{ $delivery_method === 'pickup' ? 'text-blue-700' : 'text-gray-600' }} space-y-1">
                                <p><i class="fas fa-map-marker-alt {{ $delivery_method === 'pickup' ? 'text-blue-500' : 'text-gray-400' }} mr-2"></i>{{ \App\Models\StoreSetting::get('store_address', 'Jl. Raya Apotek No. 123') }}</p>
                                <p><i class="fas fa-clock {{ $delivery_method === 'pickup' ? 'text-blue-500' : 'text-gray-400' }} mr-2"></i>{{ \App\Models\StoreSetting::get('store_hours', 'Senin-Sabtu: 08:00-20:00') }}</p>
                                <p class="text-xs {{ $delivery_method === 'pickup' ? 'text-blue-600' : 'text-gray-500' }}">Obat dapat diambil setelah mendapat konfirmasi dari apoteker</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delivery Option -->
                <div
                    wire:click="$set('delivery_method', 'delivery')"
                    class="border-2 rounded-lg p-4 transition-all duration-200 cursor-pointer {{ $delivery_method === 'delivery' ? 'border-blue-500 bg-blue-50 shadow-md' : 'border-gray-200 hover:border-gray-300' }}">
                    <div class="flex items-start space-x-3">
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h4 class="text-base font-medium {{ $delivery_method === 'delivery' ? 'text-blue-900' : 'text-gray-900' }}">
                                    <i class="fas fa-truck mr-2 {{ $delivery_method === 'delivery' ? 'text-blue-600' : 'text-gray-500' }}"></i>
                                    Kirim ke Alamat
                                </h4>
                                <span class="text-sm font-bold text-blue-600 bg-blue-100 px-2 py-1 rounded-full">Rp {{ number_format(\App\Models\StoreSetting::get('delivery_fee_per_km', 2000), 0, ',', '.') }}/km</span>
                            </div>
                            <div class="mt-2 text-sm {{ $delivery_method === 'delivery' ? 'text-blue-700' : 'text-gray-600' }} space-y-1">
                                <p><i class="fas fa-shipping-fast {{ $delivery_method === 'delivery' ? 'text-blue-500' : 'text-gray-400' }} mr-2"></i>Estimasi {{ \App\Models\StoreSetting::get('delivery_estimation', '1-2 jam') }} setelah obat siap</p>
                                <p><i class="fas fa-route {{ $delivery_method === 'delivery' ? 'text-blue-500' : 'text-gray-400' }} mr-2"></i>Jarak maksimal {{ \App\Models\StoreSetting::get('max_delivery_distance', 15) }} km dari toko</p>
                                @if(\App\Models\StoreSetting::get('free_delivery_minimum', 0) > 0)
                                <p class="text-xs text-green-600">
                                    <i class="fas fa-gift text-green-500 mr-2"></i>
                                    Gratis ongkir untuk pembelian minimal Rp {{ number_format(\App\Models\StoreSetting::get('free_delivery_minimum', 0), 0, ',', '.') }}
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @error('delivery_method')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Address Selection (only for delivery) -->
            @if($delivery_method === 'delivery')
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Pilih Alamat Pengiriman</h3>
                    <button
                        type="button"
                        wire:click="toggleAddressForm"
                        class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        {{ $show_address_form ? 'Batal' : 'Tambah Alamat' }}
                    </button>
                </div>

                <!-- Add New Address Form -->
                @if($show_address_form)
                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                    <h4 class="text-base font-medium text-gray-900 mb-4">Tambah Alamat Baru</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Label -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Label Alamat</label>
                            <select wire:model="address_form.label" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="rumah">Rumah</option>
                                <option value="kantor">Kantor</option>
                                <option value="kost">Kost</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                            @error('address_form.label') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Recipient Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penerima</label>
                            <input type="text" wire:model="address_form.recipient_name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('address_form.recipient_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                            <input type="text" wire:model="address_form.phone" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('address_form.phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Province -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                            <select wire:model="address_form.province_key" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih Provinsi</option>
                                @if(is_array($provinces))
                                @foreach($provinces as $province)
                                @if(is_array($province) && isset($province['key']) && isset($province['name']))
                                <option value="{{ $province['key'] }}">{{ $province['name'] }}</option>
                                @endif
                                @endforeach
                                @endif
                            </select>
                            @error('address_form.province_key') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Regency -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kabupaten/Kota</label>
                            <select wire:model="address_form.regency_key" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" {{ empty($regencies) ? 'disabled' : '' }}>
                                <option value="">Pilih Kabupaten/Kota</option>
                                @if(is_array($regencies))
                                @foreach($regencies as $regency)
                                @if(is_array($regency) && isset($regency['key']) && isset($regency['name']))
                                <option value="{{ $regency['key'] }}">{{ $regency['name'] }}</option>
                                @endif
                                @endforeach
                                @endif
                            </select>
                            @error('address_form.regency_key') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Sub District -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                            <select wire:model="address_form.sub_district_key" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" {{ empty($sub_districts) ? 'disabled' : '' }}>
                                <option value="">Pilih Kecamatan</option>
                                @if(is_array($sub_districts))
                                @foreach($sub_districts as $sub_district)
                                @if(is_array($sub_district) && isset($sub_district['key']) && isset($sub_district['name']))
                                <option value="{{ $sub_district['key'] }}">{{ $sub_district['name'] }}</option>
                                @endif
                                @endforeach
                                @endif
                            </select>
                            @error('address_form.sub_district_key') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Village -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Desa/Kelurahan</label>
                            <select wire:model="address_form.village_key" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" {{ empty($villages) ? 'disabled' : '' }}>
                                <option value="">Pilih Desa/Kelurahan</option>
                                @if(is_array($villages))
                                @foreach($villages as $village)
                                @if(is_array($village) && isset($village['key']) && isset($village['name']))
                                <option value="{{ $village['key'] }}">{{ $village['name'] }}</option>
                                @endif
                                @endforeach
                                @endif
                            </select>
                            @error('address_form.village_key') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Postal Code -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
                            <select wire:model="address_form.postal_code" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" {{ empty($postal_codes) ? 'disabled' : '' }}>
                                <option value="">Pilih Kode Pos</option>
                                @foreach($postal_codes as $postal_code)
                                <option value="{{ $postal_code }}">{{ $postal_code }}</option>
                                @endforeach
                            </select>
                            @error('address_form.postal_code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Detailed Address -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                        <textarea wire:model="address_form.detailed_address" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nama jalan, nomor rumah, RT/RW, patokan, dll"></textarea>
                        @error('address_form.detailed_address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Notes -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                        <input type="text" wire:model="address_form.notes" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Catatan untuk kurir">
                    </div>

                    <!-- Address Preview -->
                    @if($address_preview)
                    <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                        <p class="text-sm text-blue-800"><strong>Preview Alamat:</strong></p>
                        <p class="text-sm text-blue-700">{{ $address_preview }}</p>
                    </div>
                    @endif

                    <!-- Default Address Checkbox -->
                    <div class="mt-4">
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="address_form.is_default" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Jadikan alamat utama</span>
                        </label>
                    </div>

                    <!-- Save Button -->
                    <div class="mt-4 flex justify-end">
                        <button
                            type="button"
                            wire:click="saveNewAddress"
                            class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Simpan Alamat
                        </button>
                    </div>
                </div>
                @endif

                <!-- Address List -->
                @if(count($addresses) > 0)
                <div class="space-y-3">
                    @foreach($addresses as $address)
                    <div
                        wire:click="$set('selected_address_id', '{{ $address->address_id }}')"
                        class="border rounded-lg p-4 cursor-pointer transition-all duration-200 {{ $selected_address_id == $address->address_id ? 'border-blue-500 bg-blue-50 shadow-md' : 'border-gray-200 hover:border-gray-300' }}">
                        <div class="flex items-start space-x-3">
                            <input
                                type="radio"
                                wire:model="selected_address_id"
                                value="{{ $address->address_id }}"
                                class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $selected_address_id == $address->address_id ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }} capitalize">
                                        {{ $address->label }}
                                    </span>
                                    @if($address->is_default)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Utama
                                    </span>
                                    @endif
                                </div>
                                <div class="text-sm {{ $selected_address_id == $address->address_id ? 'text-blue-900' : 'text-gray-900' }}">
                                    <p class="font-medium">{{ $address->recipient_name }}</p>
                                    <p class="{{ $selected_address_id == $address->address_id ? 'text-blue-700' : 'text-gray-600' }}">{{ $address->phone }}</p>
                                </div>
                                <div class="mt-2 text-sm {{ $selected_address_id == $address->address_id ? 'text-blue-700' : 'text-gray-600' }}">
                                    <p>{{ $address->detailed_address }}</p>
                                    <p>{{ $address->village }}, {{ $address->sub_district }}, {{ $address->regency }}, {{ $address->province }} {{ $address->postal_code }}</p>
                                    @if($address->notes)
                                    <p class="text-xs {{ $selected_address_id == $address->address_id ? 'text-blue-600' : 'text-gray-500' }} mt-1">Catatan: {{ $address->notes }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8 border-2 border-dashed border-gray-300 rounded-lg">
                    <i class="fas fa-map-marker-alt text-gray-400 text-3xl mb-4"></i>
                    <p class="text-gray-600 mb-4">Belum ada alamat tersimpan</p>
                    <button
                        type="button"
                        wire:click="toggleAddressForm"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Alamat Pertama
                    </button>
                </div>
                @endif

                @error('selected_address_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
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
                    placeholder="Catatan khusus untuk apoteker (misal: alergi obat, kondisi khusus, dll)"></textarea>
                @error('notes')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('customer.prescriptions.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Batal
                </a>
                <button
                    type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>Upload Resep</span>
                    <span wire:loading>Mengupload...</span>
                </button>
            </div>
        </form>
    </div>
</div>
