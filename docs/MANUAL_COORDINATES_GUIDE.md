# Manual Coordinates System Guide

Panduan lengkap untuk mengelola sistem koordinat manual di Apotek Baraya E-Commerce.

## Overview

Sistem koordinat manual memungkinkan aplikasi menggunakan koordinat yang telah ditentukan secara manual untuk alamat-alamat tertentu, mengurangi ketergantungan pada API geocoding eksternal dan meningkatkan akurasi lokasi.

## Struktur Data alamatsubang.json

### Format Dasar

```json
{
    "NamaKecamatan": {
        "kodepos": "41XXX",
        "desa": [
            "NamaDesa1",
            "NamaDesa2",
            "NamaDesa3"
        ],
        "coordinates": {
            "NamaDesa1": {
                "latitude": -6.XXXXX,
                "longitude": 107.XXXXX
            },
            "NamaDesa2": {
                "latitude": -6.XXXXX,
                "longitude": 107.XXXXX
            }
        }
    }
}
```

### Contoh Implementasi

```json
{
    "Subang": {
        "kodepos": "41211",
        "desa": [
            "Parung",
            "Karanganyar",
            "Sukamelang",
            "Wanareja"
        ],
        "coordinates": {
            "Parung": {
                "latitude": -6.58794,
                "longitude": 107.738773
            },
            "Karanganyar": {
                "latitude": -6.5885,
                "longitude": 107.7395
            }
        }
    }
}
```

## Cara Menambahkan Koordinat Manual Baru

### 1. Menambahkan Koordinat untuk Desa yang Sudah Ada

Jika desa sudah terdaftar dalam array `desa` tetapi belum memiliki koordinat:

```json
// Sebelum
"Subang": {
    "kodepos": "41211",
    "desa": ["Parung", "Karanganyar", "Sukamelang", "Wanareja"],
    "coordinates": {
        "Parung": {"latitude": -6.58794, "longitude": 107.738773}
    }
}

// Sesudah - menambahkan koordinat untuk Karanganyar
"Subang": {
    "kodepos": "41211",
    "desa": ["Parung", "Karanganyar", "Sukamelang", "Wanareja"],
    "coordinates": {
        "Parung": {"latitude": -6.58794, "longitude": 107.738773},
        "Karanganyar": {"latitude": -6.5885, "longitude": 107.7395}
    }
}
```

### 2. Menambahkan Desa Baru dengan Koordinat

```json
// Menambahkan desa baru "DesaBaru" ke kecamatan Subang
"Subang": {
    "kodepos": "41211",
    "desa": ["Parung", "Karanganyar", "Sukamelang", "Wanareja", "DesaBaru"],
    "coordinates": {
        "Parung": {"latitude": -6.58794, "longitude": 107.738773},
        "Karanganyar": {"latitude": -6.5885, "longitude": 107.7395},
        "DesaBaru": {"latitude": -6.5890, "longitude": 107.7400}
    }
}
```

### 3. Menambahkan Kecamatan Baru

```json
// Menambahkan kecamatan baru "KecamatanBaru"
"KecamatanBaru": {
    "kodepos": "41XXX",
    "desa": [
        "DesaA",
        "DesaB",
        "DesaC"
    ],
    "coordinates": {
        "DesaA": {
            "latitude": -6.XXXXX,
            "longitude": 107.XXXXX
        },
        "DesaB": {
            "latitude": -6.XXXXX,
            "longitude": 107.XXXXX
        }
    }
}
```

## Cara Mendapatkan Koordinat yang Akurat

### 1. Menggunakan Google Maps

1. Buka Google Maps
2. Cari lokasi desa yang diinginkan
3. Klik kanan pada titik yang tepat
4. Pilih koordinat yang muncul (format: latitude, longitude)
5. Salin koordinat tersebut

### 2. Menggunakan GPS Tools Online

- **GPS Coordinates**: https://www.gps-coordinates.net/
- **LatLong.net**: https://www.latlong.net/
- **Maps.ie**: https://www.maps.ie/coordinates.html

### 3. Format Koordinat

