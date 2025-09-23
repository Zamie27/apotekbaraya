<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add contact settings to store_settings table
        $contactSettings = [
            [
                'key' => 'store_phone',
                'value' => '(022) 1234-5678',
                'type' => 'string',
                'description' => 'Nomor telepon toko/apotek',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'store_email',
                'value' => 'info@apotekbaraya.com',
                'type' => 'string',
                'description' => 'Email kontak toko/apotek',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'store_whatsapp',
                'value' => '6281234567890',
                'type' => 'string',
                'description' => 'Nomor WhatsApp toko/apotek (format internasional)',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        // Insert contact settings only if they don't exist
        foreach ($contactSettings as $setting) {
            $exists = DB::table('store_settings')
                ->where('key', $setting['key'])
                ->exists();
            
            if (!$exists) {
                DB::table('store_settings')->insert($setting);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the contact settings
        $keysToRemove = [
            'store_phone',
            'store_email',
            'store_whatsapp'
        ];

        DB::table('store_settings')
            ->whereIn('key', $keysToRemove)
            ->delete();
    }
};
