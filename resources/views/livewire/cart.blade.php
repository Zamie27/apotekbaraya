<div class="min-h-screen bg-gray-50">
    <!-- Confirmation Modal -->
    @livewire('confirmation-modal')

    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-8 py-4 sm:py-8">
        @if($cart && !$cart->isEmpty())
        <div class="lg:grid lg:grid-cols-12 lg:gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-8 mb-6 lg:mb-0">
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="px-3 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-base sm:text-lg font-medium text-gray-900">Item Keranjang ({{ $cartSummary['items_count'] }})</h2>
                            <button wire:click="clearCart"
                                class="text-xs sm:text-sm text-red-600 hover:text-red-800 transition-colors">
                                Kosongkan Keranjang
                            </button>
                        </div>
                    </div>



                    <div class="divide-y divide-gray-200">
                        @foreach($cart->cartItems as $item)
                        <div class="p-3 sm:p-6" wire:key="cart-item-{{ $item->cart_item_id }}">
                            <div class="flex items-start space-x-3 sm:space-x-4">
                                <!-- Product Image -->
                                <div class="flex-shrink-0">
                                    @if($item->product->images->isNotEmpty())
                                    <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}"
                                        alt="{{ $item->product->name }}"
                                        class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded-lg">
                                    @else
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    @endif
                                </div>

                                <!-- Product Details -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="text-xs sm:text-sm font-medium text-gray-900 line-clamp-2">{{ $item->product->name }}</h3>
                                            <p class="text-xs sm:text-sm text-gray-500 mt-1">{{ $item->product->category->name }}</p>
                                            
                                            {{-- Price Display with Discount Info --}}
                                            <div class="flex items-center gap-1 sm:gap-2 mt-1">
                                                @if($item->product->is_on_sale)
                                                    {{-- Show discount badge --}}
                                                    <span class="badge badge-error badge-xs">-{{ $item->product->discount_percentage }}%</span>
                                                    {{-- Original price (crossed out) --}}
                                                    <span class="text-xs text-gray-400 line-through">{{ $item->product->formatted_price }}</span>
                                                    {{-- Discounted price --}}
                                                    <span class="text-xs sm:text-sm font-medium text-green-600">{{ $item->product->formatted_discount_price }}</span>
                                                @else
                                                    {{-- Regular price --}}
                                                    <span class="text-xs sm:text-sm font-medium text-green-600">{{ $item->formatted_price }}</span>
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
                                            class="ml-2 sm:ml-4 text-gray-400 hover:text-red-500 transition-colors">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Quantity Controls -->
                    <div class="flex items-center mt-3 sm:mt-4 space-x-2 sm:space-x-3">
                        <span class="text-xs sm:text-sm text-gray-700">Jumlah:</span>
                        <div class="flex items-center border border-gray-300 rounded-lg">
                            <button wire:click="decreaseQuantity({{ $item->cart_item_id }})"
                                class="p-1.5 sm:p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-50 transition-colors">
                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </button>
                            <span class="px-2 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-medium text-gray-900 border-x border-gray-300">{{ $item->quantity }}</span>
                            <button wire:click="increaseQuantity({{ $item->cart_item_id }})"
                                class="p-1.5 sm:p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-50 transition-colors"
                                @if($item->quantity >= $item->product->stock) disabled @endif>
                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </button>
                                        </div>
                                        <span class="text-xs sm:text-sm text-gray-500">Stok: {{ $item->product->stock }}</span>
                                    </div>

                                    <!-- Subtotal with Discount Info -->
                                    <div class="mt-2 sm:mt-3">
                                        @if($item->product->is_on_sale)
                                            {{-- Show original subtotal (crossed out) and discounted subtotal --}}
                                            <div class="flex items-center gap-1 sm:gap-2">
                                                <span class="text-xs text-gray-400 line-through">{{ $item->formatted_original_subtotal }}</span>
                                                <span class="text-xs sm:text-sm font-medium text-green-600">{{ $item->formatted_subtotal }}</span>
                                            </div>
                                            <div class="text-xs text-green-600 mt-1">
                                                Hemat: {{ $item->formatted_discount_amount }}
                                            </div>
                                        @else
                                            <span class="text-xs sm:text-sm font-medium text-gray-900">Subtotal: {{ $item->formatted_subtotal }}</span>
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
            <div class="lg:col-span-4 mt-6 sm:mt-8 lg:mt-0">
                <div class="bg-white rounded-lg shadow-sm sticky top-20">
                    <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-200">
                        <h2 class="text-base sm:text-lg font-medium text-gray-900">Ringkasan Pesanan</h2>
                    </div>

                    <div class="px-4 sm:px-6 py-3 sm:py-4 space-y-3 sm:space-y-4">
                        @php
                            // Get summary for all items in cart
                            $summary = $this->getCartSummary();
                        @endphp

                        <div class="flex justify-between text-xs sm:text-sm">
                            <span class="text-gray-600">Total Item</span>
                            <span class="font-medium">{{ $summary['count'] }} item</span>
                        </div>

                        @if($summary['total_discount'] > 0)
                        <div class="flex justify-between text-xs sm:text-sm">
                            <span class="text-gray-600">Harga Asli</span>
                            <span class="font-medium line-through text-gray-400">Rp {{ number_format($summary['subtotal'] + $summary['total_discount'], 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="flex justify-between text-xs sm:text-sm">
                            <span class="text-green-600">Diskon</span>
                            <span class="font-medium text-green-600">-{{ $summary['formatted_total_discount'] }}</span>
                        </div>
                        @endif

                        <div class="flex justify-between text-xs sm:text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium">{{ $summary['formatted_subtotal'] }}</span>
                        </div>

                        @if($summary['total_discount'] > 0)
                        <div class="bg-green-50 p-2 rounded text-center">
                            <span class="text-xs sm:text-sm text-green-600 font-medium">
                                🎉 Anda hemat {{ $summary['formatted_total_discount'] }}!
                            </span>
                        </div>
                        @endif

                        <div class="border-t border-gray-200 pt-3 sm:pt-4">
                            <div class="flex justify-between text-sm sm:text-base font-medium">
                                <span class="text-gray-900">Total</span>
                                <span class="text-gray-900">{{ $summary['formatted_subtotal'] }}</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Belum termasuk ongkos kirim</p>
                        </div>
                    </div>

                    <div class="px-4 sm:px-6 py-3 sm:py-4 border-t border-gray-200">
                        <button wire:click="proceedToCheckout"
                            class="w-full bg-green-600 text-white py-2.5 sm:py-3 px-3 sm:px-4 rounded-lg text-sm sm:text-base font-medium hover:bg-green-700 transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                            Lanjut ke Checkout
                        </button>

                        <button wire:click="continueShopping"
                            class="w-full mt-2 sm:mt-3 bg-gray-100 text-gray-700 py-2.5 sm:py-3 px-3 sm:px-4 rounded-lg text-sm sm:text-base font-medium hover:bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            Lanjut Belanja
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Empty Cart -->
        <div class="text-center py-12 sm:py-16 px-4">
            <div class="max-w-md mx-auto">
                <x-icons.shopping-cart class="w-16 h-16 sm:w-24 sm:h-24 mx-auto text-gray-300 mb-4 sm:mb-6" />
                <h2 class="text-xl sm:text-2xl font-medium text-gray-900 mb-2">Keranjang Kosong</h2>
                <p class="text-sm sm:text-base text-gray-600 mb-6 sm:mb-8">Belum ada produk yang ditambahkan ke keranjang Anda.</p>
                <button wire:click="continueShopping"
                    class="bg-green-600 text-white py-2.5 sm:py-3 px-4 sm:px-6 rounded-lg text-sm sm:text-base font-medium hover:bg-green-700 transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    Mulai Belanja
                </button>
            </div>
        </div>
        @endif
    </div>
</div>

@script
<script>
    // Update cart counter di navbar
    function updateCartCounter() {
        fetch('/api/cart/count')
            .then(response => response.json())
            .then(data => {
                const cartCounters = document.querySelectorAll('.cart-counter');
                cartCounters.forEach(counter => {
                    counter.textContent = data.count;
                    counter.style.display = data.count > 0 ? 'inline' : 'none';
                });
            })
            .catch(error => console.error('Error updating cart counter:', error));
    }

    // Initialize saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        updateCartCounter();
    });

    // Re-initialize setelah Livewire update
    document.addEventListener('livewire:navigated', function() {
        updateCartCounter();
    });
    
    // Listen untuk custom events dari Livewire components
    window.addEventListener('cart-updated', updateCartCounter);
</script>
@endscript