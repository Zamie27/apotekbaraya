<div class="container mx-auto px-4 py-6">
    <!-- Card Container -->
    <div class="card w-full bg-base-100 shadow-xl">
        <div class="flex flex-col md:flex-row">
            <!-- Product Image Section -->
            <div class="w-full md:w-1/4 p-4 flex justify-center">
                <div class="product-image-container">
                    <img src="path/to/your-image.png" alt="Happy Tos Keripik Tortilla" class="w-64 h-64 object-cover" />
                </div>
            </div>

            <!-- Product Description Section -->
            <div class="w-full md:w-1/2 p-6">
                <!-- Product Name and Price -->
                <div class="flex flex-col justify-start items-start">
                    <h2 class="text-3xl font-bold">Happy Tos Keripik Tortilla Hijau</h2>
                    <span class="text-xl font-bold text-black mt-2">Rp 10.000</span>
                </div>

                <!-- Description -->
                <div class="mt-4 text-gray-700">
                    <p>
                        Terbuat dari biji jagung pilihan. Digoreng dengan minyak kelapa sawit bermutu. Dapat dikonsumsi kapan dan di mana saja dalam berbagai suasana.
                    </p>
                    <p class="mt-2">
                        Happy Tos Real Corn Hijau merupakan keripik renyah dan lezat yang terbuat dari biji jagung pilihan. Keripik Happy Tos diolah secara teliti dan digoreng dengan minyak kelapa sawit bermutu untuk menghasilkan keripik yang enak dengan tingkat kerenyahan yang pas.
                    </p>
                </div>
            </div>

            <!-- Cart and Button Section -->
            <div class="w-full md:w-1/4 p-6 flex flex-col items-center justify-between">
                <!-- Total Purchase -->
                <div class="text-center mb-4">
                    <h3 class="text-lg font-semibold">Total Pembelian</h3>
                    <span class="text-xl font-bold">Rp 10.000</span>
                </div>

                <!-- New Container for Cart Icon, Quantity, Add to Cart, and Checkout -->
                <div class="w-full flex flex-col space-y-4">
                    <!-- Cart Icon and Quantity -->
                    <div class="flex items-center space-x-2">
                        <i class="text-xl">🛒</i>
                        <span>Jumlah Pembelian:</span>
                        <!-- Quantity control with buttons -->
                        <div class="flex items-center space-x-2">
                            <button class="btn btn-sm bg-white border border-gray-500 text-gray-500 rounded-lg">-</button>
                            <span id="quantity" class="text-lg font-semibold">1</span>
                            <button class="btn btn-sm bg-white border border-gray-500 text-gray-500 rounded-lg">+</button>
                        </div>
                    </div>

                    <!-- Add to Cart Button and Checkout Button (Side by Side) -->
                    <div class="flex w-full space-x-4 mb-4">
                        <!-- Add to Cart Button with custom width -->
                        <button class="btn btn-success text-white rounded-lg w-1/2">Keranjang</button>

                        <!-- Checkout Button with custom width -->
                        <button class="btn btn-success text-white rounded-lg w-1/2">Checkout</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        feather.replace(); // Initialize Feather Icons

        // Handling the quantity change
        document.querySelectorAll('.btn-sm').forEach(button => {
            button.addEventListener('click', function() {
                let quantityDisplay = document.getElementById('quantity');
                let currentQuantity = parseInt(quantityDisplay.textContent);
                
                if (this.textContent === '+') {
                    quantityDisplay.textContent = currentQuantity + 1;
                } else if (this.textContent === '-' && currentQuantity > 1) {
                    quantityDisplay.textContent = currentQuantity - 1;
                }
            });
        });
    </script>
@endpush
