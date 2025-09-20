<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Apotek Baraya</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #374151;
        }
        .message {
            margin-bottom: 30px;
            color: #6b7280;
            line-height: 1.7;
        }
        .reset-button {
            display: inline-block;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            transition: all 0.3s ease;
        }
        .reset-button:hover {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            transform: translateY(-1px);
        }
        .alternative-link {
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
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
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 0;
            font-size: 12px;
            color: #9ca3af;
        }
        .security-notice {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .security-notice p {
            margin: 0;
            font-size: 14px;
            color: #92400e;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🏥 Apotek Baraya</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Reset Password Anda</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Halo, {{ $user->name }}!
            </div>

            <div class="message">
                <p>Kami menerima permintaan untuk mereset password akun Anda di Apotek Baraya. Jika Anda yang meminta reset password, silakan klik tombol di bawah ini untuk membuat password baru.</p>
            </div>

            <!-- Reset Button -->
            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="reset-button">
                    🔑 Reset Password Saya
                </a>
            </div>

            <!-- Alternative Link -->
            <div class="alternative-link">
                <p><strong>Tidak bisa klik tombol di atas?</strong></p>
                <p>Salin dan tempel link berikut ke browser Anda:</p>
                <p><a href="{{ $resetUrl }}">{{ $resetUrl }}</a></p>
            </div>

            <!-- Security Notice -->
            <div class="security-notice">
                <p><strong>⚠️ Penting untuk Keamanan:</strong></p>
                <p>• Link ini hanya berlaku selama 24 jam</p>
                <p>• Jika Anda tidak meminta reset password, abaikan email ini</p>
                <p>• Jangan bagikan link ini kepada siapa pun</p>
            </div>

            <div class="message">
                <p>Jika Anda tidak meminta reset password, silakan abaikan email ini. Akun Anda tetap aman dan tidak ada perubahan yang akan dilakukan.</p>
                
                <p>Jika Anda memiliki pertanyaan atau membutuhkan bantuan, jangan ragu untuk menghubungi tim support kami.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} Apotek Baraya. Semua hak dilindungi.</p>
            <p>Email ini dikirim secara otomatis, mohon jangan membalas email ini.</p>
        </div>
    </div>
</body>
</html>