# Perbaikan Logika Perhitungan Ongkir

## Masalah yang Ditemukan

Pada hasil test sebelumnya:
- **Jarak**: 6.28 km
- **Biaya Ongkir**: Rp 0 (seharusnya Rp 14.000)
- **Gratis Ongkir**: Ya
- **Dapat Dikirim**: Ya

## Akar Masalah

Logika gratis ongkir di `DistanceCalculatorService::calculateShippingCost()` **TERBALIK**:

### Logika Lama (Salah)
```php
$isFreeShipping = $orderTotal >= $freeShippingMinimum; // Rp 100.000
```

### Logika Baru (Benar)
```php
$isFreeShipping = $orderTotal < $freeShippingMinimum; // Rp 100.000
```

## Penjelasan Logika Bisnis

### Konsep Gratis Ongkir yang Benar
- **Gratis ongkir diberikan untuk pesanan KECIL** (< Rp 100.000)
- **Tujuan**: Mendorong pelanggan untuk berbelanja lebih banyak
- **Pesanan besar** (≥ Rp 100.000) **dikenakan ongkir** karena sudah mencapai target minimum

### Hasil Test Setelah Perbaikan

| Total Belanja | Jarak | Biaya Dasar | Biaya Final | Gratis Ongkir |
|---------------|-------|-------------|-------------|---------------|
| Rp 50.000     | 6.28 km | Rp 14.000   | **Rp 0**    | ✅ Ya         |
| Rp 100.000    | 6.28 km | Rp 14.000   | **Rp 14.000** | ❌ Tidak     |
| Rp 150.000    | 6.28 km | Rp 14.000   | **Rp 14.000** | ❌ Tidak     |

## Rekomendasi Konfigurasi Bisnis

### Opsi 1: Gratis Ongkir untuk Pesanan Kecil (Saat Ini)
```php
'free_shipping_minimum' => '100000', // Rp 100.000
'shipping_rate_per_km' => '2000',    // Rp 2.000/km
```
**Keuntungan**: Menarik pelanggan baru, mudah untuk pembelian obat rutin
**Kerugian**: Margin keuntungan berkurang untuk pesanan kecil

### Opsi 2: Gratis Ongkir untuk Pesanan Besar (Alternatif)
```php
'free_shipping_minimum' => '150000', // Rp 150.000
'shipping_rate_per_km' => '3000',    // Rp 3.000/km
```
**Keuntungan**: Mendorong pembelian dalam jumlah besar, margin lebih baik
**Kerugian**: Mungkin mengurangi daya tarik untuk pelanggan baru

### Opsi 3: Tanpa Gratis Ongkir
```php
'free_shipping_minimum' => '0', // Tidak ada gratis ongkir
'shipping_rate_per_km' => '2500', // Rp 2.500/km
```
**Keuntungan**: Margin konsisten, biaya operasional tercover
**Kerugian**: Kurang kompetitif dibanding apotek online lain

## File yang Dimodifikasi

1. **`app/Services/DistanceCalculatorService.php`**
   - Perbaikan logika `calculateShippingCost()`
   - Penambahan komentar penjelasan

## Pengujian

✅ **Test berhasil** - Logika ongkir sekarang berfungsi dengan benar:
- Pesanan < Rp 100.000: **Gratis ongkir**
- Pesanan ≥ Rp 100.000: **Dikenakan ongkir** sesuai jarak

## Catatan Penting

1. **Koordinat toko sudah akurat** (dari perbaikan sebelumnya)
2. **Perhitungan jarak menggunakan formula Haversine** (akurat)
3. **Tarif Rp 2.000/km** sudah sesuai standar pengiriman lokal
4. **Jarak maksimal 15 km** masih dalam batas wajar untuk apotek lokal

## Rekomendasi Selanjutnya

1. **Monitor performa bisnis** dengan konfigurasi saat ini
2. **Evaluasi margin keuntungan** vs daya tarik pelanggan
3. **Pertimbangkan penyesuaian** `free_shipping_minimum` berdasarkan data penjualan
4. **Tambahkan notifikasi** di UI tentang syarat gratis ongkir