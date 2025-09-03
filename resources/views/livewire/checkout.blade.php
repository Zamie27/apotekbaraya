<div class="container mx-auto px-3 sm:px-4 py-4 sm:py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">Checkout</h1>
            <div class="breadcrumbs text-xs sm:text-sm">
                <ul>
                    <li><a href="{{ route('home') }}" class="text-green-600 hover:text-green-800">Home</a></li>
                    <li><a href="{{ route('cart') }}" class="text-green-600 hover:text-green-800">Keranjang</a></li>
                    <li class="text-gray-500">Checkout</li>
                </ul>
            </div>
        </div>

        @if (session()->has('success'))
        <div class="alert alert-success mb-4 sm:mb-6 text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if (session()->has('error'))
        <div class="alert alert-error mb-4 sm:mb-6 text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        @if (isset($checkoutSummary['error']))
        <div class="alert alert-error mb-4 sm:mb-6 text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ $checkoutSummary['error'] }}</span>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
            <!-- Left Column: Shipping & Address -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Shipping Type Selection -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body p-4 sm:p-6">
                        <h2 class="card-title text-lg sm:text-xl mb-3 sm:mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                            <span class="text-base sm:text-xl">Pilih Metode Pengiriman</span>
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                            <!-- Store Pickup -->
                            <label class="cursor-pointer">
                                <input type="radio" wire:model.live="shippingType" value="pickup" name="shipping_method" class="radio radio-success" />
                                <div class="card bg-base-200 border-2 {{ $shippingType === 'pickup' ? 'border-success' : 'border-transparent' }} ml-3">
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
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Home Delivery -->
                            <label class="cursor-pointer">
                                <input type="radio" wire:model.live="shippingType" value="delivery" name="shipping_method" class="radio radio-success" />
                                <div class="card bg-base-200 border-2 {{ $shippingType === 'delivery' ? 'border-success' : 'border-transparent' }} ml-3">
                                    <div class="card-body p-3 sm:p-4">
                                        <h3 class="font-semibold text-sm sm:text-base">Kirim ke Alamat</h3>
                                        <p class="text-xs sm:text-sm text-gray-600">Rp {{ number_format(\App\Models\StoreSetting::get('shipping_rate_per_km', 2000), 0, ',', '.') }}/km - Maksimal {{ \App\Models\StoreSetting::get('max_delivery_distance', 15) }}km</p>
                                        <div class="text-xs text-gray-500 mt-2">
                                            <p>🚚 Estimasi 1-2 jam</p>
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
                    </div>
                </div>

                <!-- Area Pengiriman Tersedia (only for delivery) -->
                @if ($shippingType === 'delivery')
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body p-4 sm:p-6">
                        <h2 class="card-title text-lg sm:text-xl mb-3 sm:mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                            <span class="text-base sm:text-xl">Jarak Maksimal Pengiriman</span>
                        </h2>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 sm:p-4">
                            <div class="flex items-start gap-2 sm:gap-3">
                                <div class="flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 text-blue-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-blue-800 mb-2 text-sm sm:text-base">Layanan pengiriman tersedia untuk alamat dengan jarak maksimal:</h3>
                                    <div class="flex items-center gap-2 sm:gap-3 mb-3">
                                        <div class="flex items-center gap-1.5 sm:gap-2">
                                            <span class="w-2.5 h-2.5 sm:w-3 sm:h-3 bg-green-500 rounded-full"></span>
                                            <span class="text-base sm:text-lg font-bold text-blue-700">{{ \App\Models\StoreSetting::get('max_delivery_distance', 15) }} km</span>
                                            <span class="text-xs sm:text-sm text-blue-600">dari toko</span>
                                        </div>
                                    </div>
                                    <p class="text-xs text-blue-600 mt-3">
                                        💡 <strong>Tips:</strong> Pastikan alamat Anda berada dalam radius jarak pengiriman untuk dapat menggunakan layanan pengiriman.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Address Selection (only for delivery) -->
                @if ($shippingType === 'delivery')
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
                                @if ($showAddressForm)
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
                        @if ($showAddressForm)
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
                            <form wire:submit="saveNewAddress">
                                <x-address-form
                                    :addressForm="$addressForm"
                                    :provinces="$provinces"
                                    :regencies="$regencies"
                                    :subDistricts="$subDistricts"
                                    :villages="$villages"
                                    :postalCodes="$postalCodes"
                                    :addressPreview="$addressPreview"
                                    :editingAddressId="null"
                                    cancelAction="toggleAddressForm" />
                            </form>
                        </div>
                        @endif

                        <!-- Address List -->
                        @if ($addresses->count() > 0)
                        <div class="space-y-4 sm:space-y-6">
                            @foreach ($addresses as $address)
                            <label class="cursor-pointer block mb-3 sm:mb-4">
                                <input type="radio" wire:model.live="selectedAddressId" value="{{ $address->address_id }}" name="delivery_address" class="radio radio-success" />
                                <div class="card bg-base-200 border-2 {{ $selectedAddressId == $address->address_id ? 'border-success' : 'border-transparent' }} ml-3">
                                    <div class="card-body p-3 sm:p-4">
                                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 sm:gap-0">
                                            <div class="flex-1">
                                                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 mb-1">
                                                    <span class="badge badge-outline text-xs">{{ ucfirst($address->label) }}</span>
                                                    @if ($address->is_default)
                                                    <span class="badge badge-success badge-sm text-xs">Default</span>
                                                    @endif
                                                </div>
                                                <h4 class="font-semibold text-sm sm:text-base">{{ $address->recipient_name }}</h4>
                                                <p class="text-xs sm:text-sm text-gray-600">{{ $address->phone }}</p>

                                                @if ($address->detailed_address)
                                                <p class="text-xs sm:text-sm mt-1">{{ $address->detailed_address }}</p>
                                                @else
                                                <p class="text-xs sm:text-sm mt-1">{{ $address->address }}</p>
                                                @endif

                                                <div class="text-xs sm:text-sm text-gray-600 mt-1">
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
                        <div class="text-center py-6 sm:py-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 sm:h-16 sm:w-16 mx-auto text-gray-400 mb-3 sm:mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <p class="text-gray-500 mb-3 sm:mb-4 text-sm sm:text-base">Belum ada alamat tersimpan</p>
                            <button type="button" wire:click="toggleAddressForm" class="btn btn-success btn-sm sm:btn-md">Tambah Alamat Pertama</button>
                        </div>
                        @endif
                    </div>
                </div>
                @endif



                <!-- Order Notes -->
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body p-4 sm:p-6">
                        <h2 class="card-title text-lg sm:text-xl mb-3 sm:mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Catatan Pesanan
                        </h2>
                        <div class="form-control">
                            <textarea wire:model="notes" class="textarea textarea-bordered text-sm" rows="3" placeholder="Tambahkan catatan untuk pesanan Anda (opsional)"></textarea>
                            @error('notes') <span class="text-error text-xs sm:text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="lg:col-span-1">
                <div class="card bg-base-100 shadow-lg lg:sticky lg:top-20">
                    <div class="card-body p-4 sm:p-6">
                        <h2 class="card-title text-lg sm:text-xl mb-3 sm:mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Ringkasan Pesanan
                        </h2>

                        @if (isset($checkoutSummary['cart_items']) && count($checkoutSummary['cart_items']) > 0)
                        <!-- Cart Items -->
                        <div class="space-y-2 sm:space-y-3 mb-3 sm:mb-4">
                            @foreach ($checkoutSummary['cart_items'] as $item)
                            <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                <div class="flex-1 pr-2">
                                    <h4 class="font-medium text-xs sm:text-sm">{{ $item->product->name }}</h4>
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
                                    <p class="font-medium text-red-600 text-xs sm:text-sm">Rp {{ number_format($item->quantity * $item->product->final_price, 0, ',', '.') }}</p>
                                    @else
                                    <p class="font-medium text-xs sm:text-sm">Rp {{ number_format($item->quantity * $item->product->final_price, 0, ',', '.') }}</p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Summary Calculations -->
                        <div class="space-y-2 mb-3 sm:mb-4">
                            <div class="flex justify-between text-sm">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($checkoutSummary['subtotal'] ?? 0, 0, ',', '.') }}</span>
                            </div>

                            @if ($shippingType === 'delivery')
                            <div class="flex justify-between text-sm">
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
                            <div class="flex justify-between text-green-600 text-sm">
                                <span>Gratis Ongkir</span>
                                <span>-Rp {{ number_format($checkoutSummary['shipping_cost'] ?? 0, 0, ',', '.') }}</span>
                            </div>
                            @endif

                            @if (isset($checkoutSummary['delivery_available']) && !$checkoutSummary['delivery_available'])
                            <div class="alert alert-warning alert-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-3 w-3 sm:h-4 sm:w-4" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                                <span class="text-xs">Alamat melebihi jarak maksimal (15km)</span>
                            </div>
                            @endif
                            @endif
                        </div>

                        <div class="divider my-2"></div>

                        <div class="flex justify-between text-base sm:text-lg font-bold">
                            <span>Total</span>
                            <span>Rp {{ number_format($checkoutSummary['total'] ?? 0, 0, ',', '.') }}</span>
                        </div>

                        <!-- reCAPTCHA Hidden Input -->
                        <input type="hidden" wire:model="recaptchaToken" id="recaptcha-token">

                        <div class="mt-4 sm:mt-6">
                            <button
                                type="button"
                                id="checkout-button"
                                class="btn btn-success w-full text-sm sm:text-base"
                                {{ $isProcessing || (isset($checkoutSummary['delivery_available']) && !$checkoutSummary['delivery_available'] && $shippingType === 'delivery') || (isset($checkoutSummary['address_required']) && $checkoutSummary['address_required']) ? 'disabled' : '' }}>
                                @if ($isProcessing)
                                <span class="loading loading-spinner loading-sm"></span>
                                Memproses...
                                @elseif (isset($checkoutSummary['address_required']) && $checkoutSummary['address_required'])
                                Pilih Alamat Dulu
                                @else
                                Buat Pesanan
                                @endif
                            </button>

                            <!-- Debug Info -->
                            @if (config('app.debug'))
                            <div class="mt-2 text-xs text-gray-500">
                                Debug: Shipping={{ $shippingType }}, Address={{ $selectedAddressId }}
                            </div>
                            @endif
                        </div>
                        @else
                        <div class="text-center py-6 sm:py-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 sm:h-16 sm:w-16 mx-auto text-gray-400 mb-3 sm:mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5-6M20 13v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6" />
                            </svg>
                            <p class="text-gray-500 mb-3 sm:mb-4 text-sm sm:text-base">Keranjang kosong</p>
                            <a href="{{ route('home') }}" class="btn btn-success btn-sm sm:btn-md">Mulai Belanja</a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Google reCAPTCHA v3 -->
