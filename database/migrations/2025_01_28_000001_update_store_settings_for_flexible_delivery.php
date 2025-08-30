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
        // Add new store hours setting
        DB::table('store_settings')->insert([
            [
                'key' => 'store_hours',
                'value' => 'Senin-Sabtu: 08:00-20:00',
                'type' => 'string',
                'description' => 'Jam operasional toko untuk pickup',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Remove coordinate-related settings as they are no longer needed
        DB::table('store_settings')->whereIn('key', [
            'store_latitude',
            'store_longitude'
        ])->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove store hours setting
        DB::table('store_settings')->where('key', 'store_hours')->delete();

        // Restore coordinate settings
        DB::table('store_settings')->insert([
            [
                'key' => 'store_latitude',
                'value' => '-6.2088',
                'type' => 'string',
                'description' => 'Koordinat latitude toko (untuk perhitungan jarak)',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'store_longitude',
                'value' => '106.8456',
                'type' => 'string',
                'description' => 'Koordinat longitude toko (untuk perhitungan jarak)',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
};