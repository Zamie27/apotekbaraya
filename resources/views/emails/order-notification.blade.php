<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Notifikasi Pesanan - Apotek Baraya' }}</title>
    <style>
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
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .header p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
        }
        
        .status-waiting-payment {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .status-confirmed {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .status-processing {
            background-color: #e0e7ff;
            color: #3730a3;
        }
        
        .status-ready {
            background-color: #f3e8ff;
            color: #6b21a8;
        }
        
        .status-shipped {
            background-color: #fce7f3;
            color: #be185d;
        }
        
        .status-completed {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .message-title {
            font-size: 24px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 16px;
        }
        
        .message-content {
            font-size: 16px;
            color: #4b5563;
            margin-bottom: 30px;
            line-height: 1.7;
        }
        
        .order-card {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        
        .order-number {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
        }
        
        .order-date {
            font-size: 14px;
            color: #6b7280;
        }
        
        .order-details {
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .detail-label {
            font-weight: 500;
            color: #374151;
        }
        
        .detail-value {
            color: #6b7280;
        }
        
        .total-row {
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
            margin-top: 10px;
            font-weight: 600;
            font-size: 16px;
        }
        
        .action-button {
            display: inline-block;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            margin: 20px 0;
        }
        
        .action-button:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
        }
        
        .footer {
            background-color: #f9fafb;
            padding: 30px 20px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        
        .footer p {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 10px;
        }
        
        .contact-info {
            font-size: 12px;
            color: #9ca3af;
        }
        
        @media (max-width: 600px) {
            .email-container {
                margin: 0;
                border-radius: 0;
            }
            
            .content {
                padding: 20px 15px;
            }
            
            .order-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .detail-row {
                flex-direction: column;
                margin-bottom: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>🏥 Apotek Baraya</h1>
            <p>Kesehatan Keluarga, Prioritas Utama</p>
        </div>
        
        <!-- Content -->
        <div class="content">
            <!-- Status Badge -->
            <div class="status-badge status-{{ str_replace(['_', ' '], '-', strtolower($order->status)) }}">
                {{ $statusLabel }}
            </div>
            
            <!-- Message Title -->
            <h2 class="message-title">{{ $title }}</h2>
            
            <!-- Message Content -->
            <div class="message-content">
                {!! $mailMessage !!}
            </div>
            
            <!-- Order Details Card -->
            <div class="order-card">
                <div class="order-header">
                    <div class="order-number">Pesanan #{{ $order->order_id }}</div>
                    <div class="order-date">{{ $order->created_at->format('d M Y, H:i') }}</div>
                </div>
                
                <div class="order-details">
                    <div class="detail-row">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value">{{ $order->status_label }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Metode Pengiriman:</span>
                        <span class="detail-value">{{ $order->shipping_type_label }}</span>
                    </div>
                    @if($order->shipping_type === 'delivery' && $order->shipping_address)
                    <div class="detail-row">
                        <span class="detail-label">Alamat Pengiriman:</span>
                        <span class="detail-value">
                            {{ $order->shipping_address['address'] ?? '' }}, 
                            {{ $order->shipping_address['city'] ?? '' }}, 
                            {{ $order->shipping_address['postal_code'] ?? '' }}
                        </span>
                    </div>
                    @endif
                    <div class="detail-row">
                        <span class="detail-label">Subtotal:</span>
                        <span class="detail-value">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if($order->delivery_fee > 0)
                    <div class="detail-row">
                        <span class="detail-label">Ongkos Kirim:</span>
                        <span class="detail-value">Rp {{ number_format($order->delivery_fee, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($order->discount_amount > 0)
                    <div class="detail-row">
                        <span class="detail-label">Diskon:</span>
                        <span class="detail-value">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="detail-row total-row">
                        <span class="detail-label">Total:</span>
                        <span class="detail-value">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Action Button (if provided) -->
            @if(isset($actionUrl) && isset($actionText))
            <div style="text-align: center;">
                <a href="{{ $actionUrl }}" class="action-button">{{ $actionText }}</a>
            </div>
            @endif
            
            <!-- Additional Info (if provided) -->
            @if(isset($additionalInfo))
            <div style="background-color: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 15px; margin-top: 20px;">
                <p style="margin: 0; color: #1e40af; font-size: 14px;">
                    <strong>ℹ️ Informasi Tambahan:</strong><br>
                    {!! $additionalInfo !!}
                </p>
            </div>
            @endif
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p><strong>Terima kasih telah mempercayai {{ $storeSettings['store_name'] }}!</strong></p>
            <p>Jika ada pertanyaan, silakan hubungi kami:</p>
            <div class="contact-info">
                📞 Telepon: {{ $storeSettings['store_phone'] }}<br>
                📧 Email: {{ $storeSettings['store_email'] }}<br>
                📍 Alamat: {{ $storeSettings['store_address'] }}
            </div>
        </div>
    </div>
</body>
</html>