@if(config('app.env') === 'production')
<script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
@endif
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkoutButton = document.getElementById('checkout-button');
        const recaptchaToken = document.getElementById('recaptcha-token');
        const isProduction = {{ config('app.env') === 'production' ? 'true' : 'false' }};

        if (checkoutButton && recaptchaToken) {
            checkoutButton.addEventListener('click', function(e) {
                e.preventDefault();

                // Check if button is disabled
                if (this.disabled) {
                    return;
                }
                
                // Handle reCAPTCHA based on environment
                if (isProduction && typeof grecaptcha !== 'undefined') {
                    // Execute reCAPTCHA for production
                    grecaptcha.ready(function() {
                        grecaptcha.execute('{{ config("services.recaptcha.site_key") }}', {
                                action: 'checkout'
                            })
                            .then(function(token) {
                                // Set the token to the hidden input
                                recaptchaToken.value = token;

                                // Trigger Livewire method
                                @this.set('recaptchaToken', token);
                                @this.call('processCheckout');
                            })
                            .catch(function(error) {
                                // For production, show error and don't proceed
                                alert('Terjadi kesalahan verifikasi keamanan. Silakan coba lagi.');
                            });
                    });
                } else {
                    // For development/localhost, skip reCAPTCHA and proceed directly
                    recaptchaToken.value = 'dev-token-' + Date.now();
                    @this.set('recaptchaToken', recaptchaToken.value);
                    @this.call('processCheckout');
                }
            });
        }
    });
</script>
@endpush