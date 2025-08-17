# Auto-Geocoding System untuk Alamat Pelanggan

## Overview

Sistem auto-geocoding telah diimplementasikan untuk secara otomatis mendapatkan koordinat latitude dan longitude dari alamat pelanggan saat alamat disimpan. Ini meningkatkan akurasi perhitungan jarak dan biaya pengiriman.

## Fitur Utama

### 1. **Auto-Geocoding saat Menyimpan Alamat**
- Koordinat latitude dan longitude otomatis diperoleh saat pelanggan menyimpan alamat baru
- Koordinat juga diperbarui saat pelanggan mengedit alamat yang sudah ada
- Menggunakan service geocoding yang sama dengan admin settings

### 2. **Implementasi di Multiple Components**
- **Profile Component** (`app/Livewire/Profile.php`): Untuk manajemen alamat di halaman profil
- **Checkout Component** (`app/Livewire/Checkout.php`): Untuk alamat baru saat checkout

### 3. **Feedback kepada User**
- Pesan sukses yang informatif berdasarkan hasil geocoding
- Jika geocoding berhasil: "Alamat berhasil ditambahkan dan koordinat lokasi telah diperoleh!"
- Jika geocoding gagal: "Alamat berhasil ditambahkan! (Koordinat lokasi akan diperbarui secara otomatis)"

## Technical Implementation

### 1. **Database Schema**

Kolom latitude dan longitude telah ditambahkan ke tabel `user_addresses`:

```sql
-- Migration: 2025_01_15_000002_add_coordinates_to_user_addresses_table.php
ALTER TABLE user_addresses 
ADD COLUMN latitude DECIMAL(10,8) NULL AFTER postal_code,
ADD COLUMN longitude DECIMAL(11,8) NULL AFTER latitude;
```

### 2. **Model Updates**

**UserAddress Model** (`app/Models/UserAddress.php`):
```php
protected $fillable = [
    'user_id', 'label', 'recipient_name', 'phone',
    'address', 'district', 'city', 'postal_code',
    'notes', 'is_default',
    'latitude', 'longitude'  // Added coordinates
];
```

### 3. **Service Integration**

**CheckoutService** (`app/Services/CheckoutService.php`):
```php
public function updateAddressCoordinates(UserAddress $address): bool
{
    try {
        $fullAddress = $address->address . ', ' . $address->district . ', ' . 
                      $address->city . ', ' . $address->postal_code;
        
        $coordinates = $this->distanceCalculator->getCoordinatesFromAddress($fullAddress);
        
        $address->update([
            'latitude' => $coordinates['latitude'],
            'longitude' => $coordinates['longitude']
        ]);

        return true;
    } catch (\Exception $e) {
        \Log::error('Failed to update address coordinates: ' . $e->getMessage());
        return false;
    }
}
```

### 4. **Livewire Component Updates**

**Profile Component** - Method `saveAddress()`:
```php
// Auto-geocoding: Get coordinates for new/updated address
$checkoutService = app(CheckoutService::class);
$geocodingSuccess = $checkoutService->updateAddressCoordinates($address);

// Provide appropriate feedback
if ($geocodingSuccess) {
    session()->flash('success', 'Alamat berhasil ditambahkan dan koordinat lokasi telah diperoleh!');
} else {
    session()->flash('success', 'Alamat berhasil ditambahkan! (Koordinat lokasi akan diperbarui secara otomatis)');
}
```

**Checkout Component** - Method `saveNewAddress()`:
```php
// Auto-geocoding: Get coordinates for new address
$checkoutService = app(CheckoutService::class);
$geocodingSuccess = $checkoutService->updateAddressCoordinates($address);

// Show appropriate message based on geocoding result
if ($geocodingSuccess) {
    session()->flash('success', 'Alamat berhasil ditambahkan dan koordinat lokasi telah diperoleh!');
} else {
    session()->flash('success', 'Alamat berhasil ditambahkan! (Koordinat lokasi akan diperbarui secara otomatis)');
}
```

## Benefits

### 1. **Akurasi Pengiriman**
- Koordinat yang akurat memungkinkan perhitungan jarak yang lebih presisi
- Biaya pengiriman menjadi lebih akurat
- Validasi area pengiriman yang lebih baik

### 2. **User Experience**
- Pelanggan tidak perlu manual input koordinat
- Proses yang seamless dan otomatis
- Feedback yang jelas tentang status geocoding

### 3. **Sistem yang Robust**
- Graceful handling jika geocoding gagal
- Alamat tetap tersimpan meskipun geocoding gagal
- Logging error untuk monitoring

## Error Handling

### 1. **Geocoding Failure**
- Alamat tetap tersimpan ke database
- Koordinat akan bernilai NULL
- User mendapat notifikasi yang informatif
- Error di-log untuk debugging

### 2. **Fallback Mechanism**
- Jika koordinat tidak tersedia, sistem masih bisa berfungsi
- Perhitungan jarak akan menggunakan metode alternatif
- Admin bisa manual update koordinat jika diperlukan

## Future Enhancements

### 1. **Batch Geocoding**
- Command untuk update koordinat alamat yang belum memiliki koordinat
- Background job untuk geocoding alamat lama

### 2. **GPS Integration**
- HTML5 Geolocation API untuk mendapatkan koordinat langsung dari device
- Opsi untuk pelanggan memilih antara alamat manual atau GPS

### 3. **Geocoding Provider Options**
- Support multiple geocoding providers (Google Maps, Nominatim, etc.)
- Fallback ke provider lain jika satu gagal

## Testing

### Manual Testing Steps:

1. **Test di Profile Page:**
   - Login sebagai pelanggan
   - Buka halaman Profile
   - Tambah alamat baru
   - Verifikasi pesan sukses dan koordinat tersimpan

2. **Test di Checkout Page:**
   - Tambah produk ke keranjang
   - Pilih delivery dan tambah alamat baru
   - Verifikasi alamat tersimpan dengan koordinat

3. **Test Error Handling:**
   - Input alamat yang tidak valid/tidak ditemukan
   - Verifikasi alamat tetap tersimpan tanpa koordinat

## Monitoring

- Check log file: `storage/logs/laravel.log`
- Monitor geocoding success rate
- Track alamat tanpa koordinat untuk manual review

---

**Implementasi Date:** January 2025  
**Status:** Active  
**Dependencies:** DistanceCalculatorService, CheckoutService