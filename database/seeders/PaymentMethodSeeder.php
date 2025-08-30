<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            [
                'code' => 'cod',
                'name' => 'Cash on Delivery (COD)',
                'type' => 'cash',
                'description' => 'Bayar langsung di toko saat mengambil pesanan',
                'config' => json_encode([
                    'pickup_only' => true,
                    'requires_verification' => false,
                    'min_amount' => 0,
                    'max_amount' => null
                ]),
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'code' => 'qris',
                'name' => 'QRIS (Quick Response Code Indonesian Standard)',
                'type' => 'qris',
                'description' => 'Pembayaran menggunakan QRIS - bisa dari dompet digital, mobile banking, atau aplikasi pembayaran lainnya',
                'config' => json_encode([
                    'qr_code_url' => null, // Will be set by admin
                    'merchant_id' => null, // Will be set by admin
                    'min_amount' => 1000,
                    'max_amount' => 10000000,
                    'auto_verification' => false
                ]),
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'code' => 'bank_transfer',
                'name' => 'Transfer Bank',
                'type' => 'bank_transfer',
                'description' => 'Transfer manual ke rekening bank apotek',
                'config' => json_encode([
                    'bank_accounts' => [
                        [
                            'bank_name' => 'Bank BCA',
                            'account_number' => '1234567890',
                            'account_name' => 'Apotek Baraya'
                        ],
                        [
                            'bank_name' => 'Bank Mandiri',
                            'account_number' => '0987654321',
                            'account_name' => 'Apotek Baraya'
                        ]
                    ],
                    'min_amount' => 10000,
                    'max_amount' => null,
                    'verification_required' => true
                ]),
                'is_active' => false, // Disabled by default
                'sort_order' => 3
            ],
            [
                'code' => 'e_wallet',
                'name' => 'Dompet Digital',
                'type' => 'e_wallet',
                'description' => 'Pembayaran melalui dompet digital (GoPay, OVO, DANA, ShopeePay)',
                'config' => json_encode([
                    'supported_wallets' => ['gopay', 'ovo', 'dana', 'shopeepay'],
                    'min_amount' => 1000,
                    'max_amount' => 5000000,
                    'auto_verification' => false
                ]),
                'is_active' => false, // Disabled by default
                'sort_order' => 4
            ]
        ];

        DB::table('payment_methods')->insert($paymentMethods);
    }
}