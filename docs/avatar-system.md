# Avatar System Documentation

## Overview
Sistem avatar dinamis untuk aplikasi Apotek Baraya yang mendukung avatar default berdasarkan role dan avatar pribadi yang dapat diupload oleh pengguna.

## File Structure
```
public/src/img/avatars/
├── default-admin.svg      # Avatar default untuk Admin
├── default-apoteker.svg   # Avatar default untuk Apoteker
├── default-kurir.svg      # Avatar default untuk Kurir
└── default-pelanggan.svg  # Avatar default untuk Pelanggan

storage/app/public/
└── avatars/               # Folder untuk avatar yang diupload pengguna
```

## How It Works

### 1. Default Avatars
- Setiap role memiliki avatar default yang unik
- Avatar disimpan dalam format SVG di `public/src/img/avatars/`
- Desain avatar mencerminkan karakteristik masing-masing role:
  - **Admin**: Hijau dengan crown/mahkota
  - **Apoteker**: Biru dengan simbol medis (cross)
  - **Kurir**: Orange dengan topi dan paket
  - **Pelanggan**: Ungu dengan shopping bag

### 2. Custom Avatars
- Pengguna dapat mengupload avatar pribadi
- File disimpan di `storage/app/public/avatars/`
- Accessible via symbolic link `public/storage/`

### 3. Avatar Logic
Method `getAvatarUrl()` di User model:
1. Cek apakah user memiliki avatar pribadi
2. Jika ada dan file exists → return custom avatar
3. Jika tidak → return default avatar berdasarkan role

## Usage in Blade Templates
```blade
<img src="{{ auth()->user()->getAvatarUrl() }}" alt="Profile Picture" />
```

## Implementation Files
- `app/Models/User.php` - Avatar logic
- `resources/views/components/layouts/*.blade.php` - Layout files
- `public/src/img/avatars/` - Default avatar files

## Future Enhancements
- Upload avatar functionality in profile pages
- Image validation and resizing
- Avatar cropping interface
- Multiple avatar options per role