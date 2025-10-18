# Panduan Deployment — Apotek Baraya

Panduan ini membahas persiapan environment, langkah build dan release, konfigurasi server (Apache/Nginx), queue worker, cache, serta variabel environment untuk menjalankan aplikasi Apotek Baraya di produksi.

## Prasyarat
- PHP `^8.2` dengan ekstensi standar (OpenSSL, PDO, Mbstring, Tokenizer, Ctype, JSON, BCMath).
- Composer `>=2.x`.
- Node.js `>=18` (disarankan `>=20`) dan npm.
- MySQL/MariaDB.
- Web server: Apache atau Nginx.
- Domain & SSL (HTTPS sangat disarankan).

## Langkah Deploy
1. Ambil kode: `git clone` atau `scp` sesuai pipeline Anda.
2. Salin `.env` dari contoh dan sesuaikan.
3. Install dependencies:
   - `composer install --no-dev --optimize-autoloader`
   - `npm ci && npm run build`
4. Generate key: `php artisan key:generate` (sekali per environment).
5. Migrasi database: `php artisan migrate --force`.
6. Link storage: `php artisan storage:link`.
7. Cache konfigurasi & route:
   - `php artisan config:cache`
   - `php artisan route:cache`
   - `php artisan view:cache`
8. Jalankan queue worker (Database driver):
   - Supervisor command: `php artisan queue:work --tries=3 --sleep=3 --timeout=120`
   - Pastikan tabel `jobs` dan `failed_jobs` tersedia (lihat migrasi Laravel standar).
9. Konfigurasikan vhost:
   - Nginx root ke `public/`, set `index.php` dan `try_files`.
   - Apache gunakan `DocumentRoot public/` dan mod_rewrite.
10. Pastikan cron/scheduler bila ada perintah berkala (opsional).

## Konfigurasi .env Utama
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=apotekbaraya
DB_USERNAME=apotek
DB_PASSWORD=strong_password

CACHE_DRIVER=file
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_user
MAIL_PASSWORD=your_pass
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@domain-anda
MAIL_FROM_NAME="Apotek Baraya"

RECAPTCHA_SITE_KEY=...
RECAPTCHA_SECRET_KEY=...

MIDTRANS_SERVER_KEY=...
MIDTRANS_CLIENT_KEY=...
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

## Catatan Queue & Email
- Default `QUEUE_CONNECTION=database` (lihat `config/queue.php`). Jalankan worker permanen via Supervisor.
- Mailer default bisa `log` pada pengembangan, ubah ke `smtp` atau layanan lain di produksi.
- Lihat `docs/email-queue-system.md` untuk arsitektur notifikasi email.

## Keamanan
- Aktifkan HTTPS dan HSTS.
- Simpan credential (Midtrans, SMTP) hanya di server.
- Batasi akses `POST /payment/notification` hanya dari Midtrans (opsional dengan IP allowlist atau token tambahan).
- Pastikan `APP_DEBUG=false` di produksi.

## Troubleshooting
- 500 Server Error: cek `storage/logs/laravel.log`.
- Asset tidak termuat: pastikan `npm run build` dan konfigurasi vhost ke `public/`.
- Queue tidak berjalan: cek Supervisor dan isi tabel `jobs`/`failed_jobs`.
- Callback Midtrans gagal: verifikasi URL publik dan signature.

## Referensi
- `config/services.php`, `config/midtrans.php`, `config/queue.php`, `config/mail.php`.
- Dokumen terkait: `docs/PAYMENT_GATEWAY_MIDTRANS.md`, `docs/email-queue-system.md`.