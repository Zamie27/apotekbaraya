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
            // Remove coordinate fields as distance calculation now uses JSON data
            // Check if columns exist before dropping them
            if (Schema::hasColumn('user_addresses', 'latitude')) {
                $table->dropColumn('latitude');
            }
            if (Schema::hasColumn('user_addresses', 'longitude')) {
                $table->dropColumn('longitude');
            }
            if (Schema::hasColumn('user_addresses', 'coordinate_source')) {
                $table->dropColumn('coordinate_source');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            // Restore coordinate fields
            $table->decimal('latitude', 10, 8)->nullable()->after('postal_code');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->enum('coordinate_source', ['manual', 'geocoding'])->default('geocoding')->after('longitude');
        });
    }
};