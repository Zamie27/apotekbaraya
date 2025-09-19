<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi Akun - Apotek Baraya</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            width: 80px;
            height: 80px;
            background-color: #10b981;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .logo svg {
            width: 40px;
            height: 40px;
            color: white;
        }
        h1 {
            color: #1f2937;
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .subtitle {
            color: #6b7280;
            font-size: 16px;
            margin: 10px 0 0 0;
        }
        .content {
            margin: 30px 0;
        }
        .greeting {
            font-size: 18px;
            color: #1f2937;
            margin-bottom: 20px;
        }
        .message {
            color: #4b5563;
            font-size: 16px;
            line-height: 1.7;
            margin-bottom: 30px;
        }
        .button-container {
            text-align: center;
            margin: 40px 0;
        }
        .activate-button {
            display: inline-block;
            background-color: #10b981;
            color: white;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .activate-button:hover {
            background-color: #059669;
        }
        .alternative-link {
            margin-top: 20px;
            padding: 15px;
            background-color: #f3f4f6;
            border-radius: 6px;
            border-left: 4px solid #10b981;
        }
        .alternative-link p {
            margin: 0;
            font-size: 14px;
            color: #6b7280;
        }
        .alternative-link a {
            color: #10b981;
            word-break: break-all;
        }
        .info-box {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 16px;
            margin: 20px 0;
        }
        .info-box h3 {
            color: #1e40af;
            margin: 0 0 10px 0;
            font-size: 16px;
        }
        .info-box ul {
            margin: 0;
            padding-left: 20px;
            color: #1e40af;
        }
        .info-box li {
            margin-bottom: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .footer a {
            color: #10b981;
            text-decoration: none;
        }
        .expiry-notice {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            padding: 12px;
            margin: 20px 0;
            text-align: center;
        }
        .expiry-notice p {
            margin: 0;
            color: #92400e;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h1>Aktivasi Akun</h1>
            <p class="subtitle">Apotek Baraya - Kesehatan Keluarga Terpercaya</p>
        </div>

        <!-- Content -->
        <div class="content">
            <p class="greeting">Halo, {{ $user->name }}!</p>
            
            <div class="message">
                <p>Terima kasih telah mendaftar di <strong>Apotek Baraya</strong>. Untuk melengkapi proses registrasi dan mengaktifkan akun Anda, silakan klik tombol aktivasi di bawah ini:</p>
            </div>

            <!-- Activation Button -->
            <div class="button-container">
                <a href="{{ $verificationUrl }}" class="activate-button">
                    🚀 Aktivasi Akun Saya
                </a>
            </div>

            <!-- Alternative Link -->
            <div class="alternative-link">
                <p><strong>Tidak bisa mengklik tombol?</strong> Salin dan tempel link berikut ke browser Anda:</p>
                <a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a>
            </div>

            <!-- Expiry Notice -->
            <div class="expiry-notice">
                <p>⏰ Link aktivasi ini berlaku selama 24 jam</p>
            </div>

            <!-- Info Box -->
            <div class="info-box">
                <h3>📋 Setelah aktivasi, Anda dapat:</h3>
                <ul>
                    <li>Berbelanja obat dan produk kesehatan</li>
                    <li>Melihat riwayat pesanan dan transaksi</li>
                    <li>Mendapatkan notifikasi promo dan diskon</li>
                    <li>Menggunakan layanan konsultasi online</li>
                    <li>Menyimpan alamat pengiriman favorit</li>
                </ul>
            </div>

            <div class="message">
                <p>Jika Anda tidak merasa mendaftar di Apotek Baraya, abaikan email ini. Akun tidak akan dibuat tanpa aktivasi.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Email ini dikirim otomatis, mohon tidak membalas.</p>
            <p>Butuh bantuan? Hubungi kami di <a href="mailto:support@apotekbaraya.com">support@apotekbaraya.com</a></p>
            <p>atau WhatsApp: <a href="https://wa.me/6281234567890">+62 812-3456-7890</a></p>
            <br>
            <p>&copy; {{ date('Y') }} Apotek Baraya. Semua hak dilindungi.</p>
        </div>
    </div>
</body>
</html>