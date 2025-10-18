# Apotek Baraya — Overview & User Guide

Dokumen ini menjadi ringkasan komprehensif sistem Apotek Baraya, mencakup fitur utama, peran pengguna, arsitektur, modul fungsional, alur kerja, keamanan, integrasi pembayaran, notifikasi email, serta panduan penggunaan.

## Fitur Utama
- Login & registrasi untuk Admin, Apoteker, Kurir, Pelanggan.
- Dashboard per role dengan CRUD sesuai hak akses.
- Katalog produk obat: harga, stok, deskripsi.
- Keranjang & checkout, pembayaran manual & gateway Midtrans.
- Status pesanan: proses, dikirim, selesai; refund/pembatalan bila diperlukan.
- Riwayat transaksi & laporan penjualan.
- Manajemen kurir untuk pengiriman lokal.
- Notifikasi (email/WhatsApp—email terimplementasi via queue).
- Log aktivitas pengguna.

## Teknologi
- Backend: Laravel (PHP) + Livewire.
- Frontend: Blade + Tailwind CSS + DaisyUI.
- Database: MySQL.
- Server: Apache/Nginx (local: Herd, production: VPS/Hosting).

## Modul & Dokumen Terkait
- Katalog & Produk: `docs/KATEGORI_DAN_PRODUK.md`.
- Keranjang: `docs/CART_SYSTEM.md`.
- Avatar Dinamis: `docs/avatar-system.md`.
- Email Queue: `docs/email-queue-system.md`.
- Pembayaran Midtrans: `docs/PAYMENT_GATEWAY_MIDTRANS.md`.
- Deployment: `docs/DEPLOYMENT_GUIDE.md`.
- Pengujian Sistem Resep: `docs/PRESCRIPTION_SYSTEM_TESTING.md`.

## Alur Penggunaan (Pelanggan)
1. Registrasi & verifikasi email.
2. Jelajah katalog, tambah ke keranjang.
3. Checkout di `/checkout`.
4. Pembayaran: diarahkan ke Payment Link Midtrans.
5. Setelah bayar, status pesanan diperbarui otomatis via callback.
6. Lacak status di halaman `Orders`/detail pesanan.

## Alur (Admin)
- Kelola produk, kategori, stok, harga.
- Lihat & kelola pesanan, refund, laporan penjualan.
- Atur konfigurasi toko dan notifikasi email.

## Alur (Apoteker)
- Review resep, konfirmasi, buat pesanan dari resep.
- Pantau pesanan dan koordinasi dengan kurir.

## Alur (Kurir)
- Lihat daftar pengantaran, detail rute, update status pengiriman.

## Keamanan & Praktik Baik
- PSR-12 untuk PHP, komentar/dokumentasi di public methods.
- Middleware: `auth`, `verified`, `user.status`, `role:<role>`.
- CSRF dikecualikan hanya untuk rute yang membutuhkan (notification Midtrans).
- Validasi input & sanitasi data; gunakan 3DS untuk kartu.

## Integrasi Pembayaran
- Direkomendasikan menggunakan Payment Links (lihat dokumen Payment Gateway).
- SNAP legacy tersedia namun bertahap akan dipensiunkan.

## Notifikasi Email
- Asinkron via Laravel Queue (driver database).
- Job utama: `SendEmailNotification`.
- Service util: `EmailNotificationService` dengan statistik dan antrean.

## Troubleshooting & Dukungan
- Lihat `storage/logs/laravel.log` untuk error.
- Gunakan dokumen modul terkait untuk detail spesifik.