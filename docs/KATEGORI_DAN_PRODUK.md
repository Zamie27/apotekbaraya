# Dokumentasi Kategori dan Produk - Apotek Baraya

## Overview
Sistem kategori dan produk telah dibuat untuk mendukung e-commerce Apotek Baraya dengan struktur database yang lengkap dan data dummy yang siap digunakan.

## Struktur Database

### Tabel Categories
- `category_id` (Primary Key)
- `name` - Nama kategori
- `slug` - URL-friendly identifier
- `description` - Deskripsi kategori
- `image` - Path gambar kategori (nullable)
- `is_active` - Status aktif kategori
- `sort_order` - Urutan tampilan
- `timestamps`

### Tabel Products
- `product_id` (Primary Key)
- `name` - Nama produk
- `slug` - URL-friendly identifier
- `description` - Deskripsi lengkap produk
- `short_description` - Deskripsi singkat
- `price` - Harga normal
- `discount_price` - Harga diskon (nullable)
- `stock` - Status stok (available/out_of_stock)
- `sku` - Stock Keeping Unit
- `category_id` - Foreign key ke categories
- `requires_prescription` - Apakah butuh resep dokter
- `is_active` - Status aktif produk
- `unit` - Satuan (pcs, box, botol, strip, tube, sachet)
- `specifications` - Spesifikasi dalam format JSON
- `weight` - Berat produk (kg)
- `timestamps`

### Tabel Product Images
- `image_id` (Primary Key)
- `product_id` - Foreign key ke products
- `image_path` - Path file gambar
- `is_primary` - Apakah gambar utama
- `sort_order` - Urutan tampilan
- `timestamps`

## Kategori yang Tersedia

1. **Obat Keras** - Obat yang memerlukan resep dokter
2. **Obat Bebas Terbatas** - Obat dengan batasan tertentu
3. **Obat Bebas** - Obat yang dapat dibeli bebas
4. **Jamu & Herbal** - Produk jamu tradisional
5. **Kebutuhan Bayi & Ibu** - Produk untuk bayi dan ibu
6. **Perawatan Wajah** - Produk perawatan wajah
7. **Perawatan Tubuh** - Produk perawatan tubuh
8. **Kosmetik** - Produk kosmetik dan makeup
9. **Suplemen & Vitamin** - Suplemen dan vitamin
10. **Alat Kesehatan** - Peralatan medis
11. **Perban & Pertolongan Pertama** - Produk P3K

## Data Dummy Produk

Seeder telah menyediakan data dummy untuk berbagai kategori:

### Obat Keras
- Amoxicillin 500mg (Antibiotik)
- Captopril 25mg (Obat hipertensi)

### Obat Bebas Terbatas
- Paracetamol 500mg (Penurun demam)
- Ibuprofen 400mg (Anti-inflamasi)

### Obat Bebas
- Antangin JRG (Obat masuk angin)
- Tolak Angin (Obat tradisional)

### Jamu & Herbal
- Jamu Kunyit Asam

### Kebutuhan Bayi & Ibu
- Susu Formula Bayi 0-6 Bulan
- Minyak Telon Bayi

### Suplemen & Vitamin
- Vitamin C 1000mg
- Multivitamin Dewasa

### Alat Kesehatan
- Termometer Digital
- Tensimeter Digital

### Perban & Pertolongan Pertama
- Perban Elastis 10cm
- Plester Luka Waterproof

## Cara Menjalankan Migration dan Seeder

### 1. Menjalankan Migration
```bash
php artisan migrate
```

### 2. Menjalankan Seeder (Fresh Install)
```bash
php artisan db:seed
```

### 3. Menjalankan Seeder Spesifik
```bash
# Hanya kategori
php artisan db:seed --class=CategorySeeder

# Hanya produk
php artisan db:seed --class=ProductSeeder
```

### 4. Reset Database dan Seed Ulang
```bash
php artisan migrate:fresh --seed
```

## Fitur Khusus

### 1. Requires Prescription
- Produk dengan `requires_prescription = true` memerlukan resep dokter
- Sistem dapat membedakan produk yang memerlukan resep

### 2. Specifications JSON
- Setiap produk memiliki field `specifications` dalam format JSON
- Dapat menyimpan informasi seperti kandungan, kemasan, produsen

### 3. Stock Management
- Enum stock: 'available', 'out_of_stock'
- Mudah untuk tracking ketersediaan produk

### 4. Pricing System
- `price` untuk harga normal
- `discount_price` untuk harga diskon (nullable)
- Mendukung sistem promosi

### 5. Unit Management
- Berbagai satuan: pcs, box, botol, strip, tube, sachet
- Sesuai dengan kebutuhan apotek

## Model Eloquent (Rekomendasi)

### Category Model
```php
class Category extends Model
{
    protected $primaryKey = 'category_id';
    protected $fillable = ['name', 'slug', 'description', 'image', 'is_active', 'sort_order'];
    
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }
}
```

### Product Model
```php
class Product extends Model
{
    protected $primaryKey = 'product_id';
    protected $fillable = [
        'name', 'slug', 'description', 'short_description', 'price', 'discount_price',
        'stock', 'sku', 'category_id', 'requires_prescription', 'is_active',
        'unit', 'specifications', 'weight'
    ];
    
    protected $casts = [
        'specifications' => 'array',
        'requires_prescription' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'weight' => 'decimal:2'
    ];
    
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }
    
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'product_id');
    }
}
```

## Security Notes

1. **Prescription Validation**: Pastikan validasi resep dokter untuk produk `requires_prescription = true`
2. **Stock Validation**: Implementasikan validasi stok sebelum checkout
3. **Price Validation**: Validasi harga dan diskon untuk mencegah manipulasi
4. **Image Upload**: Implementasikan validasi file upload untuk gambar produk

## Next Steps

1. Buat Model Eloquent untuk Category dan Product
2. Implementasikan CRUD operations
3. Buat interface admin untuk manage kategori dan produk
4. Implementasikan sistem upload gambar produk
5. Buat filter dan search functionality
6. Implementasikan sistem review dan rating produk

---

**Catatan**: Data dummy ini siap untuk development dan testing. Untuk production, ganti dengan data produk yang sebenarnya.