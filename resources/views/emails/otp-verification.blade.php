<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - Apotek Baraya</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            color: #10b981;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .otp-code {
            background-color: #10b981;
            color: white;
            font-size: 32px;
            font-weight: bold;
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            letter-spacing: 8px;
        }
        .info-box {
            background-color: #f0f9ff;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
        }
        .warning-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🏥 Apotek Baraya</div>
            <h1>Verifikasi Email Anda</h1>
        </div>

        <p>Halo <strong>{{ $user->name }}</strong>,</p>
        
        <p>Terima kasih telah mendaftar di Apotek Baraya! Untuk melengkapi proses registrasi, silakan verifikasi alamat email Anda dengan memasukkan kode OTP berikut:</p>

        <div class="otp-code">
            {{ $otpCode }}
        </div>

        <div class="info-box">
            <strong>📋 Informasi Penting:</strong>
            <ul>
                <li>Kode OTP ini berlaku selama <strong>10 menit</strong></li>
                <li>Kode akan kedaluwarsa pada: <strong>{{ $expiresAt->format('d/m/Y H:i:s') }} WIB</strong></li>
                <li>Jangan bagikan kode ini kepada siapa pun</li>
            </ul>
        </div>

        <div class="warning-box">
            <strong>⚠️ Peringatan Keamanan:</strong><br>
            Jika Anda tidak melakukan registrasi di Apotek Baraya, abaikan email ini. Akun tidak akan dibuat tanpa verifikasi email.
        </div>

        <p>Setelah memasukkan kode OTP, Anda dapat langsung mengakses layanan Apotek Baraya dan mulai berbelanja obat-obatan dengan mudah dan aman.</p>

        <p>Jika Anda mengalami kesulitan atau memiliki pertanyaan, jangan ragu untuk menghubungi tim customer service kami.</p>

        <div class="footer">
            <p><strong>Apotek Baraya</strong><br>
            Email: support@apotekbaraya.com<br>
            Telepon: (021) 1234-5678</p>
            
            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html>