<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil Dibuat - Apotek Baraya</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2563eb;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f8fafc;
            padding: 30px;
            border: 1px solid #e2e8f0;
        }
        .footer {
            background-color: #64748b;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 8px 8px;
        }
        .info-box {
            background-color: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            border-left: 4px solid #2563eb;
        }
        .order-items {
            background-color: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .item:last-child {
            border-bottom: none;
            font-weight: bold;
        }
        .btn {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .status-badge {
            background-color: #10b981;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏥 Apotek Baraya</h1>
        <p>Pesanan Berhasil Dibuat dari Resep</p>
    </div>

    <div class="content">
        <h2>Halo {{ $order->user->name }},</h2>
        
        <p>Kabar baik! Resep Anda telah diproses dan pesanan berhasil dibuat oleh apoteker kami.</p>

        <div class="info-box">
            <h3>📋 Informasi Resep</h3>
            <p><strong>Nomor Resep:</strong> {{ $prescription->prescription_number }}</p>
            <p><strong>Nama Pasien:</strong> {{ $prescription->patient_name }}</p>
            <p><strong>Nama Dokter:</strong> {{ $prescription->doctor_name }}</p>
            <p><strong>Status:</strong> <span class="status-badge">{{ ucfirst($prescription->status) }}</span></p>
        </div>

        <div class="info-box">
            <h3>🛒 Informasi Pesanan</h3>
            <p><strong>Nomor Pesanan:</strong> {{ $order->order_number }}</p>
            <p><strong>Tanggal Pesanan:</strong> {{ $order->created_at->format('d F Y, H:i') }}</p>
            <p><strong>Status Pesanan:</strong> <span class="status-badge">{{ ucfirst($order->status) }}</span></p>
            <p><strong>Status Pembayaran:</strong> <span class="status-badge">{{ ucfirst($order->payment_status) }}</span></p>
        </div>

        <div class="order-items">
            <h3>📦 Detail Pesanan</h3>
            @foreach($order->orderItems as $item)
            <div class="item">
                <div>
                    <strong>{{ $item->product->name }}</strong><br>
                    <small>{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</small>
                </div>
                <div>
                    <strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong>
                </div>
            </div>
            @endforeach
            <div class="item">
                <div><strong>Total Pesanan</strong></div>
                <div><strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></div>
            </div>
        </div>

        @if($order->notes)
        <div class="info-box">
            <h3>📝 Catatan Pesanan</h3>
            <p>{{ $order->notes }}</p>
        </div>
        @endif

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('pelanggan.orders.show', $order->order_id) }}" class="btn">Lihat Detail Pesanan</a>
        </div>

        <div style="background-color: #fef3c7; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <h4 style="color: #92400e; margin: 0 0 10px 0;">⚠️ Langkah Selanjutnya:</h4>
            <ul style="color: #92400e; margin: 0; padding-left: 20px;">
                <li>Silakan lakukan pembayaran untuk pesanan ini</li>
                <li>Setelah pembayaran dikonfirmasi, pesanan akan diproses</li>
                <li>Anda akan menerima notifikasi update status pesanan</li>
            </ul>
        </div>

        <p>Terima kasih telah mempercayakan kebutuhan kesehatan Anda kepada Apotek Baraya.</p>
        
        <p>Salam sehat,<br>
        <strong>Tim Apotek Baraya</strong></p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} Apotek Baraya. Semua hak dilindungi.</p>
        <p>📧 Email: info@apotekbaraya.com | 📞 Telepon: (021) 1234-5678</p>
    </div>
</body>
</html>