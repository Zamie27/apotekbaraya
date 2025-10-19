# Apotek Baraya 💊

E‑commerce apotek modern dengan alur resep end‑to‑end, dibangun pakai Laravel 12 + Livewire 3, UI cepat, dan pembayaran Midtrans (Snap).

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-3-4E56A6?logo=livewire)](https://livewire.laravel.com)
[![Tailwind](https://img.shields.io/badge/Tailwind-4-38B2AC?logo=tailwind-css)](https://tailwindcss.com)
[![Vite](https://img.shields.io/badge/Vite-6-646CFF?logo=vite)](https://vitejs.dev)
[![MIT License](https://img.shields.io/badge/License-MIT-000000.svg)](https://opensource.org/licenses/MIT)

Quick Links: [Instalasi Cepat](#instalasi-cepat) • [Fitur](#fitur-utama) • [Pembayaran](#pembayaran-midtrans) • [Akun Demo](#akun-demo-hasil-seeding) • [Dokumentasi](#dokumentasi)

## Fitur Utama
- 🏪 Katalog & pencarian produk (kategori, deskripsi, detail lengkap)
- 🛒 Keranjang & Checkout yang halus
- 📄 Resep: upload pelanggan → konfirmasi apoteker → buat pesanan dari resep
- 💳 Midtrans Snap: tombol "Bayar" di detail pesanan + status & timeline pembayaran
- ⏱️ Timeline pesanan: waiting_payment → paid → waiting_confirmation → processing → shipped → delivered
- 🧑‍💼 Admin: produk/kategori, pesanan, refund, pengguna, pengaturan toko
- 🚚 Kurir: daftar & detail pengiriman
- ✉️ Notifikasi email & antrean (queue)

## Teknologi
Laravel 12, Livewire 3, Tailwind CSS 4 + DaisyUI, Vite 6, Pest.

## Prasyarat
- PHP >= 8.2, Composer
- Node.js >= 18, npm
- Database: SQLite (default) atau MySQL
- Akun Midtrans (server_key & client_key) untuk pembayaran

## Instalasi Cepat
1. Clone repo: `git clone <repo-url>`
2. `composer install`
3. Salin env: `cp .env.example .env`
4. Atur APP_URL, koneksi DB (atau buat `database/database.sqlite` untuk SQLite)
5. Set Midtrans env: `MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`, `MIDTRANS_IS_PRODUCTION=false`, `MIDTRANS_IS_SANITIZED=true`, `MIDTRANS_IS_3DS=true`
6. `php artisan key:generate`
7. Migrasi: `php artisan migrate`
8. (Opsional) Seed: `php artisan db:seed`
   - Menyediakan user, kategori, produk, metode pembayaran (PaymentMethodSeeder), contoh pesanan
9. Jalankan dev:
   - Satu perintah: `composer run dev` (serve + queue:listen + vite)
   - Atau manual: `php artisan serve`, `php artisan queue:listen`, `npm run dev`

## Akun Demo (hasil seeding)
- Admin: `admin@apotekbaraya.com` / `password`
- Apoteker: `apoteker@apotekbaraya.com` / `password`
- Kurir: `kurir@apotekbaraya.com` / `password`
- Pelanggan: `customer1@example.com` s.d. `customer5@example.com` / `password`

## Pembayaran Midtrans
- Pesanan normal maupun dari resep berstatus `waiting_payment` dan memiliki Payment record (status `pending`).
- Pelanggan dapat menekan tombol "Bayar" di halaman detail pesanan untuk membuka Snap (`/payment/snap`).
- Notifikasi pembayaran ditangani melalui webhook: `/payment/notification` atau `/webhook/midtrans`.

## Variabel Lingkungan Terkait
- Midtrans: `MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`, `MIDTRANS_IS_PRODUCTION`, `MIDTRANS_IS_SANITIZED`, `MIDTRANS_IS_3DS`
- ReCAPTCHA (opsional): `RECAPTCHA_SITE_KEY`, `RECAPTCHA_SECRET_KEY`
- Email (opsional): `MAIL_*`
- Wajib: `APP_URL`

## Pengujian
- Jalankan: `php artisan test`
- Atau: `vendor/bin/pest`

## Dokumentasi
- `docs/PAYMENT_GATEWAY_MIDTRANS.md`
- `docs/PRESCRIPTION_SYSTEM_TESTING.md`
- `docs/DEPLOYMENT_GUIDE.md`, `PRODUCTION_DEPLOYMENT.md`

## Lisensi
MIT

## Kontribusi
Pull request dan issue dipersilakan.
