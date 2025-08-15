# Sistem Keranjang Belanja - Apotek Baraya

## Overview
Sistem keranjang belanja yang terintegrasi dengan aplikasi e-commerce Apotek Baraya, memungkinkan pelanggan untuk menambahkan produk obat ke keranjang sebelum melakukan checkout.

## Struktur Database

### Tabel `carts`
```sql
- cart_id (Primary Key, Auto Increment)
- user_id (Foreign Key ke users.user_id)
- created_at (Timestamp)
- updated_at (Timestamp)
```

### Tabel `cart_items`
```sql
- cart_item_id (Primary Key, Auto Increment)
- cart_id (Foreign Key ke carts.cart_id)
- product_id (Foreign Key ke products.product_id)
- quantity (Integer)
- price (Decimal)
- created_at (Timestamp)
- updated_at (Timestamp)
```

## Komponen Utama

### 1. Models

#### Cart Model (`app/Models/Cart.php`)
- **Relasi:**
  - `belongsTo(User::class)` - Relasi ke pengguna
  - `hasMany(CartItem::class)` - Relasi ke item keranjang

- **Methods:**
  - `getTotalItemsAttribute()` - Menghitung total item
  - `getSubtotalAttribute()` - Menghitung subtotal
  - `getFormattedSubtotalAttribute()` - Format subtotal ke Rupiah
  - `isEmpty()` - Cek apakah keranjang kosong
  - `clear()` - Kosongkan keranjang
  - `getOrCreateForUser($user)` - Dapatkan atau buat keranjang untuk user

#### CartItem Model (`app/Models/CartItem.php`)
- **Relasi:**
  - `belongsTo(Cart::class)` - Relasi ke keranjang
  - `belongsTo(Product::class)` - Relasi ke produk

- **Methods:**
  - `getSubtotalAttribute()` - Menghitung subtotal item
  - `getFormattedPriceAttribute()` - Format harga ke Rupiah
  - `getFormattedSubtotalAttribute()` - Format subtotal ke Rupiah
  - `isInStock()` - Cek ketersediaan stok
  - `updateQuantity($quantity)` - Update kuantitas

### 2. Services

#### CartService (`app/Services/CartService.php`)
Service class untuk mengelola logika bisnis keranjang:

- **Methods:**
  - `addToCart($user, $product, $quantity)` - Tambah produk ke keranjang
  - `updateQuantity($user, $cartItemId, $quantity)` - Update kuantitas item
  - `removeFromCart($user, $cartItemId)` - Hapus item dari keranjang
  - `clearCart($user)` - Kosongkan keranjang
  - `getCart($user)` - Dapatkan keranjang user
  - `getCartItemsCount($user)` - Hitung jumlah item
  - `getCartSummary($user)` - Dapatkan ringkasan keranjang
  - `validateCart($user)` - Validasi keranjang

### 3. Livewire Components

#### Cart Component (`app/Livewire/Cart.php`)
Komponen untuk halaman keranjang belanja:

- **Properties:**
  - `$cart` - Data keranjang
  - `$cartItems` - Item-item dalam keranjang

- **Methods:**
  - `mount()` - Inisialisasi komponen
  - `loadCart()` - Muat data keranjang
  - `updateQuantity($cartItemId, $quantity)` - Update kuantitas
  - `removeItem($cartItemId)` - Hapus item
  - `clearCart()` - Kosongkan keranjang
  - `proceedToCheckout()` - Lanjut ke checkout
  - `continueShopping()` - Lanjut belanja

#### AddToCartButton Component (`app/Livewire/AddToCartButton.php`)
Komponen tombol tambah ke keranjang yang dapat digunakan di berbagai halaman:

- **Properties:**
  - `$productId` - ID produk
  - `$quantity` - Kuantitas
  - `$buttonText` - Teks tombol
  - `$buttonClass` - Class CSS tombol
  - `$showQuantityInput` - Tampilkan input kuantitas
  - `$isLoading` - Status loading

- **Methods:**
  - `addToCart()` - Tambah ke keranjang
  - `incrementQuantity()` - Tambah kuantitas
  - `decrementQuantity()` - Kurangi kuantitas

