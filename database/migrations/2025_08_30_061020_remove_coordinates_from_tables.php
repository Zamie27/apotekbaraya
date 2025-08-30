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
        Schema::table('store_settings', function (Blueprint $table) {
            // Remove coordinate fields as they are no longer needed
            // Distance calculation now uses manual JSON data
            if (Schema::hasColumn('store_settings', 'store_latitude')) {
                $table->dropColumn('store_latitude');
            }
            if (Schema::hasColumn('store_settings', 'store_longitude')) {
                $table->dropColumn('store_longitude');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            // Restore coordinate fields
            $table->decimal('store_latitude', 10, 8)->nullable()->after('store_address');
            $table->decimal('store_longitude', 11, 8)->nullable()->after('store_latitude');
        });
    }
};
