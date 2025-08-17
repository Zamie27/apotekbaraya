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
        Schema::table('user_addresses', function (Blueprint $table) {
            // Add detailed address fields for better geocoding accuracy
            $table->string('village')->nullable()->after('phone'); // Desa
            $table->string('sub_district')->nullable()->after('village'); // Kecamatan (rename from district)
            $table->string('regency')->nullable()->after('sub_district'); // Kabupaten
            $table->string('province')->nullable()->after('regency'); // Provinsi
            $table->text('detailed_address')->nullable()->after('province'); // Alamat lengkap spesifik untuk kurir
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropColumn([
                'village',
                'sub_district', 
                'regency',
                'province',
                'detailed_address'
            ]);
        });
    }
};
