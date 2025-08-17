<?php

namespace App\Livewire;

use App\Models\Product;
use App\Services\CartService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('components.layouts.user')]
class Deskripsi extends Component
{
    public $productId;
    public $product;
    
    #[Validate('required|integer|min:1|max:999')]
    public $quantity = 1;
    
    protected $cartService;
    
    public function boot(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    
    /**
     * Sanitize quantity input - only numbers, max 999
     */
    public function updatedQuantity($value)
    {
        // Laravel validation will handle input security
        $this->quantity = (int) $value;
        $this->quantity = max(1, min($this->quantity, $this->product->stock));
    }
    
    /**
     * Mount component with product ID parameter
     */
    public function mount($id = null)
    {
        // Debug: Log the received ID
        \Log::info('Deskripsi mount called with ID: ' . ($id ?? 'null'));
        
        // Check if product ID is provided
        if (!$id) {
            abort(404, 'ID produk tidak ditemukan');
        }
        
        $this->productId = $id;
        
        // Load product with relationships
        $this->product = Product::with(['category', 'images'])
            ->find($this->productId);
            
        // Redirect to 404 if product not found
        if (!$this->product) {
            abort(404, 'Produk tidak ditemukan');
        }
    }
    
    /**
     * Increase quantity
     */
    public function increaseQuantity()
    {
        if ($this->quantity < $this->product->stock) {
            $this->quantity++;
        }
    }
    
    /**
     * Decrease quantity
     */
    public function decreaseQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }
    
    /**
     * Add product to cart with selected quantity
     */
    public function addToCart()
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            $this->dispatch('show-toast', 'error', 'Silakan login terlebih dahulu untuk menambahkan produk ke keranjang.', 5000);
            return redirect()->route('login');
        }

        // Validate quantity
        $this->validate();

        try {
            // Add to cart using CartService
            $result = $this->cartService->addToCart($this->product->product_id, $this->quantity);

            if ($result['success']) {
                // Dispatch event to update cart counter
                $this->dispatch('cart-updated');

                // Show success toast notification
                $this->dispatch('show-toast', 'success', $result['message'], 4000);

                // Reset quantity to 1 after successful add
                $this->quantity = 1;
            } else {
                // Show error toast notification
                $this->dispatch('show-toast', 'error', $result['message'], 5000);
            }
        } catch (\Exception $e) {
            // Show error toast notification for exceptions
            $this->dispatch('show-toast', 'error', 'Terjadi kesalahan saat menambahkan produk ke keranjang.', 5000);
        }
    }

    public function render()
    {
        return view('livewire.deskripsi', [
            'product' => $this->product
        ]);
    }
}
