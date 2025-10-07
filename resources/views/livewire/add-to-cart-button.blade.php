<div class="add-to-cart-component">
    @if($showQuantityInput)
        <!-- Quantity Input Section -->
        <div class="flex items-center gap-2 mb-3">
            <label class="text-sm font-medium text-gray-700">Jumlah:</label>
            <div class="flex items-center border border-gray-300 rounded-lg">
                <button 
                    type="button" 
                    wire:click="decrementQuantity" 
                    class="px-3 py-1 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-l-lg"
                    {{ $quantity <= 1 ? 'disabled' : '' }}
                >
                    <x-icons.minus class="w-4 h-4" />
                </button>
                
                <input 
                    type="number" 
                    wire:model.live="quantity" 
                    min="1" 
                    max="99" 
                    class="w-16 px-2 py-1 text-center border-0 focus:ring-0 focus:outline-none"
                >
                
                <button 
                    type="button" 
                    wire:click="incrementQuantity" 
                    class="px-3 py-1 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-r-lg"
                    {{ $quantity >= 99 ? 'disabled' : '' }}
                >
                    <x-icons.plus class="w-4 h-4" />
                </button>
            </div>
        </div>
        
        @error('quantity')
            <div class="text-red-500 text-sm mb-2">{{ $message }}</div>
        @enderror
    @endif

    <!-- Add to Cart / Prescription CTA Button -->
    @php
        // Ambil produk dari komponen (livewire akan menyediakan lewat parent)
        // Pada kasus ini, AddToCartButton hanya tahu productId, jadi kondisi UI
        // akan ditentukan di komponen parent. Untuk fallback, tetap panggil addToCart
        $requiresPrescription = false;
        try {
            $p = \App\Models\Product::find($productId);
            $requiresPrescription = $p ? (bool)$p->requires_prescription : false;
        } catch (\Throwable $e) {
            $requiresPrescription = false;
        }
        $isPelanggan = auth()->check() && auth()->user()->hasRole('pelanggan');
    @endphp

    @if($requiresPrescription && $isPelanggan)
        <a href="{{ route('customer.prescriptions.create') }}" class="btn btn-warning w-full">
            <x-icons.clipboard-check class="w-4 h-4 mr-2" />
            Pesan via Resep Dokter
        </a>
    @else
        <button 
            type="button" 
            wire:click="addToCart" 
            class="{{ $buttonClass }} {{ $isLoading ? 'loading' : '' }}"
            {{ $isLoading ? 'disabled' : '' }}
        >
            @if($isLoading)
                <span class="loading loading-spinner loading-sm"></span>
                Menambahkan...
            @else
                <x-icons.shopping-cart class="w-4 h-4 mr-2" />
                {{ $buttonText }}
                @if(!$showQuantityInput && $quantity > 1)
                    ({{ $quantity }})
                @endif
            @endif
        </button>
    @endif
</div>