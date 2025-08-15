# Instruksi Migration dan Seeder - Apotek Baraya

## File yang Telah Dibuat

✅ **Migration**: `database/migrations/2025_07_30_054057_create_products_table.php` (sudah ada)
✅ **CategorySeeder**: `database/seeders/CategorySeeder.php` (baru dibuat)
✅ **ProductSeeder**: `database/seeders/ProductSeeder.php` (baru dibuat)
✅ **DatabaseSeeder**: `database/seeders/DatabaseSeeder.php` (sudah diperbarui)

## Cara Menjalankan Migration dan Seeder

### Opsi 1: Fresh Migration dengan Seeder (Recommended)
```bash
php artisan migrate:fresh --seed
```

### Opsi 2: Step by Step
```bash
# 1. Reset database
php artisan migrate:fresh

# 2. Jalankan seeder
php artisan db:seed
```

### Opsi 3: Seeder Spesifik Saja
```bash
# Jika migration sudah jalan, hanya jalankan seeder kategori dan produk
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=ProductSeeder
```

## Yang Akan Terjadi Setelah Migration

### Tabel yang Dibuat:
1. **categories** - 11 kategori apotek
2. **products** - 16 produk dummy dari berbagai kategori
3. **product_images** - Tabel untuk gambar produk (kosong)
4. **roles** - Role pengguna (admin, apoteker, kurir, pelanggan)
5. **users** - User dummy untuk setiap role

### Data Kategori yang Akan Dibuat:
1. Obat Keras
2. Obat Bebas Terbatas
3. Obat Bebas
4. Jamu & Herbal
5. Kebutuhan Bayi & Ibu
6. Perawatan Wajah
7. Perawatan Tubuh
8. Kosmetik
9. Suplemen & Vitamin
10. Alat Kesehatan
11. Perban & Pertolongan Pertama

### Data Produk yang Akan Dibuat:
- **Obat Keras**: Amoxicillin 500mg, Captopril 25mg
- **Obat Bebas Terbatas**: Paracetamol 500mg, Ibuprofen 400mg
- **Obat Bebas**: Antangin JRG, Tolak Angin
- **Jamu & Herbal**: Jamu Kunyit Asam
- **Kebutuhan Bayi & Ibu**: Susu Formula Bayi, Minyak Telon Bayi
- **Suplemen & Vitamin**: Vitamin C 1000mg, Multivitamin Dewasa
- **Alat Kesehatan**: Termometer Digital, Tensimeter Digital
- **Perban & P3K**: Perban Elastis 10cm, Plester Luka Waterproof

## Verifikasi Data

Setelah migration berhasil, Anda dapat memverifikasi data dengan:

```bash
# Cek jumlah kategori
php artisan tinker
>>> App\Models\Category::count(); // Harus return 11

# Cek jumlah produk
>>> App\Models\Product::count(); // Harus return 16

# Cek produk berdasarkan kategori
>>> App\Models\Product::where('category_id', 1)->count(); // Obat Keras: 2 produk
```

## Troubleshooting

### Jika Ada Error Foreign Key
- Pastikan migration categories dijalankan sebelum products
- Migration file sudah mengatur urutan yang benar

### Jika Ada Error Duplicate Entry
- Jalankan `php artisan migrate:fresh --seed` untuk reset database

### Jika Seeder Tidak Jalan
- Pastikan file seeder ada di `database/seeders/`
- Pastikan DatabaseSeeder.php sudah diperbarui
- Cek syntax error di file seeder

## Next Steps Setelah Migration

1. **Buat Model Eloquent**:
   ```bash
   php artisan make:model Category
   php artisan make:model Product
   php artisan make:model ProductImage
   ```

2. **Test Data di Browser**:
   - Akses halaman dashboard
   - Cek apakah produk muncul dengan benar

3. **Implementasi CRUD**:
   - Admin panel untuk manage kategori
   - Admin panel untuk manage produk

4. **Upload Gambar Produk**:
   - Implementasi upload gambar
   - Seeder untuk product_images

---

**PENTING**: Jalankan command migration di terminal/command prompt dari direktori root project Apotek Baraya.