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
    public $selectedItems = []; // Array untuk menyimpan item yang dipilih
    public $selectAll = false; // Checkbox select all

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

        // Jangan reset selected items setiap kali load cart
        // Hanya reset jika selectedItems belum diinisialisasi
        if (!is_array($this->selectedItems)) {
            $this->selectedItems = [];
        }
        
        // Pastikan selectedItems hanya berisi item yang masih ada di cart
        if (!empty($this->selectedItems) && $this->cart && $this->cart->cartItems) {
            $existingItemIds = $this->cart->cartItems->pluck('cart_item_id')->toArray();
            $this->selectedItems = array_intersect($this->selectedItems, $existingItemIds);
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Select semua item - ambil semua cart_item_id yang ada dan cast ke string
            $this->selectedItems = $this->cart->cartItems->pluck('cart_item_id')->map(function($id) {
                return (string) $id;
            })->toArray();
        } else {
            // Unselect semua item
            $this->selectedItems = [];
        }
        

    }

    public function updatedSelectedItems($value)
    {
        // Pastikan selectedItems adalah array
        if (!is_array($this->selectedItems)) {
            $this->selectedItems = [];
        }
        
        // Update selectAll berdasarkan selectedItems
        $totalItems = $this->cart->cartItems->count();
        $selectedCount = count($this->selectedItems);
        
        // Set selectAll ke true hanya jika semua item terpilih
        // Set selectAll ke false jika tidak semua item terpilih
        $this->selectAll = $totalItems > 0 && $selectedCount === $totalItems;
        

    }
    
    /**
     * Update selectAll status saat component di-render
     */
    public function updateSelectAllStatus()
    {
        if ($this->cart && $this->cart->cartItems) {
            $totalItems = $this->cart->cartItems->count();
            $selectedCount = count($this->selectedItems);
            $this->selectAll = $totalItems > 0 && $selectedCount === $totalItems;
        }
    }

    public function getSelectedItemsSummary()
    {
        // Jika tidak ada item yang dipilih, return summary kosong
        if (empty($this->selectedItems)) {
            return [
                'count' => 0,
                'subtotal' => 0,
                'formatted_subtotal' => 'Rp 0',
                'total_discount' => 0,
                'formatted_total_discount' => 'Rp 0'
            ];
        }

        $selectedCartItems = $this->cart->cartItems->whereIn('cart_item_id', $this->selectedItems);

        $subtotal = $selectedCartItems->sum(function ($item) {
            return $item->quantity * $item->product->final_price;
        });

        $originalTotal = $selectedCartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        $totalDiscount = $originalTotal - $subtotal;

        return [
            'count' => $selectedCartItems->sum('quantity'),
            'subtotal' => $subtotal,
            'formatted_subtotal' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
            'total_discount' => $totalDiscount,
            'formatted_total_discount' => 'Rp ' . number_format($totalDiscount, 0, ',', '.')
        ];
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
            $newQuantity = $cartItem->quantity - 1;

            // Jika quantity akan menjadi 0, tampilkan konfirmasi
            if ($newQuantity <= 0) {
                $this->dispatch('show-confirmation', [
                    'title' => 'Hapus Item dari Keranjang',
                    'message' => 'Quantity akan menjadi 0. Apakah Anda yakin ingin menghapus "' . $cartItem->product->name . '" dari keranjang?',
                    'confirmText' => 'Ya, Hapus',
                    'cancelText' => 'Batal',
                    'confirmButtonClass' => 'btn-error',
                    'actionMethod' => 'confirmDecreaseToZero',
                    'actionParams' => ['cartItemId' => $cartItemId]
                ]);
            } else {
                $this->updateQuantity($cartItemId, $newQuantity);
            }
        }
    }

    public function confirmDecreaseToZero($cartItemId)
    {
        $result = $this->cartService->updateQuantity($cartItemId, 0);

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
        // Jika ada item yang dipilih, checkout hanya item tersebut
        if (!empty($this->selectedItems)) {
            return $this->checkoutSelectedItems();
        }

        // Jika tidak ada yang dipilih, checkout semua item
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

    public function checkoutSelectedItems()
    {
        if (empty($this->selectedItems)) {
            $this->dispatch('show-toast', 'error', 'Pilih minimal satu item untuk checkout', 3000);
            return;
        }

        // Validasi item yang dipilih
        $selectedCartItems = $this->cart->cartItems->whereIn('cart_item_id', $this->selectedItems);

        $errors = [];
        foreach ($selectedCartItems as $item) {
            if ($item->quantity > $item->product->stock) {
                $errors[] = "Stok {$item->product->name} tidak mencukupi. Tersedia: {$item->product->stock}";
            }
        }

        if (!empty($errors)) {
            $this->dispatch('show-toast', 'error', 'Ada masalah dengan item yang dipilih: ' . implode(', ', $errors), 5000);
            return;
        }

        // Simpan selected items ke session untuk checkout
        session(['checkout_items' => $this->selectedItems]);

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
        // Update status selectAll sebelum render
        $this->updateSelectAllStatus();
        
        return view('livewire.cart');
    }
}
