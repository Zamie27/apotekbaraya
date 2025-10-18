<x-layouts.apoteker title="Buat Pesanan dari Resep">

    <style>
        /* Product item styles */
        .product-item {
            transition: all 0.3s ease;
            cursor: default;
        }

        .product-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Quantity controls styles */
        .quantity-controls {
            min-width: 80px;
        }

        .quantity-input:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background-color: #f9fafb;
        }

        .quantity-input:enabled {
            border-color: #3b82f6;
            background-color: white;
        }

        /* Input validation styles */
        .border-red-500 {
            border-color: #ef4444 !important;
            background-color: #fef2f2;
        }

        .input-error {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
        }

        .input-warning {
            border-color: #f59e0b !important;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1) !important;
        }

        .product-checkbox:checked+.product-content {
            background-color: rgba(59, 130, 246, 0.05);
        }

        /* Button styles */
        .btn-disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Loading animation */
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .loading-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .btn-loading {
            animation: pulse 1.5s ease-in-out infinite;
        }

        /* Modal styles */
        .modal-backdrop {
            backdrop-filter: blur(4px);
        }

        /* Ring animation for selected products */
        .ring-2 {
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
        }

        /* Image hover effect */
        .product-item img:hover {
            transform: scale(1.05);
        }

        /* Success modal animation */
        .success-modal {
            animation: fadeInScale 0.3s ease-out;
        }

        @keyframes fadeInScale {
            0% {
                opacity: 0;
                transform: scale(0.9);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Buat Pesanan dari Resep</h1>
                    <p class="text-gray-600">Buat pesanan berdasarkan resep yang telah dikonfirmasi</p>
                </div>

                <a href="{{ route('apoteker.prescriptions.detail', $prescription) }}" class="btn btn-outline">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Detail Resep
                </a>
            </div>

            <!-- Error Message -->
            @if(session('error'))
            <div class="alert alert-error mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Prescription Info -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Informasi Resep</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Nomor Resep</label>
                                <p class="text-lg font-semibold text-gray-900">{{ $prescription->prescription_number }}</p>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-600">Nama Pasien</label>
                                <p class="text-gray-900">{{ $prescription->patient_name }}</p>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-600">Nama Dokter</label>
                                <p class="text-gray-900">{{ $prescription->doctor_name }}</p>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-600">Pelanggan</label>
                                <p class="text-gray-900">{{ $prescription->user->name }}</p>
                                <p class="text-sm text-gray-600">{{ $prescription->user->email }}</p>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-600">Status</label>
                                <span class="badge badge-success">{{ ucfirst($prescription->status) }}</span>
                            </div>

                            <div>
                                <label class="text-sm font-medium text-gray-600">Metode Pengiriman</label>
                                <span class="badge {{ $prescription->delivery_method === 'delivery' ? 'badge-info' : 'badge-success' }}">
                                    {{ $prescription->delivery_method === 'delivery' ? 'Kirim ke Alamat' : 'Ambil di Toko' }}
                                </span>
                            </div>

                            @if($prescription->notes)
                            <div>
                                <label class="text-sm font-medium text-gray-600">Catatan Pelanggan</label>
                                <p class="text-gray-900">{{ $prescription->notes }}</p>
                            </div>
                            @endif
                        </div>

                        <!-- Prescription Image -->
                        <div class="mt-6">
                            <label class="text-sm font-medium text-gray-600">Gambar Resep</label>
                            <div class="mt-2">
                                <img src="{{ Storage::url($prescription->prescription_image) }}"
                                    alt="Resep {{ $prescription->prescription_number }}"
                                    class="w-full h-auto rounded-lg border border-gray-200 cursor-pointer"
                                    onclick="openImageModal('{{ Storage::url($prescription->prescription_image) }}')">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Creation Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Buat Pesanan</h3>

                        <form action="{{ route('apoteker.prescriptions.store-order', $prescription) }}" method="POST" id="orderForm">
                            @csrf

                            <!-- Product Selection -->
                            <div class="mb-6">
                                <label class="text-sm font-medium text-gray-600 mb-2 block">Pilih Produk</label>

                                <!-- Search Products -->
                                <div class="mb-4">
                                    <input type="text"
                                        id="productSearch"
                                        placeholder="Cari produk..."
                                        class="input input-bordered w-full"
                                        onkeyup="filterProducts()">
                                </div>

                                <!-- Products List -->
                                <div class="text-sm text-gray-600 mb-3">
                                    Total {{ count($products) }} produk tersedia
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 max-h-[28rem] overflow-y-auto" id="productsList">
                                    @foreach($products as $index => $product)
                                    <div class="product-item card bg-base-100 shadow hover:shadow-lg transition"
                                        data-product-name="{{ strtolower($product->name) }}"
                                        data-product-id="{{ $product->product_id }}"
                                        data-price="{{ (float)$product->price }}"
                                        data-final-price="{{ (float)$product->final_price }}"
                                        data-unit="{{ $product->unit }}">
                                        <figure class="relative pt-2">
                                            @php($imgUrl = $product->primary_image_url)
                                            <div class="relative w-full aspect-square overflow-hidden">
                                                @if($imgUrl)
                                                <img src="{{ $imgUrl }}"
                                                    alt="{{ $product->name }}"
                                                    class="object-cover w-full h-full cursor-pointer"
                                                    onclick="openImageModal('{{ $imgUrl }}')" />
                                                @else
                                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                                @endif
                                            </div>

                                            <!-- Select Checkbox -->
                                            <div class="absolute top-2 left-2 bg-white/80 rounded px-1 py-1">
                                                <input type="checkbox"
                                                    name="products[{{ $product->product_id }}][selected]"
                                                    value="1"
                                                    id="product_{{ $product->product_id }}"
                                                    class="checkbox checkbox-primary product-checkbox"
                                                    data-product-id="{{ $product->product_id }}">
                                                <input type="hidden" name="products[{{ $product->product_id }}][product_id]" value="{{ $product->product_id }}">
                                            </div>

                                            <!-- Badges -->
                                            <div class="absolute top-2 right-2 space-y-1">
                                                @if($product->requires_prescription)
                                                <span class="badge badge-warning badge-xs">Resep</span>
                                                @endif
                                                @if($product->is_on_sale)
                                                <span class="badge badge-error badge-xs">-{{ $product->discount_percentage }}%</span>
                                                @endif
                                            </div>
                                        </figure>
                                        <div class="card-body p-3">
                                            <h4 class="card-title text-sm font-bold line-clamp-2 product-name" title="{{ $product->name }}">{{ $product->name }}</h4>
                                            <!-- Price Display -->
                                            <div class="space-y-0.5">
                                                @if($product->is_on_sale)
                                                <div class="flex items-center gap-2">
                                                    <p class="text-xs text-gray-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                                    <p class="text-sm font-semibold text-success">Rp {{ number_format($product->final_price, 0, ',', '.') }}</p>
                                                </div>
                                                <span class="text-xs text-gray-500">/ {{ $product->unit }}</span>
                                                @else
                                                <div class="flex items-center gap-2">
                                                    <p class="text-sm font-semibold text-gray-700">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                                </div>
                                                <span class="text-xs text-gray-500">/ {{ $product->unit }}</span>
                                                @endif
                                            </div>
                                            <!-- Stock Status -->
                                            <div class="flex items-center justify-between mt-1">
                                                <span class="text-xs {{ $product->is_available ? 'text-success' : 'text-error' }}">{{ $product->is_available ? 'Tersedia' : 'Habis' }}</span>
                                            </div>
                                            <!-- Quantity Controls -->
                                            <div class="mt-2 flex items-center gap-2">
                                                <button type="button" class="btn btn-xs" onclick="changeQuantity({{ $product->product_id }}, -1)" disabled id="dec_btn_{{ $product->product_id }}">-</button>
                                                <input type="number" id="quantity_input_{{ $product->product_id }}" name="products[{{ $product->product_id }}][quantity]" min="1" value="1" disabled class="quantity-input input input-bordered input-xs w-16 text-center" title="Pilih produk terlebih dahulu untuk mengatur jumlah">
                                                <button type="button" class="btn btn-xs" onclick="changeQuantity({{ $product->product_id }}, 1)" disabled id="inc_btn_{{ $product->product_id }}">+</button>
                                                <button type="button" class="btn btn-ghost btn-xs text-error ml-auto" onclick="removeSelection({{ $product->product_id }})">Hapus</button>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <!-- Cart Summary -->
                                <div class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-sm text-gray-700">Dipilih: <span id="selectedCount" class="font-semibold">0</span> produk</p>
                                        <p class="text-xs text-gray-500">Ongkir: {{ $prescription->delivery_method === 'delivery' ? 'Rp 10.000' : 'Rp 0' }}</p>
                                    </div>
                                    <div class="space-y-1">
                                        <div class="flex justify-between text-sm">
                                            <span>Subtotal</span>
                                            <span id="subtotalText">Rp 0</span>
                                        </div>
                                        <div class="flex justify-between text-sm text-success">
                                            <span>Diskon</span>
                                            <span id="discountText">- Rp 0</span>
                                        </div>
                                        <div class="divider my-1"></div>
                                        <div class="flex justify-between font-semibold">
                                            <span>Total</span>
                                            <span id="totalText">Rp 0</span>
                                        </div>
                                        <div class="divider my-1"></div>
                                        <p class="text-sm text-gray-700">Detail Barang Dipilih</p>
                                        <div id="selectedItemsDetails" class="mt-1 space-y-1 text-xs text-gray-700"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Notes -->
                            <div class="mb-6">
                                <label class="text-sm font-medium text-gray-600 mb-2 block">Catatan Pesanan</label>
                                <textarea name="order_notes"
                                    rows="3"
                                    class="textarea textarea-bordered w-full"
                                    placeholder="Catatan tambahan untuk pesanan ini..."></textarea>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end space-x-4">
                                <a href="{{ route('apoteker.prescriptions.detail', $prescription) }}" class="btn btn-outline">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitOrderBtn">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    Buat Pesanan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="modal">
        <div class="modal-box max-w-4xl">
            <h3 class="font-bold text-lg mb-4">Gambar Resep</h3>
            <img id="modalImage" src="" alt="Resep" class="w-full h-auto">
            <div class="modal-action">
                <button class="btn" onclick="closeImageModal()">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-box success-modal">
            <h3 class="font-bold text-lg mb-2">Pesanan Berhasil Dibuat</h3>
            <p class="text-sm text-gray-600">Nomor Pesanan:</p>
            <p id="orderNumber" class="font-mono font-semibold mb-2">-</p>
            <p class="text-sm text-gray-600">Total:</p>
            <p id="orderTotal" class="font-semibold">Rp 0</p>
            <p class="text-xs text-gray-500 mt-3">Anda akan diarahkan ke halaman detail pesanan...</p>
            <div class="modal-action">
                <button class="btn" onclick="document.getElementById('successModal').classList.remove('modal-open')">Tutup</button>
            </div>
        </div>
        <div class="modal-backdrop" onclick="document.getElementById('successModal').classList.remove('modal-open')"></div>





















































































































































































































































    </div>
    <script>
        // Disable console.log in production to reduce noise
        (function() {
            // Use simple boolean check for production environment
            var isProd = {{ app()->environment('production') ? 'true' : 'false' }};
            if (isProd && typeof console !== 'undefined') {
                try {
                    console.log = function() {};
                } catch (e) {}
            }
        })();

        // Toggle product quantity input based on checkbox
        function toggleProductQuantity(productId) {
            const checkbox = document.getElementById(`product_${productId}`);
            const quantityInput = document.getElementById(`quantity_input_${productId}`);
            const decBtn = document.getElementById(`dec_btn_${productId}`);
            const incBtn = document.getElementById(`inc_btn_${productId}`);
            const productItem = checkbox ? checkbox.closest('.product-item') : null;

            if (!checkbox || !quantityInput || !productItem) {
                return;
            }

            if (checkbox.checked) {
                // Enable quantity input
                quantityInput.disabled = false;
                quantityInput.classList.add('border-primary');
                quantityInput.title = 'Masukkan jumlah yang diinginkan';
                if (decBtn) decBtn.disabled = false;
                if (incBtn) incBtn.disabled = false;

                // Add visual feedback to product item
                productItem.classList.add('ring-2', 'ring-primary', 'ring-opacity-50');
            } else {
                // Disable quantity input
                quantityInput.disabled = true;
                quantityInput.classList.remove('border-primary');
                quantityInput.value = 1; // Reset to default
                quantityInput.title = 'Pilih produk terlebih dahulu untuk mengatur jumlah';
                if (decBtn) decBtn.disabled = true;
                if (incBtn) incBtn.disabled = true;

                // Remove visual feedback from product item
                productItem.classList.remove('ring-2', 'ring-primary', 'ring-opacity-50');
            }

            updateSubmitButton();
            updateCartSummary();
        }

        // Update submit button text and state
        function updateSubmitButton() {
            const submitBtn = document.getElementById('submitOrderBtn');

            if (!submitBtn) {
                return;
            }

            // Count based on actual checked checkboxes, not the array
            const checkedCheckboxes = document.querySelectorAll('.product-checkbox:checked');
            const count = checkedCheckboxes.length;
            const selectedCountEl = document.getElementById('selectedCount');
            if (selectedCountEl) selectedCountEl.textContent = count;

            if (count > 0) {
                submitBtn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            Buat Pesanan (${count} produk)
        `;
                submitBtn.disabled = false;
                submitBtn.classList.remove('btn-disabled', 'opacity-50');
                submitBtn.classList.add('btn-primary');
            } else {
                submitBtn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            Pilih Produk Terlebih Dahulu
        `;
                submitBtn.disabled = true;
                submitBtn.classList.add('btn-disabled', 'opacity-50');
                submitBtn.classList.remove('btn-primary');
            }
        }

        // Filter products
        function filterProducts() {
            const searchTerm = document.getElementById('productSearch').value.toLowerCase();
            const productItems = document.querySelectorAll('.product-item');

            productItems.forEach(item => {
                const productName = item.getAttribute('data-product-name');
                const isMatch = productName.includes(searchTerm);
                item.classList.toggle('hidden', !isMatch);
            });
        }

        // Image modal functions
        function openImageModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModal').classList.add('modal-open');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.remove('modal-open');
        }

        // Form submission with validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('orderForm');
            const submitBtn = document.getElementById('submitOrderBtn');
            const deliveryMethod = '{{ $prescription->delivery_method }}';

            // Initialize form state

            // Initialize all quantity inputs to disabled state
            const allQuantityInputs = document.querySelectorAll('.quantity-input');
            allQuantityInputs.forEach((input) => {
                input.disabled = true;
                input.classList.remove('border-primary');
                input.value = 1;
                input.title = 'Pilih produk terlebih dahulu untuk mengatur jumlah';
            });

            // Initial state
            updateSubmitButton();
            updateCartSummary();

    // Use event delegation for checkbox changes
    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('product-checkbox')) {
            const productId = e.target.getAttribute('data-product-id');
            toggleProductQuantity(productId);
        }
    });

    // Also listen to click to ensure immediate update in some browsers/UI libs
    document.addEventListener('click', function(e){
        if (e.target && e.target.classList.contains('product-checkbox')) {
            const productId = e.target.getAttribute('data-product-id');
            toggleProductQuantity(productId);
        }
    });

            // Listen for quantity input changes to update summary
            document.addEventListener('input', function(e) {
                if (e.target && e.target.classList.contains('quantity-input')) {
                    const val = parseInt(e.target.value || '1');
                    if (val < 1) e.target.value = 1;
                    updateCartSummary();
                }
            });

            // Form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validate at least one product is selected
                const selectedCheckboxes = document.querySelectorAll('.product-checkbox:checked');
                if (selectedCheckboxes.length === 0) {
                    alert('⚠️ Pilih minimal satu produk untuk membuat pesanan.');
                    return;
                }

                // Validate quantities
                let isValid = true;
                let errorMessages = [];

                selectedCheckboxes.forEach(checkbox => {
                    const productId = checkbox.getAttribute('data-product-id');
                    const quantityInput = document.getElementById(`quantity_input_${productId}`);
                    const quantity = parseInt(quantityInput.value);
                    const productName = quantityInput.closest('.product-item').querySelector('.product-name').textContent;

                    if (quantity < 1) {
                        isValid = false;
                        errorMessages.push(`${productName}: Jumlah minimal 1`);
                        quantityInput.classList.add('input-error');
                    } else {
                        quantityInput.classList.remove('input-error');
                    }
                });

                if (!isValid) {
                    alert('⚠️ Periksa kembali jumlah produk:\n' + errorMessages.join('\n'));
                    return;
                }

                // Show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
            <span class="loading loading-spinner loading-sm mr-2"></span>
            Memproses Pesanan...
        `;

                // Create custom FormData with only selected products
                const formData = new FormData();

                // Add CSRF token
                const csrfTokenInput = document.querySelector('input[name="_token"]');
                if (csrfTokenInput) {
                    formData.append('_token', csrfTokenInput.value);
                }

                // Add order notes
                const orderNotesInput = document.querySelector('textarea[name="order_notes"]');
                if (orderNotesInput) {
                    formData.append('order_notes', orderNotesInput.value);
                }

                // Add only selected products
                selectedCheckboxes.forEach(checkbox => {
                    const productId = checkbox.getAttribute('data-product-id');
                    const quantityInput = document.getElementById(`quantity_input_${productId}`);

                    if (quantityInput && !quantityInput.disabled) {
                        formData.append(`products[${productId}][selected]`, '1');
                        formData.append(`products[${productId}][quantity]`, quantityInput.value);
                    }
                });

                // Get CSRF token safely
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    alert('❌ Error: CSRF token tidak ditemukan. Silakan refresh halaman.');
                    // Reset button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                Buat Pesanan (${selectedCheckboxes.length} produk)
            `;
                    return;
                }

                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                        }
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        console.log('Response URL:', response.url);
                        
                        // Check if response was redirected (indicates form submission fallback)
                        if (response.redirected) {
                            console.log('Response was redirected, likely successful order creation');
                            // Show success message and redirect
                            alert('✅ Pesanan berhasil dibuat! Mengalihkan ke detail pesanan...');
                            window.location.href = response.url;
                            return;
                        }
                        
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        
                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            // If not JSON, assume successful form submission
                            console.log('Non-JSON response, assuming success');
                            alert('✅ Pesanan berhasil dibuat! Mengalihkan ke halaman detail resep...');
                            window.location.href = "{{ route('apoteker.prescriptions.detail', $prescription) }}";
                            return;
                        }
                        
                        return response.json();
                    })
                    .then(data => {
                        // Only process if data exists (JSON response)
                        if (!data) return;
                        
                        console.log('Response data:', data);
                        
                        if (data.success) {
                            // Show success modal
                            const successModal = document.getElementById('successModal');
                            if (successModal) {
                                successModal.classList.add('modal-open');
                                const orderNumberEl = document.getElementById('orderNumber');
                                const orderTotalEl = document.getElementById('orderTotal');
                                if (orderNumberEl) orderNumberEl.textContent = data.order_number;
                                if (orderTotalEl) orderTotalEl.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.total_price);
                            }

                            // Redirect after 2 seconds to order detail
                            setTimeout(() => {
                                if (data.redirect_url) {
                                    window.location.href = data.redirect_url;
                                } else {
                                    window.location.href = `/apoteker/orders/${data.order_id}`;
                                }
                            }, 2000);
                        } else {
                            console.error('Server returned error:', data);
                            alert('❌ Gagal membuat pesanan: ' + (data.message || 'Terjadi kesalahan'));
                            resetSubmitButton();
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);

                        // More specific error handling
                        if (error.message.includes('HTTP error')) {
                            alert('❌ Server error: ' + error.message);
                        } else if (error.message.includes('JSON') || error.message.includes('Unexpected token')) {
                            // Likely successful but response parsing failed
                            alert('✅ Pesanan mungkin berhasil dibuat. Mengalihkan ke detail resep untuk verifikasi...');
                            setTimeout(() => {
                                window.location.href = "{{ route('apoteker.prescriptions.detail', $prescription) }}";
                            }, 1000);
                            return;
                        } else {
                            alert('❌ Terjadi kesalahan saat memproses pesanan: ' + error.message);
                        }

                        resetSubmitButton();
                    });

                // Helper function to reset submit button
                function resetSubmitButton() {
                    const currentSelected = document.querySelectorAll('.product-checkbox:checked');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Buat Pesanan (${currentSelected.length} produk)
                    `;
                }
            });

            // Add event listeners to quantity inputs for real-time validation
            document.querySelectorAll('input[type="number"]').forEach(input => {
                input.addEventListener('input', function() {
                    const quantity = parseInt(this.value);

                    if (quantity < 1) {
                        this.value = 1;
                    }
                });
            });
        });

        // Quantity change helper
        function changeQuantity(productId, delta) {
            const input = document.getElementById(`quantity_input_${productId}`);
            const checkbox = document.getElementById(`product_${productId}`);
            if (!input || !checkbox) return;
            if (checkbox.checked) {
                const current = parseInt(input.value || '1');
                const next = Math.max(1, current + delta);
                input.value = next;
                updateCartSummary();
            }
        }

        // Remove selection
        function removeSelection(productId) {
            const checkbox = document.getElementById(`product_${productId}`);
            if (!checkbox) return;
            checkbox.checked = false;
            toggleProductQuantity(productId);
            updateCartSummary();
        }

        // Format Rupiah helper
        function formatRupiah(n) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n));
        }

        function getDeliveryFee() {
            const method = '{{ $prescription->delivery_method }}';
            return method === 'delivery' ? 10000 : 0;
        }

        // Update cart summary (subtotal, discount, total)
        function updateCartSummary() {
            const checked = Array.from(document.querySelectorAll('.product-checkbox:checked'));
            let subtotal = 0;
            let discount = 0;
            const details = [];
            checked.forEach(chk => {
                const productId = chk.getAttribute('data-product-id');
                const root = chk.closest('.product-item');
                const qtyInput = document.getElementById(`quantity_input_${productId}`);
                const qty = Math.max(1, parseInt(qtyInput?.value || '1'));
                const price = parseFloat(root?.getAttribute('data-price') || '0');
                const finalPrice = parseFloat(root?.getAttribute('data-final-price') || price);
                const name = root?.querySelector('.product-name')?.textContent?.trim() || `Produk ${productId}`;
                const unit = root?.getAttribute('data-unit') || '';
                subtotal += finalPrice * qty;
                discount += Math.max(0, (price - finalPrice)) * qty;

                // Build detail line: "Nama × qty unit"  ....  "Rp lineTotal"
                const lineTotal = finalPrice * qty;
                const qtyText = `× ${qty}${unit ? ' ' + unit : ''}`;
                details.push(`<div class="flex justify-between"><span>${name} ${qtyText}</span><span>${formatRupiah(lineTotal)}</span></div>`);
            });
            const total = subtotal + getDeliveryFee();
            const subtotalEl = document.getElementById('subtotalText');
            const discountEl = document.getElementById('discountText');
            const totalEl = document.getElementById('totalText');
            const detailsEl = document.getElementById('selectedItemsDetails');
            if (subtotalEl) subtotalEl.textContent = formatRupiah(subtotal);
            if (discountEl) discountEl.textContent = (discount > 0 ? '- ' : '') + formatRupiah(discount);
            if (totalEl) totalEl.textContent = formatRupiah(total);
            if (detailsEl) detailsEl.innerHTML = details.length ? details.join('') : '<span class="text-gray-500">Belum ada produk dipilih</span>';
        }
    </script>
</x-layouts.apoteker>