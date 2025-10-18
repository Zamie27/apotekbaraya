# Payment Gateway Midtrans — Integrasi di Apotek Baraya

Dokumen ini menjelaskan integrasi Payment Gateway Midtrans pada aplikasi Apotek Baraya, meliputi konfigurasi environment, alur pembayaran (Payment Links dan SNAP legacy), rute callback/webhook, komponen kode, serta praktik terbaik keamanan dan troubleshooting.

## Ringkasan
- Integrasi menggunakan Midtrans dengan dua pendekatan: Payment Links (disarankan) dan SNAP (legacy).
- Konfigurasi diambil dari `config/services.php` dan di-derivasi pada `config/midtrans.php`.
- Rute penting: `POST /payment/notification` (callback), `GET /payment/finish`, `GET /payment/status`, halaman pembayaran `GET /payment` dan `GET /payment/snap` (legacy SNAP).
- CSRF untuk `payment/notification` sudah dikecualikan di bootstrap.

## Dependensi & Versi
- `php` `^8.2`.
- `laravel/framework` `^12.0`.
- `livewire/livewire` `^3.6`.
- SDK Midtrans tersedia melalui autoload `Midtrans\` yang mengarah ke `app/Libraries/Midtrans/`.

## Konfigurasi Environment
Atur variabel `.env` berikut:

```
MIDTRANS_SERVER_KEY="your_server_key"
MIDTRANS_CLIENT_KEY="your_client_key"
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

Sumber konfigurasi:
- `config/services.php` mengambil nilai dari `.env`.
- `config/midtrans.php` menurunkan nilai tersebut dan menyediakan base URL Sandbox/Production untuk API dan SNAP.

## Rute & Middleware
- `POST /payment/notification` → `WebhookController@midtransNotification` (CSRF dikecualikan).
- `GET /payment/finish` → `PaymentController@finish`.
- `GET /payment/status` → cek status pembayaran (middleware: `auth`, `verified`, `user.status`).
- `GET /payment` → halaman pembayaran Livewire.
- `GET /payment/snap` → halaman pembayaran SNAP legacy.

CSRF Exemption: Lihat `bootstrap/app.php` (rute `payment/notification` dikecualikan dari verifikasi CSRF).

## Komponen Kode Terkait
- Service: `app/Services/MidtransService.php`
  - `createPaymentLink($order)` → Membuat Payment Link (disarankan) via API v1.
  - `createSnapToken($order)` → Membuat token SNAP (legacy; gunakan Payment Link bila memungkinkan).
  - `verifySignature(array $notification)` → Verifikasi signature Midtrans untuk callback.
  - `getTransactionStatus($orderId)` → Mendapatkan status transaksi.
- Frontend SNAP: `resources/views/livewire/payment-snap.blade.php` menggunakan `snapJsUrl` dan `clientKey`.
- Livewire: `app/Livewire/Checkout.php`, `app/Livewire/Pelanggan/OrderDetail.php` (membuat token SNAP saat pending; disarankan transisi ke Payment Links).
- Routes: `routes/web.php` mendefinisikan semua rute pembayaran.

## Alur Pembayaran yang Direkomendasikan (Payment Links)
1. Pelanggan checkout di `GET /checkout` (komponen `Checkout`).
2. Backend memanggil `MidtransService::createPaymentLink($order)` dan menerima `payment_url`.
3. Aplikasi mengarahkan pelanggan ke `payment_url` Midtrans.
4. Setelah pembayaran, Midtrans memanggil `POST /payment/notification` dan/atau mengarahkan ke `GET /payment/finish`.
5. Sistem memverifikasi signature (`verifySignature`) dan memperbarui status `Order` secara idempotent.
6. Pelanggan melihat status di `GET /payment/status` atau halaman detail pesanan.

## Alur SNAP (Legacy)
1. Komponen `PaymentSnap` atau `OrderDetail` memanggil `createSnapToken`.
2. Frontend memuat `snap.js` menggunakan `clientKey` dan memicu UI pembayaran.
3. Callback bekerja serupa; gunakan verifikasi signature untuk memastikan keabsahan.

## Callback & Verifikasi Signature
- Signature dihitung: `sha512(order_id + status_code + gross_amount + server_key)`.
- Cocokkan dengan `signature_key` dari notifikasi.
- Pastikan pembaruan status pesanan idempotent untuk menghindari duplikasi.

## Status Transaksi & Penanganan
- Gunakan `getTransactionStatus($orderId)` untuk polling atau validasi.
- Mapping status umum: `capture`, `settlement`, `pending`, `deny`, `cancel`, `expire`, `refund`.
- Terapkan kebijakan pembatalan/pengembalian dana di `App\Models\Order` sesuai logika bisnis.

## Praktik Terbaik Keamanan
- Simpan `MIDTRANS_SERVER_KEY` hanya di server, jangan di frontend.
- Aktifkan `is_3ds` untuk kartu; gunakan `is_sanitized` untuk payload lebih aman.
- Validasi `gross_amount` sebagai integer; hindari rounding issues.
- Log semua notifikasi (`Log::info`/`Log::error`) untuk audit.
- Terapkan idempotency pada handler notifikasi untuk keamanan dan konsistensi.

## Testing (Sandbox)
- Set `MIDTRANS_IS_PRODUCTION=false` untuk Sandbox.
- Gunakan kartu uji dari dokumentasi Midtrans.
- Uji penuh alur: Checkout → Payment Link → Callback → Update Status.

## Troubleshooting
- 401 Unauthorized: cek `server_key` dan header Authorization (Basic).
- Signature mismatch: pastikan `order_id`, `status_code`, `gross_amount`, dan `server_key` konsisten.
- Callback tidak masuk: verifikasi URL publik `POST /payment/notification` dapat diakses.
- SNAP UI tidak muncul: cek `clientKey` dan `snapJsUrl`.

## Referensi
- Lihat `config/services.php` dan `config/midtrans.php` untuk konfigurasi.
- Rute: `routes/web.php` (payment & webhook).
- Service: `app/Services/MidtransService.php`.
- Terkait Email & Notifikasi: `docs/email-queue-system.md`.