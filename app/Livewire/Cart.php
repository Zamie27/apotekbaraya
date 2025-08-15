<?php

namespace App\Livewire;

use App\Services\CartService;
use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.user')]
class Cart extends Component
{
    #[Title('Keranjang Belanja - Apotek Baraya')]

    public $cart;
    public $cartSummary;

    protected $cartService;

    protected $listeners = ['execute-action' => 'executeAction'];

    public function boot(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        $this->cart = $this->cartService->getCart();
        $this->cartSummary = $this->cartService->getCartSummary();
    }

    public function updateQuantity($cartItemId, $quantity)
    {
        $result = $this->cartService->updateQuantity($cartItemId, $quantity);

        if ($result['success']) {
            $this->loadCart();
            $this->dispatch('cart-updated', count: $result['cart_count']);
            $this->dispatch('show-toast', 'success', $result['message'], 3000);
        } else {
            $this->dispatch('show-toast', 'error', $result['message'], 5000);
        }
    }

    /**
     * Execute action from confirmation modal
     *
     * @param string $method Method name to execute
     * @param array $params Parameters for the method
     */
    public function executeAction($method, $params = [])
    {
        if ($method && method_exists($this, $method)) {
            if (!empty($params)) {
                // Extract parameters and call method
                $this->$method(...array_values($params));
            } else {
                $this->$method();
            }
        }
    }

    public function increaseQuantity($cartItemId)
    {
        $cartItem = $this->cart->cartItems->firstWhere('cart_item_id', $cartItemId);
        if ($cartItem) {
            $this->updateQuantity($cartItemId, $cartItem->quantity + 1);
        }
    }

    public function decreaseQuantity($cartItemId)
    {
        $cartItem = $this->cart->cartItems->firstWhere('cart_item_id', $cartItemId);
        if ($cartItem) {
            $this->updateQuantity($cartItemId, $cartItem->quantity - 1);
        }
    }

    public function removeItem($cartItemId)
    {
        // Show confirmation modal
        $this->dispatch('show-confirmation', [
            'title' => 'Hapus Item',
            'message' => 'Apakah Anda yakin ingin menghapus item ini dari keranjang?',
            'confirmText' => 'Ya, Hapus',
            'cancelText' => 'Batal',
            'confirmButtonClass' => 'btn-error',
            'actionMethod' => 'confirmRemoveItem',
            'actionParams' => ['cartItemId' => $cartItemId]
        ]);
    }

    public function confirmRemoveItem($cartItemId)
    {
        $result = $this->cartService->removeFromCart($cartItemId);

        if ($result['success']) {
            $this->loadCart();
            $this->dispatch('cart-updated', count: $result['cart_count']);
            $this->dispatch('show-toast', 'success', $result['message'], 4000);
        } else {
            $this->dispatch('show-toast', 'error', $result['message'], 5000);
        }
        
        // Hide confirmation modal after action is completed
        $this->dispatch('hide-confirmation');
    }

    public function clearCart()
    {
        // Show confirmation modal
        $this->dispatch('show-confirmation', [
            'title' => 'Kosongkan Keranjang',
            'message' => 'Apakah Anda yakin ingin mengosongkan seluruh keranjang belanja? Tindakan ini tidak dapat dibatalkan.',
            'confirmText' => 'Ya, Kosongkan',
            'cancelText' => 'Batal',
            'confirmButtonClass' => 'btn-error',
            'actionMethod' => 'confirmClearCart',
            'actionParams' => []
        ]);
    }

    public function confirmClearCart()
    {
        $result = $this->cartService->clearCart();

        if ($result['success']) {
            $this->loadCart();
            $this->dispatch('cart-updated', count: 0);
            $this->dispatch('show-toast', 'success', $result['message'], 4000);
        } else {
            $this->dispatch('show-toast', 'error', $result['message'], 5000);
        }
        
        // Hide confirmation modal after action is completed
        $this->dispatch('hide-confirmation');
    }

    public function proceedToCheckout()
    {
        $validation = $this->cartService->validateCart();

        if (!$validation['valid']) {
            $this->dispatch('show-toast', 'error', $validation['message'], 5000);

            if (isset($validation['errors'])) {
                foreach ($validation['errors'] as $error) {
                    $this->dispatch('show-toast', 'error', $error, 5000);
                }
            }
            return;
        }

        // Redirect to checkout page
        return redirect()->route('checkout');
    }

    /**
     * Redirect user to dashboard to continue shopping
     */
    public function continueShopping()
    {
        return redirect()->route('home');
    }

    public function render()
    {
        return view('livewire.cart');
    }
}
