<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        /* Reset styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8fafc;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .header h1 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 16px;
            opacity: 0.9;
        }

        /* Content */
        .content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #374151;
        }

        .message {
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 30px;
            color: #4b5563;
        }

        /* Order Details */
        .order-details {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
        }

        .order-details h3 {
            color: #10b981;
            font-size: 18px;
            margin-bottom: 15px;
            border-bottom: 2px solid #10b981;
            padding-bottom: 8px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #374151;
        }

        .detail-value {
            color: #6b7280;
            text-align: right;
        }

        /* Prescription Info */
        .prescription-info {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }

        .prescription-info h4 {
            color: #1e40af;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .prescription-info p {
            color: #1e3a8a;
            font-size: 14px;
        }

        /* Order Items */
        .order-items {
            margin: 25px 0;
        }

        .order-items h4 {
            color: #374151;
            font-size: 16px;
            margin-bottom: 15px;
        }

        .item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .item:last-child {
            border-bottom: none;
        }

        .item-info {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
        }

        .item-details {
            font-size: 14px;
            color: #6b7280;
        }

        .item-total {
            font-weight: 600;
            color: #10b981;
        }

        /* Total */
        .total-section {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
        }

        .total-final {
            border-top: 2px solid #10b981;
            padding-top: 15px;
            margin-top: 15px;
            font-size: 18px;
            font-weight: bold;
            color: #10b981;
        }

        /* Action Button */
        .action-section {
            text-align: center;
            margin: 40px 0;
        }

        .action-button {
            display: inline-block;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.3);
        }

        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(16, 185, 129, 0.4);
        }

        /* Additional Info */
        .additional-info {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }

        .additional-info h4 {
            color: #92400e;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .additional-info p {
            color: #78350f;
            font-size: 14px;
        }

        /* Footer */
        .footer {
            background-color: #f3f4f6;
            padding: 30px 20px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .footer-content {
            max-width: 500px;
            margin: 0 auto;
        }

        .footer h3 {
            color: #10b981;
            font-size: 20px;
            margin-bottom: 15px;
        }

        .footer-text {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .footer-link {
            color: #10b981;
            text-decoration: none;
        }

        .footer-link:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .email-container {
                margin: 0;
                box-shadow: none;
            }

            .content {
                padding: 30px 20px;
            }

            .order-details,
            .prescription-info,
            .additional-info {
                padding: 20px 15px;
            }

            .detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }

            .detail-value {
                text-align: left;
            }

            .item {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .action-button {
                padding: 12px 25px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>{{ $storeSettings['store_name'] }}</h1>
            <p>{{ $title }}</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Halo {{ $order->user->name }},
            </div>

            <div class="message">
                {{ $mailMessage }}
            </div>

            <!-- Prescription Info -->
            <div class="prescription-info">
                <h4>📋 Informasi Resep</h4>
                <p>
                    <strong>ID Resep:</strong> #{{ $prescription->prescription_id }}<br>
                    <strong>Tanggal Upload:</strong> {{ $prescription->created_at->format('d M Y, H:i') }}<br>
                    <strong>Status:</strong> {{ ucfirst($prescription->status) }}
                </p>
            </div>

            <!-- Order Details -->
            <div class="order-details">
                <h3>📦 Detail Pesanan</h3>
                <div class="detail-row">
                    <span class="detail-label">Nomor Pesanan:</span>
                    <span class="detail-value">#{{ $order->order_number }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tanggal Pesanan:</span>
                    <span class="detail-value">{{ $order->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status Pesanan:</span>
                    <span class="detail-value">{{ ucfirst($order->status) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Metode Pengiriman:</span>
                    <span class="detail-value">{{ $order->shipping_type === 'delivery' ? 'Diantar' : 'Diambil di Toko' }}</span>
                </div>
            </div>

            <!-- Order Items -->
            <div class="order-items">
                <h4>🛒 Produk yang Dipesan:</h4>
                @foreach($order->items as $item)
                <div class="item">
                    <div class="item-info">
                        <div class="item-name">{{ $item->product->name }}</div>
                        <div class="item-details">
                            {{ $item->qty }} x Rp {{ number_format($item->price, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="item-total">
                        Rp {{ number_format($item->qty * $item->price, 0, ',', '.') }}
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Total -->
            <div class="total-section">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($order->delivery_fee > 0)
                <div class="total-row">
                    <span>Biaya Pengiriman:</span>
                    <span>Rp {{ number_format($order->delivery_fee, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($order->discount_amount > 0)
                <div class="total-row">
                    <span>Diskon:</span>
                    <span>-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="total-row total-final">
                    <span>Total Pembayaran:</span>
                    <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Action Button -->
            <div class="action-section">
                <a href="{{ $actionUrl }}" class="action-button">
                    {{ $actionText }}
                </a>
            </div>

            <!-- Additional Info -->
            @if($additionalInfo)
            <div class="additional-info">
                <h4>⚠️ Informasi Penting</h4>
                <p>{{ $additionalInfo }}</p>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-content">
                <h3>{{ $storeSettings['store_name'] }}</h3>
                <p class="footer-text">
                    📍 {{ $storeSettings['store_address'] }}
                </p>
                <p class="footer-text">
                    📞 {{ $storeSettings['store_phone'] }} | 
                    ✉️ <a href="mailto:{{ $storeSettings['store_email'] }}" class="footer-link">{{ $storeSettings['store_email'] }}</a>
                </p>
                <p class="footer-text">
                    Email ini dikirim secara otomatis oleh sistem {{ $storeSettings['store_name'] }}.
                </p>
                <p class="footer-text">
                    Jika Anda memiliki pertanyaan, silakan hubungi 
                    <a href="mailto:{{ $storeSettings['store_email'] }}" class="footer-link">{{ $storeSettings['store_email'] }}</a>
                </p>
                <p class="footer-text">
                    © {{ date('Y') }} {{ $storeSettings['store_name'] }}. Semua hak dilindungi.
                </p>
            </div>
        </div>
    </div>
</body>
</html>