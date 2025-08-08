# Project Rules – E-Commerce Apotek Baraya

1. Framework: Laravel 12.x (with Livewire 3.x), Tailwind CSS 4.x, DaisyUI 5.x. Database: MySQL 8.x.
2. Dependencies: Alpine.js for lightweight interactivity, Composer-managed PHP packages only, npm-managed JS/CSS packages.
3. Testing framework: Laravel Pest/PHPUnit for backend, Laravel Dusk for browser testing.
4. Avoid using inline JavaScript or CSS; all styling must use Tailwind classes.
5. Avoid direct SQL queries without Laravel Query Builder or Eloquent ORM.
6. Avoid insecure APIs or deprecated Laravel helpers (e.g., old array\_\* functions).
7. All code must follow PSR-12 standard and include function-level comments in English.
8. Commit messages must be in English, describing the purpose clearly.

## Tentang Proyek

Proyek ini adalah **aplikasi e-commerce berbasis web** untuk Apotek Baraya, yang menyediakan layanan pemesanan dan pembelian obat secara online, dilengkapi sistem manajemen stok, pesanan, pembayaran, dan pengiriman.

## Fitur Utama

1. **Autentikasi & Role Management**

    - Role: Admin, Apoteker, Kurir, Pelanggan.
    - Sistem login & register per role.
    - Dashboard sesuai hak akses.

2. **Katalog Produk**

    - CRUD produk obat (nama, deskripsi, harga, stok, foto).
    - Kategori obat (bebas, resep, herbal, dsb).
    - Pencarian & filter produk.

3. **Keranjang & Checkout**

    - Tambah ke keranjang.
    - Update qty & hapus item.
    - Checkout dengan opsi pengiriman dan pembayaran.

4. **Pembayaran**

    - Manual transfer (upload bukti).
    - Payment gateway online.

5. **Pesanan & Pengiriman**

    - Status pesanan: proses, dikirim, selesai.
    - Tracking pengiriman lokal.
    - Manajemen kurir.

6. **Laporan & Analitik**

    - Laporan penjualan & transaksi.
    - Riwayat pesanan pelanggan.
    - Stok & peringatan restock.

7. **Notifikasi**
    - Email / WhatsApp untuk status pesanan & promo.
    - Log aktivitas pengguna.

## Teknologi yang Digunakan

-   **Backend:** Laravel + Livewire.
-   **Frontend:** Blade + Tailwind CSS (+ DaisyUI).
-   **Database:** MySQL.
-   **Interaktivitas:** Alpine.js untuk komponen ringan.
-   **Version Control:** GitHub.

## Standar & Workflow

-   Mengikuti PSR-12 untuk PHP.
-   Kode CSS menggunakan Tailwind CSS (utility-first).
-   Semua fungsi/public method diberi komentar.
-   Pengembangan modul per fitur.
-   Gunakan branch Git untuk setiap modul/fitur.
-   Lakukan testing fungsional per modul sebelum merge ke main branch.

## Aturan untuk AI

-   Semua saran kode harus sesuai standar di atas.
-   Gunakan bahasa Indonesia untuk penjelasan, bahasa Inggris untuk kode.
-   Sertakan dokumentasi singkat setiap kali membuat modul atau fitur baru.
-   Jangan menulis kode yang merusak struktur folder atau file yang sudah ada.
-   Berikan alternatif solusi jika memungkinkan.
-   Prioritaskan keamanan data, validasi input, dan perlindungan dari SQL Injection, XSS, CSRF.
-   Saat membuat UI, pastikan konsisten dengan style Tailwind + DaisyUI.