### 4. API Controllers

#### CartController (`app/Http/Controllers/Api/CartController.php`)
API untuk mendapatkan informasi keranjang:

- **Endpoints:**
  - `GET /api/cart/count` - Dapatkan jumlah item keranjang
  - `GET /api/cart/summary` - Dapatkan ringkasan keranjang

## Routing

### Web Routes (`routes/web.php`)
```php
Route::middleware('auth')->group(function () {
    Route::get('/cart', \App\Livewire\Cart::class)->name('cart');
});
```

### API Routes (`routes/api.php`)
```php
Route::middleware('web')->group(function () {
    Route::get('/cart/count', [CartController::class, 'getCartCount'])->name('api.cart.count');
    Route::get('/cart/summary', [CartController::class, 'getCartSummary'])->name('api.cart.summary');
});
```

## Frontend Integration

### Cart Counter
Di layout user (`resources/views/components/layouts/user.blade.php`):
- Ikon keranjang dengan counter dinamis
- Update otomatis via JavaScript dan Livewire events
- Menampilkan jumlah item dalam keranjang

### Usage di Halaman Produk
```blade
<!-- Tombol sederhana -->
<livewire:add-to-cart-button :product-id="$product->id" />

<!-- Dengan input kuantitas -->
<livewire:add-to-cart-button 
    :product-id="$product->id" 
    :show-quantity-input="true" 
    button-text="Beli Sekarang" 
/>

<!-- Custom styling -->
<livewire:add-to-cart-button 
    :product-id="$product->id" 
    button-class="btn btn-primary btn-lg" 
    :key="'product-'.$product->id" 
/>
```

## Security Features

1. **Authentication Check** - Hanya user yang login dapat mengakses keranjang
2. **Input Validation** - Validasi kuantitas dan data input
3. **Stock Validation** - Cek ketersediaan stok sebelum menambah ke keranjang
4. **CSRF Protection** - Menggunakan middleware Laravel
5. **XSS Prevention** - Escape output data

## Error Handling

- **Product Not Found** - Produk tidak ditemukan
- **Out of Stock** - Stok habis atau tidak tersedia
- **Invalid Quantity** - Kuantitas tidak valid
- **Authentication Required** - User belum login
- **Database Errors** - Error koneksi atau query database

## Events & Notifications

### Livewire Events
- `cart-updated` - Dipanggil saat keranjang diupdate
- Auto-update cart counter di navbar

### Flash Messages
- Success: Produk berhasil ditambahkan
- Error: Pesan error yang informatif
- Warning: Peringatan stok terbatas

## Performance Considerations

1. **Lazy Loading** - Relasi dimuat sesuai kebutuhan
2. **Caching** - Cache data keranjang untuk performa
3. **Pagination** - Untuk keranjang dengan banyak item
4. **AJAX Updates** - Update tanpa reload halaman

## Testing

### Unit Tests
- Test model methods
- Test service logic
- Test API endpoints

### Feature Tests
- Test add to cart functionality
- Test cart operations (update, remove, clear)
- Test authentication requirements

## Migration Commands

```bash
# Jalankan migrasi
php artisan migrate

# Rollback jika diperlukan
php artisan migrate:rollback --step=1
```

## Troubleshooting

### Common Issues
1. **Cart counter tidak update** - Pastikan JavaScript events berjalan
2. **Produk tidak bisa ditambah** - Cek stok dan validasi
3. **Session issues** - Clear cache dan session
4. **Database errors** - Cek koneksi dan struktur tabel

### Debug Commands
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Check routes
php artisan route:list

# Check database
php artisan migrate:status
```

## Future Enhancements

1. **Guest Cart** - Keranjang untuk user yang belum login
2. **Cart Persistence** - Simpan keranjang di localStorage
3. **Wishlist Integration** - Integrasi dengan wishlist
4. **Bulk Operations** - Operasi massal pada keranjang
5. **Cart Analytics** - Analitik perilaku keranjang
6. **Mobile Optimization** - Optimasi untuk mobile
7. **Real-time Updates** - Update real-time menggunakan WebSocket