- **Latitude**: Nilai negatif untuk belahan selatan (Indonesia: -6.XXXXX)
- **Longitude**: Nilai positif untuk belahan timur (Indonesia: 107.XXXXX)
- **Presisi**: Gunakan minimal 5 digit desimal untuk akurasi yang baik

## Aturan dan Best Practices

### 1. Konsistensi Penamaan

- Gunakan nama resmi desa sesuai data pemerintah
- Hindari singkatan atau nama tidak resmi
- Pastikan konsistensi huruf besar/kecil

### 2. Validasi Koordinat

```bash
# Jalankan test untuk memverifikasi koordinat baru
php artisan tinker

# Test koordinat manual
$addressService = new App\Services\AddressService();
$result = $addressService->getManualCoordinates('NamaDesa', 'NamaKecamatan', 'KodePos');
var_dump($result);

# Test validasi alamat
$isValid = $addressService->isValidAddress('NamaDesa', 'NamaKecamatan', 'KodePos');
var_dump($isValid);
```

### 3. Backup Data

- Selalu backup file `alamatsubang.json` sebelum melakukan perubahan
- Gunakan version control (Git) untuk tracking perubahan
- Test perubahan di environment development terlebih dahulu

### 4. Performance Considerations

- Field `coordinates` bersifat opsional - desa tanpa koordinat akan menggunakan API geocoding
- Prioritaskan menambahkan koordinat untuk desa-desa yang sering digunakan
- Monitor performa aplikasi setelah menambahkan banyak koordinat baru

## Troubleshooting

### 1. Koordinat Tidak Ditemukan

**Problem**: Fungsi `getManualCoordinates` mengembalikan `null`

**Solusi**:
- Periksa ejaan nama desa, kecamatan, dan kode pos
- Pastikan koordinat sudah ditambahkan ke field `coordinates`
- Verifikasi struktur JSON tidak rusak

### 2. Validasi Alamat Gagal

**Problem**: Fungsi `isValidAddress` mengembalikan `false`

**Solusi**:
- Pastikan desa terdaftar dalam array `desa`
- Periksa kode pos sesuai dengan yang ada di JSON
- Verifikasi nama kecamatan sudah benar

### 3. JSON Syntax Error

**Problem**: Error parsing JSON file

**Solusi**:
- Gunakan JSON validator online untuk memeriksa syntax
- Pastikan semua tanda kurung dan koma sudah benar
- Periksa tidak ada trailing comma di akhir array/object

## Testing Koordinat Baru

### Script Test Manual

```php
<?php
// test_new_coordinates.php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\GeocodingService;
use App\Services\AddressService;

$addressService = new AddressService();
$geocodingService = new GeocodingService($addressService);

// Test koordinat baru
$result = $geocodingService->geocodeAddress(
    'NamaDesa',      // Desa
    'NamaKecamatan', // Kecamatan  
    'Subang',        // Kabupaten
    'Jawa Barat',    // Provinsi
    'KodePos'        // Kode Pos
);

echo "Result: ";
var_dump($result);
echo "Source: " . ($result['source'] ?? 'unknown') . "\n";
```

### Expected Output

```
Result: array(3) {
  ["lat"]=>
  float(-6.XXXXX)
  ["lon"]=>
  float(107.XXXXX)
  ["source"]=>
  string(11) "manual_json"
}
Source: manual_json
```

## Maintenance

### 1. Regular Updates

- Review dan update koordinat secara berkala
- Tambahkan koordinat untuk alamat yang sering error
- Monitor log aplikasi untuk alamat yang gagal di-geocode

### 2. Data Quality

- Verifikasi akurasi koordinat dengan mengunjungi lokasi fisik
- Cross-check dengan data pemerintah terbaru
- Update jika ada perubahan administratif wilayah

### 3. Performance Monitoring

- Monitor response time geocoding
- Track usage API geocoding eksternal
- Analisis efektivitas koordinat manual vs API

---

**Catatan**: Dokumentasi ini akan diupdate seiring dengan perkembangan sistem. Untuk pertanyaan atau masalah, silakan hubungi tim development.