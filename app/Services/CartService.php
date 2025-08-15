<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartService
{
    /**
     * Add product to cart
     */
    public function addToCart(int $productId, int $quantity = 1, ?int $userId = null): array
    {
        try {
            $userId = $userId ?? Auth::id();
            
            if (!$userId) {
                return [
                    'success' => false,
                    'message' => 'User tidak terautentikasi'
                ];
            }

            $product = Product::find($productId);
            if (!$product) {
                return [
                    'success' => false,
                    'message' => 'Produk tidak ditemukan'
                ];
            }

            if ($product->stock < $quantity) {
                return [
                    'success' => false,
                    'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $product->stock
                ];
            }

            DB::beginTransaction();

            $cart = Cart::getOrCreateForUser($userId);
            
            $existingCartItem = CartItem::where('cart_id', $cart->cart_id)
                ->where('product_id', $productId)
                ->first();

            if ($existingCartItem) {
                $newQuantity = $existingCartItem->quantity + $quantity;
                
                if ($newQuantity > $product->stock) {
                    DB::rollBack();
                    return [
                        'success' => false,
                        'message' => 'Total quantity melebihi stok. Stok tersedia: ' . $product->stock
                    ];
                }

                $existingCartItem->update([
                    'quantity' => $newQuantity,
                    'price' => $product->price // Update price in case it changed
                ]);
            } else {
                CartItem::create([
                    'cart_id' => $cart->cart_id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $product->price
                ]);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang',
                'cart_count' => $this->getCartItemsCount($userId)
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity(int $cartItemId, int $quantity, ?int $userId = null): array
    {
        try {
            $userId = $userId ?? Auth::id();
            
            $cartItem = CartItem::whereHas('cart', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->find($cartItemId);

            if (!$cartItem) {
                return [
                    'success' => false,
                    'message' => 'Item keranjang tidak ditemukan'
                ];
            }

            if ($quantity <= 0) {
                $cartItem->delete();
                return [
                    'success' => true,
                    'message' => 'Item berhasil dihapus dari keranjang',
                    'cart_count' => $this->getCartItemsCount($userId)
                ];
            }

            if ($quantity > $cartItem->product->stock) {
                return [
                    'success' => false,
                    'message' => 'Quantity melebihi stok. Stok tersedia: ' . $cartItem->product->stock
                ];
            }

            $cartItem->update(['quantity' => $quantity]);

            return [
                'success' => true,
                'message' => 'Quantity berhasil diupdate',
                'cart_count' => $this->getCartItemsCount($userId)
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart(int $cartItemId, ?int $userId = null): array
    {
        try {
            $userId = $userId ?? Auth::id();
            
            $cartItem = CartItem::whereHas('cart', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->find($cartItemId);

            if (!$cartItem) {
                return [
                    'success' => false,
                    'message' => 'Item keranjang tidak ditemukan'
                ];
            }

            $cartItem->delete();

            return [
                'success' => true,
                'message' => 'Item berhasil dihapus dari keranjang',
                'cart_count' => $this->getCartItemsCount($userId)
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Clear entire cart
     */
    public function clearCart(?int $userId = null): array
    {
        try {
            $userId = $userId ?? Auth::id();
            
            $cart = Cart::where('user_id', $userId)->first();
            
            if ($cart) {
                $cart->clearCart();
            }

            return [
                'success' => true,
                'message' => 'Keranjang berhasil dikosongkan'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get cart for user
     */
    public function getCart(?int $userId = null): ?Cart
    {
        $userId = $userId ?? Auth::id();
        return Cart::with(['items.product.category', 'items.product.images'])
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Get cart items count
     */
    public function getCartItemsCount(?int $userId = null): int
    {
        $userId = $userId ?? Auth::id();
        $cart = Cart::where('user_id', $userId)->first();
        return $cart ? $cart->total_items : 0;
    }

    /**
     * Get cart summary
     */
    public function getCartSummary(?int $userId = null): array
    {
        $cart = $this->getCart($userId);
        
        if (!$cart || $cart->isEmpty()) {
            return [
                'items_count' => 0,
                'subtotal' => 0,
                'formatted_subtotal' => 'Rp 0',
                'total_discount' => 0,
                'formatted_total_discount' => 'Rp 0',
                'original_total' => 0,
                'formatted_original_total' => 'Rp 0',
                'items' => []
            ];
        }

        // Calculate total discount
        $totalDiscount = 0;
        $originalTotal = 0;
        
        foreach ($cart->cartItems as $item) {
            if ($item->product->is_on_sale) {
                $originalItemTotal = $item->quantity * $item->product->price;
                $discountedItemTotal = $item->quantity * $item->product->discount_price;
                $totalDiscount += ($originalItemTotal - $discountedItemTotal);
                $originalTotal += $originalItemTotal;
            } else {
                $originalTotal += $item->quantity * $item->product->price;
            }
        }

        return [
            'items_count' => $cart->total_items,
            'subtotal' => $cart->subtotal,
            'formatted_subtotal' => $cart->formatted_subtotal,
            'total_discount' => $totalDiscount,
            'formatted_total_discount' => 'Rp ' . number_format($totalDiscount, 0, ',', '.'),
            'original_total' => $originalTotal,
            'formatted_original_total' => 'Rp ' . number_format($originalTotal, 0, ',', '.'),
            'items' => $cart->items
        ];
    }

    /**
     * Validate cart before checkout
     */
    public function validateCart(?int $userId = null): array
    {
        $cart = $this->getCart($userId);
        
        if (!$cart || $cart->isEmpty()) {
            return [
                'valid' => false,
                'message' => 'Keranjang kosong'
            ];
        }

        $errors = [];
        
        foreach ($cart->cartItems as $item) {
            if ($item->quantity > $item->product->stock) {
                $errors[] = "Stok {$item->product->name} tidak mencukupi. Tersedia: {$item->product->stock}";
            }
        }

        if (!empty($errors)) {
            return [
                'valid' => false,
                'message' => 'Ada masalah dengan keranjang Anda',
                'errors' => $errors
            ];
        }

        return [
            'valid' => true,
            'message' => 'Keranjang valid untuk checkout'
        ];
    }
}