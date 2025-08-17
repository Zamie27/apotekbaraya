<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id('setting_id');
            $table->string('key')->unique();
            $table->text('value');
            $table->string('type')->default('string'); // string, number, boolean, json
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('store_settings')->insert([
            [
                'key' => 'store_name',
                'value' => 'Apotek Baraya',
                'type' => 'string',
                'description' => 'Nama toko/apotek',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'store_address',
                'value' => 'Jl. Contoh No. 123, Kota Contoh',
                'type' => 'string',
                'description' => 'Alamat lengkap toko/apotek',
                'created_at' => now(),
                'updated_at' => now()
            ],
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
            ],
            [
                'key' => 'shipping_rate_per_km',
                'value' => '2000',
                'type' => 'number',
                'description' => 'Tarif pengiriman per kilometer (dalam rupiah)',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'max_delivery_distance',
                'value' => '15',
                'type' => 'number',
                'description' => 'Jarak maksimal pengiriman (dalam kilometer)',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'free_shipping_minimum',
                'value' => '100000',
                'type' => 'number',
                'description' => 'Minimal pembelian untuk gratis ongkir (dalam rupiah)',
                'created_at' => now(),
                'updated_at' => now()
            ],
            // Google Maps API key removed - now using OpenStreetMap Nominatim
            // [
            //     'key' => 'google_maps_api_key',
            //     'value' => '',
            //     'type' => 'string',
            //     'description' => 'API Key Google Maps untuk geocoding dan distance calculation',
            //     'created_at' => now(),
            //     'updated_at' => now()
            // ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};