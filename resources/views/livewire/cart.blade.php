<div class="min-h-screen bg-gray-50">
    <!-- Confirmation Modal -->
    @livewire('confirmation-modal')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($cart && !$cart->isEmpty())
        <div class="lg:grid lg:grid-cols-12 lg:gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-8">
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-medium text-gray-900">Item Keranjang ({{ $cartSummary['items_count'] }})</h2>
                            <button wire:click="clearCart"
                                class="text-sm text-red-600 hover:text-red-800 transition-colors">
                                Kosongkan Keranjang
                            </button>
                        </div>
                    </div>

                    <div class="divide-y divide-gray-200">
                        @foreach($cart->cartItems as $item)
                        <div class="p-6" wire:key="cart-item-{{ $item->cart_item_id }}">
                            <div class="flex items-start space-x-4">
                                <!-- Product Image -->
                                <div class="flex-shrink-0">
                                    @if($item->product->images->isNotEmpty())
                                    <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}"
                                        alt="{{ $item->product->name }}"
                                        class="w-20 h-20 object-cover rounded-lg">
                                    @else
                                    <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    @endif
                                </div>

                                <!-- Product Details -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="text-sm font-medium text-gray-900 line-clamp-2">{{ $item->product->name }}</h3>
                                            <p class="text-sm text-gray-500 mt-1">{{ $item->product->category->name }}</p>
                                            
                                            {{-- Price Display with Discount Info --}}
                                            <div class="flex items-center gap-2 mt-1">
                                                @if($item->product->is_on_sale)
                                                    {{-- Show discount badge --}}
                                                    <span class="badge badge-error badge-xs">-{{ $item->product->discount_percentage }}%</span>
                                                    {{-- Original price (crossed out) --}}
                                                    <span class="text-xs text-gray-400 line-through">{{ $item->product->formatted_price }}</span>
                                                    {{-- Discounted price --}}
                                                    <span class="text-sm font-medium text-green-600">{{ $item->product->formatted_discount_price }}</span>
                                                @else
                                                    {{-- Regular price --}}
                                                    <span class="text-sm font-medium text-green-600">{{ $item->formatted_price }}</span>
                                                @endif
                                            </div>

                                            @if($item->product->stock < $item->quantity)
                                                <p class="text-xs text-red-600 mt-1">
                                                    <svg class="w-3 h-3 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Stok tidak mencukupi (tersedia: {{ $item->product->stock }})
                                                </p>
                                                @endif
                                        </div>

                                        <!-- Remove Button -->
                                        <button wire:click="removeItem({{ $item->cart_item_id }})"
                                            class="ml-4 text-gray-400 hover:text-red-500 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Quantity Controls -->
                                    <div class="flex items-center mt-4 space-x-3">
                                        <span class="text-sm text-gray-700">Jumlah:</span>
                                        <div class="flex items-center border border-gray-300 rounded-lg">
                                            <button wire:click="decreaseQuantity({{ $item->cart_item_id }})"
                                                class="p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-50 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                </svg>
                                            </button>
                                            <span class="px-4 py-2 text-sm font-medium text-gray-900 border-x border-gray-300">{{ $item->quantity }}</span>
                                            <button wire:click="increaseQuantity({{ $item->cart_item_id }})"
                                                class="p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-50 transition-colors"
                                                @if($item->quantity >= $item->product->stock) disabled @endif>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <span class="text-sm text-gray-500">Stok: {{ $item->product->stock }}</span>
                                    </div>

                                    <!-- Subtotal with Discount Info -->
                                    <div class="mt-3">
                                        @if($item->product->is_on_sale)
                                            {{-- Show original subtotal (crossed out) and discounted subtotal --}}
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs text-gray-400 line-through">Rp {{ number_format($item->quantity * $item->product->price, 0, ',', '.') }}</span>
                                                <span class="text-sm font-medium text-green-600">{{ $item->formatted_subtotal }}</span>
                                            </div>
                                            <div class="text-xs text-green-600 mt-1">
                                                Hemat: Rp {{ number_format(($item->product->price - $item->product->discount_price) * $item->quantity, 0, ',', '.') }}
                                            </div>
                                        @else
                                            <span class="text-sm font-medium text-gray-900">Subtotal: {{ $item->formatted_subtotal }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-4 mt-8 lg:mt-0">
                <div class="bg-white rounded-lg shadow-sm sticky top-20">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-900">Ringkasan Pesanan</h2>
                    </div>

                    <div class="px-6 py-4 space-y-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Total Item</span>
                            <span class="font-medium">{{ $cartSummary['items_count'] }} item</span>
                        </div>

                        @if($cartSummary['total_discount'] > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Harga Asli</span>
                            <span class="font-medium line-through text-gray-400">{{ $cartSummary['formatted_original_total'] }}</span>
                        </div>
                        
                        <div class="flex justify-between text-sm">
                            <span class="text-green-600">Diskon</span>
                            <span class="font-medium text-green-600">-{{ $cartSummary['formatted_total_discount'] }}</span>
                        </div>
                        @endif

                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium">{{ $cartSummary['formatted_subtotal'] }}</span>
                        </div>

                        @if($cartSummary['total_discount'] > 0)
                        <div class="bg-green-50 p-2 rounded text-center">
                            <span class="text-sm text-green-600 font-medium">
                                🎉 Anda hemat {{ $cartSummary['formatted_total_discount'] }}!
                            </span>
                        </div>
                        @endif

                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex justify-between text-base font-medium">
                                <span class="text-gray-900">Total</span>
                                <span class="text-gray-900">{{ $cartSummary['formatted_subtotal'] }}</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Belum termasuk ongkos kirim</p>
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-200">
                        <button wire:click="proceedToCheckout"
                            class="w-full bg-green-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-green-700 transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                            Lanjut ke Checkout
                        </button>

                        <button wire:click="continueShopping"
                            class="w-full mt-3 bg-gray-100 text-gray-700 py-3 px-4 rounded-lg font-medium hover:bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            Lanjut Belanja
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Empty Cart -->
        <div class="text-center py-16">
            <div class="max-w-md mx-auto">
                <x-icons.shopping-cart class="w-24 h-24 mx-auto text-gray-300 mb-6" />
                <h2 class="text-2xl font-medium text-gray-900 mb-2">Keranjang Kosong</h2>
                <p class="text-gray-600 mb-8">Belum ada produk yang ditambahkan ke keranjang Anda.</p>
                <button wire:click="continueShopping"
                    class="bg-green-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-green-700 transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    Mulai Belanja
                </button>
            </div>
        </div>
        @endif
    </div>
</div>