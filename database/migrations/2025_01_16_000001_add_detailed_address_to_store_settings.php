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
        // Add new detailed address fields to store_settings
        $newSettings = [
            [
                'key' => 'store_village',
                'value' => '',
                'type' => 'string',
                'description' => 'Desa/Kelurahan toko',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'store_district',
                'value' => '',
                'type' => 'string',
                'description' => 'Kecamatan toko',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'store_regency',
                'value' => '',
                'type' => 'string',
                'description' => 'Kabupaten/Kota toko',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'store_province',
                'value' => '',
                'type' => 'string',
                'description' => 'Provinsi toko',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'store_postal_code',
                'value' => '',
                'type' => 'string',
                'description' => 'Kode pos toko',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        // Insert new settings only if they don't exist
        foreach ($newSettings as $setting) {
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
        // Remove the detailed address settings
        $keysToRemove = [
            'store_village',
            'store_district',
            'store_regency',
            'store_province',
            'store_postal_code'
        ];

        DB::table('store_settings')
            ->whereIn('key', $keysToRemove)
            ->delete();
    }
};