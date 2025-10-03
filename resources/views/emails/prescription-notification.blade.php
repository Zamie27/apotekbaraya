<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
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
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
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

        /* Prescription Details */
        .prescription-details {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
        }

        .prescription-details h3 {
            color: #3b82f6;
            font-size: 18px;
            margin-bottom: 15px;
            border-bottom: 2px solid #3b82f6;
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

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        /* Action Button */
        .action-button {
            display: inline-block;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            text-align: center;
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
        }

        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(59, 130, 246, 0.4);
        }

        /* Additional Info */
        .additional-info {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }

        .additional-info p {
            color: #1e40af;
            font-size: 14px;
            margin: 0;
        }

        /* Footer */
        .footer {
            background-color: #f3f4f6;
            padding: 30px 20px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .footer p {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .footer .store-info {
            color: #374151;
            font-weight: 600;
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
            
            .prescription-details {
                padding: 20px;
            }
            
            .detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
            
            .detail-value {
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>{{ $title }}</h1>
            <p>Apotek Baraya - Sistem Manajemen Resep</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Halo, Apoteker!
            </div>

            <div class="message">
                {{ $mailMessage }}
            </div>

            <!-- Prescription Details -->
            <div class="prescription-details">
                <h3>📋 Detail Resep</h3>
                
                <div class="detail-row">
                    <span class="detail-label">Nomor Resep:</span>
                    <span class="detail-value">{{ $prescription->prescription_number }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Pelanggan:</span>
                    <span class="detail-value">{{ $prescription->user->name }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Email Pelanggan:</span>
                    <span class="detail-value">{{ $prescription->user->email }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Nama Dokter:</span>
                    <span class="detail-value">{{ $prescription->doctor_name }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Nama Pasien:</span>
                    <span class="detail-value">{{ $prescription->patient_name }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Metode Pengambilan:</span>
                    <span class="detail-value">
                        {{ $prescription->delivery_method === 'delivery' ? 'Kirim ke Alamat' : 'Ambil di Apotek' }}
                    </span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">
                        <span class="status-badge status-pending">Menunggu Tinjauan</span>
                    </span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Tanggal Upload:</span>
                    <span class="detail-value">{{ $prescription->created_at->format('d/m/Y H:i') }} WIB</span>
                </div>
                
                @if($prescription->notes)
                <div class="detail-row">
                    <span class="detail-label">Catatan:</span>
                    <span class="detail-value">{{ $prescription->notes }}</span>
                </div>
                @endif
            </div>

            <!-- Action Button -->
            <div style="text-align: center;">
                <a href="{{ $actionUrl }}" class="action-button">
                    {{ $actionText }}
                </a>
            </div>

            <!-- Additional Info -->
            @if($additionalInfo)
            <div class="additional-info">
                <p>{{ $additionalInfo }}</p>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="store-info">{{ \App\Models\StoreSetting::get('store_name', 'Apotek Baraya') }}</p>
            <p>{{ \App\Models\StoreSetting::get('store_address', 'Jl. Raya Apotek No. 123') }}</p>
            <p>{{ \App\Models\StoreSetting::get('store_phone', '(021) 123-4567') }} | {{ \App\Models\StoreSetting::get('store_email', 'info@apotekbaraya.com') }}</p>
            <p style="margin-top: 15px; font-size: 12px;">
                Email ini dikirim secara otomatis oleh sistem. Mohon tidak membalas email ini.
            </p>
        </div>
    </div>
</body>
</html>