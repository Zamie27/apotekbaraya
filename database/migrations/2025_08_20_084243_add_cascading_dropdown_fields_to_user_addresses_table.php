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
            // Add key fields for cascading dropdown
            $table->string('province_key')->nullable()->after('detailed_address'); // Key for province (jawa_barat)
            $table->string('regency_key')->nullable()->after('province_key'); // Key for regency (subang)
            $table->string('sub_district_key')->nullable()->after('regency_key'); // Key for sub-district
            $table->string('village_key')->nullable()->after('sub_district_key'); // Key for village
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropColumn([
                'province_key',
                'regency_key', 
                'sub_district_key',
                'village_key'
            ]);
        });
    }
};
