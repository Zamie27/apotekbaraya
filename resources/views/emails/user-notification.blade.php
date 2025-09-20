<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['subject'] ?? 'Notifikasi Apotek Baraya' }}</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        
        .notification-type {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
        }
        
        .type-user-created {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .type-user-updated {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .type-user-deleted {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .type-login-attempt {
            background-color: #fef3c7;
            color: #92400e;
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
        
        .details-card {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .details-title {
            font-size: 18px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 12px;
        }
        
        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .detail-item:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 500;
            color: #6b7280;
        }
        
        .detail-value {
            font-weight: 600;
            color: #1f2937;
        }
        
        .changes-list {
            background-color: #fef7ff;
            border: 1px solid #e879f9;
            border-radius: 6px;
            padding: 15px;
            margin: 15px 0;
        }
        
        .changes-title {
            font-size: 14px;
            font-weight: 600;
            color: #a21caf;
            margin-bottom: 8px;
        }
        
        .change-item {
            font-size: 14px;
            color: #7c2d12;
            margin: 4px 0;
        }
        
        .footer {
            background-color: #f3f4f6;
            padding: 30px 20px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        
        .footer-text {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 10px;
        }
        
        .footer-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .footer-link:hover {
            text-decoration: underline;
        }
        
        .timestamp {
            font-size: 12px;
            color: #9ca3af;
            font-style: italic;
            margin-top: 20px;
            text-align: center;
        }
        
        @media (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 8px;
            }
            
            .content {
                padding: 30px 20px;
            }
            
            .header {
                padding: 25px 15px;
            }
            
            .header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>🏥 Apotek Baraya</h1>
            <p>Sistem Notifikasi Otomatis</p>
        </div>
        
        <!-- Content -->
        <div class="content">
            <!-- Notification Type Badge -->
            <span class="notification-type type-{{ str_replace('_', '-', $notificationType) }}">
                {{ ucwords(str_replace('_', ' ', $notificationType)) }}
            </span>
            
            <!-- Message Title -->
            <h2 class="message-title">
                @switch($notificationType)
                    @case('user_created')
                        👤 Akun Pengguna Baru Dibuat
                        @break
                    @case('user_updated')
                        ✏️ Data Pengguna Diperbarui
                        @break
                    @case('user_deleted')
                        🗑️ Pengguna Dihapus
                        @break
                    @case('login_attempt')
                        🔐 Percobaan Login Terdeteksi
                        @break
                    @default
                        📧 Notifikasi Sistem
                @endswitch
            </h2>
            
            <!-- Message Content -->
            <div class="message-content">
                @switch($notificationType)
                    @case('user_created')
                        <p>Sebuah akun pengguna baru telah berhasil dibuat dalam sistem Apotek Baraya. Berikut adalah detail informasinya:</p>
                        @break
                    @case('user_updated')
                        <p>Data pengguna telah diperbarui dalam sistem. Silakan tinjau perubahan yang telah dilakukan:</p>
                        @break
                    @case('user_deleted')
                        <p>Sebuah akun pengguna telah dihapus dari sistem. Berikut adalah detail pengguna yang dihapus:</p>
                        @break
                    @case('login_attempt')
                        <p>Sistem mendeteksi adanya percobaan login yang mencurigakan. Harap periksa detail berikut:</p>
                        @break
                    @default
                        <p>Anda menerima notifikasi dari sistem Apotek Baraya.</p>
                @endswitch
            </div>
            
            <!-- User Details -->
            @if(isset($data['new_user']) || isset($data['updated_user']) || isset($data['deleted_user']))
                <div class="details-card">
                    <h3 class="details-title">📋 Detail Pengguna</h3>
                    
                    @php
                        $user = $data['new_user'] ?? $data['updated_user'] ?? $data['deleted_user'] ?? null;
                    @endphp
                    
                    @if($user)
                        <div class="detail-item">
                            <span class="detail-label">ID Pengguna:</span>
                            <span class="detail-value">#{{ $user['id'] }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Nama:</span>
                            <span class="detail-value">{{ $user['name'] }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Email:</span>
                            <span class="detail-value">{{ $user['email'] }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Role:</span>
                            <span class="detail-value">{{ ucfirst($user['role']) }}</span>
                        </div>
                    @endif
                </div>
            @endif
            
            <!-- Action By Details -->
            @if(isset($data['created_by']) || isset($data['updated_by']) || isset($data['deleted_by']))
                <div class="details-card">
                    <h3 class="details-title">👨‍💼 Dilakukan Oleh</h3>
                    
                    @php
                        $actionBy = $data['created_by'] ?? $data['updated_by'] ?? $data['deleted_by'] ?? null;
                    @endphp
                    
                    @if($actionBy)
                        <div class="detail-item">
                            <span class="detail-label">Nama:</span>
                            <span class="detail-value">{{ is_array($actionBy) ? $actionBy['name'] : $actionBy }}</span>
                        </div>
                        @if(is_array($actionBy) && isset($actionBy['email']))
                            <div class="detail-item">
                                <span class="detail-label">Email:</span>
                                <span class="detail-value">{{ $actionBy['email'] }}</span>
                            </div>
                        @endif
                    @endif
                </div>
            @endif
            
            <!-- Changes Details -->
            @if(isset($data['changes']) && !empty($data['changes']))
                <div class="changes-list">
                    <div class="changes-title">🔄 Perubahan yang Dilakukan:</div>
                    @foreach($data['changes'] as $field => $change)
                        <div class="change-item">
                            <strong>{{ ucfirst($field) }}:</strong> 
                            {{ $change['old'] ?? 'N/A' }} → {{ $change['new'] ?? 'N/A' }}
                        </div>
                    @endforeach
                </div>
            @endif
            
            <!-- Login Attempt Details -->
            @if($notificationType === 'login_attempt')
                <div class="details-card">
                    <h3 class="details-title">🔍 Detail Percobaan Login</h3>
                    <div class="detail-item">
                        <span class="detail-label">Email:</span>
                        <span class="detail-value">{{ $data['email'] ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">IP Address:</span>
                        <span class="detail-value">{{ $data['ip_address'] ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">User Agent:</span>
                        <span class="detail-value">{{ $data['user_agent'] ?? 'N/A' }}</span>
                    </div>
                </div>
            @endif
            
            <!-- Timestamp -->
            <div class="timestamp">
                📅 {{ $data['created_at'] ?? $data['updated_at'] ?? $data['deleted_at'] ?? $data['attempted_at'] ?? now()->format('d/m/Y H:i:s') }}
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p class="footer-text">
                Email ini dikirim secara otomatis oleh sistem Apotek Baraya.
            </p>
            <p class="footer-text">
                Jika Anda memiliki pertanyaan, silakan hubungi 
                <a href="mailto:admin@apotekbaraya.com" class="footer-link">admin@apotekbaraya.com</a>
            </p>
            <p class="footer-text">
                © {{ date('Y') }} Apotek Baraya. Semua hak dilindungi.
            </p>
        </div>
    </div>
</body>
</html>