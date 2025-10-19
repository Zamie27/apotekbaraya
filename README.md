# Apotek Baraya

Platform e‑commerce apotek berbasis Laravel 12 & Livewire 3 dengan alur resep (upload, konfirmasi apoteker, pembuatan pesanan) dan integrasi pembayaran Midtrans (Snap).

## Fitur Utama
- Katalog produk, kategori, pencarian, deskripsi produk
- Keranjang & Checkout
- Resep: upload pelanggan, konfirmasi apoteker, buat pesanan dari resep
- Pembayaran Midtrans Snap: tombol "Bayar" di detail pesanan, status & timeline pembayaran
- Manajemen pesanan: waiting_payment → paid → waiting_confirmation → processing → shipped → delivered; pembatalan; bukti pengiriman
- Admin: manajemen produk/kategori, pesanan, refund, pengguna, pengaturan toko
- Kurir: daftar & detail pengiriman
- Notifikasi email & antrean (queue)

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
