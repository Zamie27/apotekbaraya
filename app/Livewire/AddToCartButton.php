<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\CartService;
use App\Models\Product;

class AddToCartButton extends Component
{
    public $productId;
    public $quantity = 1;
    public $buttonText = 'TAMBAH KE KERANJANG';
    public $buttonClass = 'btn btn-primary w-full';
    public $showQuantityInput = false;
    public $isLoading = false;

    protected $cartService;

    public function boot(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    protected $rules = [
        'quantity' => 'required|integer|min:1|max:99'
    ];

    protected $messages = [
        'quantity.required' => 'Jumlah harus diisi',
        'quantity.integer' => 'Jumlah harus berupa angka',
        'quantity.min' => 'Jumlah minimal 1',
        'quantity.max' => 'Jumlah maksimal 99'
    ];

    public function mount($productId, $quantity = 1, $buttonText = null, $buttonClass = null, $showQuantityInput = false)
    {
        $this->productId = $productId;
        $this->quantity = $quantity;

        if ($buttonText) {
            $this->buttonText = $buttonText;
        }

        if ($buttonClass) {
            $this->buttonClass = $buttonClass;
        }

        $this->showQuantityInput = $showQuantityInput;
    }

    public function addToCart()
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            $this->dispatch('show-toast', 'error', 'Silakan login terlebih dahulu untuk menambahkan produk ke keranjang.', 5000);
            return redirect()->route('login');
        }

        // Validate quantity
        $this->validate();

        $this->isLoading = true;

        try {
            // Get product
            $product = Product::find($this->productId);

            if (!$product) {
                $this->dispatch('show-toast', 'error', 'Produk tidak ditemukan.', 5000);
                $this->isLoading = false;
                return;
            }

            // Gate: product must be active and available (stock > 0)
            if (!(bool)$product->is_active || !$product->isAvailable()) {
                $this->dispatch('show-toast', 'error', 'Produk tidak tersedia atau nonaktif.', 5000);
                $this->isLoading = false;
                return;
            }

            // Add to cart using CartService
            $result = $this->cartService->addToCart($product->product_id, $this->quantity);

            if ($result['success']) {
                // Dispatch event to update cart counter
                $this->dispatch('cart-updated');

                // Show success toast notification
                $this->dispatch('show-toast', 'success', $result['message'], 4000);

                // Reset quantity if showing quantity input
                if ($this->showQuantityInput) {
                    $this->quantity = 1;
                }
            } else {
                // Show error toast notification
                $this->dispatch('show-toast', 'error', $result['message'], 5000);
            }
        } catch (\Exception $e) {
            // Show error toast notification for exceptions
            $this->dispatch('show-toast', 'error', 'Terjadi kesalahan saat menambahkan produk ke keranjang.', 5000);
        }

        $this->isLoading = false;
    }

    public function incrementQuantity()
    {
        if ($this->quantity < 99) {
            $this->quantity++;
        }
    }

    public function decrementQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function render()
    {
        return view('livewire.add-to-cart-button');
    }
}
