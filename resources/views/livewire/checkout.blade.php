<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Checkout</h1>
            <div class="breadcrumbs text-sm">
                <ul>
                    <li><a href="{{ route('home') }}" class="text-green-600 hover:text-green-800">Home</a></li>
                    <li><a href="{{ route('cart') }}" class="text-green-600 hover:text-green-800">Keranjang</a></li>
                    <li class="text-gray-500">Checkout</li>
                </ul>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="alert alert-success mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-error mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if (isset($checkoutSummary['error']))
            <div class="alert alert-error mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ $checkoutSummary['error'] }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Shipping & Address -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Shipping Type Selection -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                            Pilih Metode Pengiriman
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Store Pickup -->
                            <label class="cursor-pointer">
                                <input type="radio" wire:model.live="shippingType" value="pickup" name="shipping_method" class="radio radio-success" />
                                <div class="card bg-base-200 border-2 {{ $shippingType === 'pickup' ? 'border-success' : 'border-transparent' }} ml-3">
                                    <div class="card-body p-4">
                                        <h3 class="font-semibold">Ambil di Toko</h3>
                                        <p class="text-sm text-gray-600">Gratis - Ambil langsung di apotek</p>
                                        <div class="text-xs text-gray-500 mt-2">
                                            <p>📍 Apotek Baraya</p>
                                            <p>⏰ Senin-Sabtu: 08:00-20:00</p>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Home Delivery -->
                            <label class="cursor-pointer">
                                <input type="radio" wire:model.live="shippingType" value="delivery" name="shipping_method" class="radio radio-success" />
                                <div class="card bg-base-200 border-2 {{ $shippingType === 'delivery' ? 'border-success' : 'border-transparent' }} ml-3">
                                    <div class="card-body p-4">
                                        <h3 class="font-semibold">Kirim ke Alamat</h3>
                                        <p class="text-sm text-gray-600">Rp 2.000/km - Maksimal 15km</p>
                                        <div class="text-xs text-gray-500 mt-2">
                                            <p>🚚 Estimasi 1-2 jam</p>
                                            <p>💰 Gratis ongkir min. Rp 100.000</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Address Selection (only for delivery) -->
                @if ($shippingType === 'delivery')
                    <div class="card bg-base-100 shadow-lg">
                        <div class="card-body">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="card-title text-xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Pilih Alamat Pengiriman
                                </h2>
                                <button type="button" wire:click="toggleAddressForm" class="btn btn-success btn-sm">
                                    @if ($showAddressForm)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Batal
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Tambah Alamat
                                    @endif
                                </button>
                            </div>

                            <!-- Add New Address Form -->
                            @if ($showAddressForm)
                                <div class="bg-base-200 p-6 rounded-lg mb-6">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-lg font-semibold">Tambah Alamat Baru</h3>
                                        <button wire:click="toggleAddressForm" class="btn btn-ghost btn-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    <form wire:submit="saveNewAddress" class="space-y-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <!-- Label -->
                                            <div class="form-control">
                                                <label class="label">
                                                    <span class="label-text font-medium">Label Alamat <span class="text-red-500">*</span></span>
                                                </label>
                                                <select wire:model="newAddress.label" class="select select-bordered w-full @error('newAddress.label') select-error @enderror" required>
                                                    <option value="rumah">Rumah</option>
                                                    <option value="kantor">Kantor</option>
                                                    <option value="kost">Kost</option>
                                                    <option value="lainnya">Lainnya</option>
                                                </select>
                                                @error('newAddress.label')
                                                <label class="label">
                                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                                </label>
                                                @enderror
                                            </div>

                                            <!-- Recipient Name -->
                                            <div class="form-control">
                                                <label class="label">
                                                    <span class="label-text font-medium">Nama Penerima <span class="text-red-500">*</span></span>
                                                </label>
                                                <input
                                                    type="text"
                                                    wire:model="newAddress.recipient_name"
                                                    class="input input-bordered w-full @error('newAddress.recipient_name') input-error @enderror"
                                                    placeholder="Nama penerima"
                                                    required>
                                                @error('newAddress.recipient_name')
                                                <label class="label">
                                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                                </label>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Phone -->
                                        <div class="form-control">
                                            <label class="label">
                                                <span class="label-text font-medium">Nomor Telepon <span class="text-red-500">*</span></span>
                                            </label>
                                            <input
                                                type="text"
                                                wire:model="newAddress.phone"
                                                class="input input-bordered w-full @error('newAddress.phone') input-error @enderror"
                                                placeholder="Nomor telepon penerima"
                                                required>
                                            @error('newAddress.phone')
                                            <label class="label">
                                                <span class="label-text-alt text-error">{{ $message }}</span>
                                            </label>
                                            @enderror
                                        </div>

                                        <!-- Detailed Address Structure -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <!-- Village -->
                                            <div class="form-control">
                                                <label class="label">
                                                    <span class="label-text font-medium">Desa/Kelurahan <span class="text-red-500">*</span></span>
                                                </label>
                                                <input
                                                    type="text"
                                                    wire:model="newAddress.village"
                                                    class="input input-bordered w-full @error('newAddress.village') input-error @enderror"
                                                    placeholder="Nama desa atau kelurahan"
                                                    required>
                                                @error('newAddress.village')
                                                <label class="label">
                                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                                </label>
                                                @enderror
                                            </div>

                                            <!-- Sub District -->
                                            <div class="form-control">
                                                <label class="label">
                                                    <span class="label-text font-medium">Kecamatan <span class="text-red-500">*</span></span>
                                                </label>
                                                <input
                                                    type="text"
                                                    wire:model="newAddress.sub_district"
                                                    class="input input-bordered w-full @error('newAddress.sub_district') input-error @enderror"
                                                    placeholder="Nama kecamatan"
                                                    required>
                                                @error('newAddress.sub_district')
                                                <label class="label">
                                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                                </label>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <!-- Regency -->
                                            <div class="form-control">
                                                <label class="label">
                                                    <span class="label-text font-medium">Kabupaten/Kota <span class="text-red-500">*</span></span>
                                                </label>
                                                <input
                                                    type="text"
                                                    wire:model="newAddress.regency"
                                                    class="input input-bordered w-full @error('newAddress.regency') input-error @enderror"
                                                    placeholder="Nama kabupaten atau kota"
                                                    required>
                                                @error('newAddress.regency')
                                                <label class="label">
                                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                                </label>
                                                @enderror
                                            </div>

                                            <!-- Province -->
                                            <div class="form-control">
                                                <label class="label">
                                                    <span class="label-text font-medium">Provinsi <span class="text-red-500">*</span></span>
                                                </label>
                                                <input
                                                    type="text"
                                                    wire:model="newAddress.province"
                                                    class="input input-bordered w-full @error('newAddress.province') input-error @enderror"
                                                    placeholder="Nama provinsi"
                                                    required>
                                                @error('newAddress.province')
                                                <label class="label">
                                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                                </label>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Postal Code -->
                                        <div class="form-control">
                                            <label class="label">
                                                <span class="label-text font-medium">Kode Pos <span class="text-red-500">*</span></span>
                                            </label>
                                            <input
                                                type="text"
                                                wire:model="newAddress.postal_code"
                                                class="input input-bordered w-full @error('newAddress.postal_code') input-error @enderror"
                                                placeholder="Kode pos (5 digit)"
                                                required>
                                            @error('newAddress.postal_code')
                                            <label class="label">
                                                <span class="label-text-alt text-error">{{ $message }}</span>
                                            </label>
                                            @enderror
                                        </div>

                                        <!-- Detailed Address -->
                                        <div class="form-control">
                                            <label class="label">
                                                <span class="label-text font-medium">Alamat Lengkap <span class="text-red-500">*</span></span>
                                            </label>
                                            <textarea
                                                wire:model="newAddress.detailed_address"
                                                class="textarea textarea-bordered w-full @error('newAddress.detailed_address') textarea-error @enderror"
                                                placeholder="Contoh: Jalan Merdeka No. 123, RT 02/RW 05, Dusun Krajan"
                                                rows="3"
                                                required></textarea>
                                            @error('newAddress.detailed_address')
                                            <label class="label">
                                                <span class="label-text-alt text-error">{{ $message }}</span>
                                            </label>
                                            @enderror
                                        </div>

                                        <!-- Hidden fields for backward compatibility -->
                                        <input type="hidden" wire:model="newAddress.district" />
                        <input type="hidden" wire:model="newAddress.city" />
                                        </div>

                                        <!-- Notes -->
                                        <div class="form-control">
                                            <label class="label">
                                                <span class="label-text font-medium">Catatan (Opsional)</span>
                                            </label>
                                            <textarea
                                                wire:model="newAddress.notes"
                                                class="textarea textarea-bordered w-full @error('newAddress.notes') textarea-error @enderror"
                                                placeholder="Patokan atau catatan tambahan"
                                                rows="2"></textarea>
                                            @error('newAddress.notes')
                                            <label class="label">
                                                <span class="label-text-alt text-error">{{ $message }}</span>
                                            </label>
                                            @enderror
                                        </div>

                                        <!-- Form Actions -->
                                        <div class="flex gap-2 pt-4">
                                            <button type="submit" class="btn btn-success">
                                                <span wire:loading.remove>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                                    </svg>
                                                    Simpan Alamat
                                                </span>
                                                <span wire:loading>
                                                    <span class="loading loading-spinner loading-sm mr-2"></span>
                                                    Menyimpan...
                                                </span>
                                            </button>
                                            <button type="button" wire:click="toggleAddressForm" class="btn btn-ghost">
                                                Batal
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endif

                            <!-- Address List -->
                            @if ($addresses->count() > 0)
                                <div class="space-y-3">
                                    @foreach ($addresses as $address)
                                        <label class="cursor-pointer">
                                            <input type="radio" wire:model.live="selectedAddressId" value="{{ $address->address_id }}" name="delivery_address" class="radio radio-success" />
                                            <div class="card bg-base-200 border-2 {{ $selectedAddressId == $address->address_id ? 'border-success' : 'border-transparent' }} ml-3">
                                                <div class="card-body p-4">
                                                    <div class="flex justify-between items-start">
                                                        <div>
                                                            <div class="flex items-center gap-2 mb-1">
                                                                <span class="badge badge-outline">{{ ucfirst($address->label) }}</span>
                                                                @if ($address->is_default)
                                                                    <span class="badge badge-success badge-sm">Default</span>
                                                                @endif
                                                            </div>
                                                            <h4 class="font-semibold">{{ $address->recipient_name }}</h4>
                                                            <p class="text-sm text-gray-600">{{ $address->phone }}</p>
                                                            
                                                            @if ($address->detailed_address)
                                                                <p class="text-sm mt-1">{{ $address->detailed_address }}</p>
                                                            @else
                                                                <p class="text-sm mt-1">{{ $address->address }}</p>
                                                            @endif
                                                            
                                                            <div class="text-sm text-gray-600 mt-1">
                                                                @if ($address->village || $address->sub_district || $address->regency || $address->province)
                                                                    <p>
                                                                        @if ($address->village) {{ $address->village }}, @endif
                                                                        @if ($address->sub_district) {{ $address->sub_district }}, @endif
                                                                        @if ($address->regency) {{ $address->regency }}, @endif
                                                                        @if ($address->province) {{ $address->province }} @endif
                                                                        @if ($address->postal_code) {{ $address->postal_code }} @endif
                                                                    </p>
                                                                @else
                                                                    <p>{{ $address->district }}, {{ $address->city }} {{ $address->postal_code }}</p>
                                                                @endif
                                                            </div>
                                                            
                                                            @if ($address->notes)
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
                                <div class="text-center py-8">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <p class="text-gray-500 mb-4">Belum ada alamat tersimpan</p>
                                    <button type="button" wire:click="toggleAddressForm" class="btn btn-success">Tambah Alamat Pertama</button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Order Notes -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Catatan Pesanan
                        </h2>
                        <div class="form-control">
                            <textarea wire:model="notes" class="textarea textarea-bordered" rows="3" placeholder="Tambahkan catatan untuk pesanan Anda (opsional)"></textarea>
                            @error('notes') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="lg:col-span-1">
                <div class="card bg-base-100 shadow-lg sticky top-4">
                    <div class="card-body">
                        <h2 class="card-title text-xl mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Ringkasan Pesanan
                        </h2>

                        @if (isset($checkoutSummary['cart_items']) && count($checkoutSummary['cart_items']) > 0)
                            <!-- Cart Items -->
                            <div class="space-y-3 mb-4">
                                @foreach ($checkoutSummary['cart_items'] as $item)
                                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-sm">{{ $item->product->name }}</h4>
                                            @if($item->product->discount_price)
                                                <p class="text-xs text-gray-500">{{ $item->quantity }}x @ 
                                                    <span class="line-through text-gray-400">Rp {{ number_format($item->product->price, 0, ',', '.') }}</span>
                                                    <span class="text-red-600 font-semibold">Rp {{ number_format($item->product->final_price, 0, ',', '.') }}</span>
                                                </p>
                                            @else
                                                <p class="text-xs text-gray-500">{{ $item->quantity }}x @ Rp {{ number_format($item->product->final_price, 0, ',', '.') }}</p>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            @if($item->product->discount_price)
                                                <p class="text-xs text-gray-400 line-through">Rp {{ number_format($item->quantity * $item->product->price, 0, ',', '.') }}</p>
                                                <p class="font-medium text-red-600">Rp {{ number_format($item->quantity * $item->product->final_price, 0, ',', '.') }}</p>
                                            @else
                                                <p class="font-medium">Rp {{ number_format($item->quantity * $item->product->final_price, 0, ',', '.') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Summary Calculations -->
                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between">
                                    <span>Subtotal</span>
                                    <span>Rp {{ number_format($checkoutSummary['subtotal'] ?? 0, 0, ',', '.') }}</span>
                                </div>
                                
                                @if ($shippingType === 'delivery')
                                    <div class="flex justify-between">
                                        <span class="flex items-center gap-1">
                                            Ongkir
                                            @if (isset($checkoutSummary['shipping_distance']) && $checkoutSummary['shipping_distance'] > 0)
                                                <span class="text-xs text-gray-500">({{ $checkoutSummary['shipping_distance'] }}km)</span>
                                            @endif
                                        </span>
                                        <span class="{{ isset($checkoutSummary['is_free_shipping']) && $checkoutSummary['is_free_shipping'] ? 'line-through text-gray-500' : '' }}">
                                            Rp {{ number_format($checkoutSummary['shipping_cost'] ?? 0, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    
                                    @if (isset($checkoutSummary['is_free_shipping']) && $checkoutSummary['is_free_shipping'])
                                        <div class="flex justify-between text-green-600">
                                            <span>Gratis Ongkir</span>
                                            <span>-Rp {{ number_format($checkoutSummary['shipping_cost'] ?? 0, 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                    
                                    @if (isset($checkoutSummary['delivery_available']) && !$checkoutSummary['delivery_available'])
                                        <div class="alert alert-warning alert-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                            </svg>
                                            <span class="text-xs">Alamat melebihi jarak maksimal (15km)</span>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <div class="divider my-2"></div>
                            
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total</span>
                                <span>Rp {{ number_format($checkoutSummary['total'] ?? 0, 0, ',', '.') }}</span>
                            </div>

                            <div class="mt-6">
                                <button 
                                    type="button" 
                                    wire:click="processCheckout" 
                                    class="btn btn-success w-full"
                                    {{ $isProcessing || (isset($checkoutSummary['delivery_available']) && !$checkoutSummary['delivery_available'] && $shippingType === 'delivery') ? 'disabled' : '' }}
                                >
                                    @if ($isProcessing)
                                        <span class="loading loading-spinner loading-sm"></span>
                                        Memproses...
                                    @else
                                        Buat Pesanan
                                    @endif
                                </button>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5-6M20 13v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6" />
                                </svg>
                                <p class="text-gray-500 mb-4">Keranjang kosong</p>
                                <a href="{{ route('home') }}" class="btn btn-success">Mulai Belanja</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>