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
        Schema::table('deliveries', function (Blueprint $table) {
            $table->string('delivery_photo')->nullable()->after('delivery_notes');
            $table->enum('delivery_status', ['pending', 'in_transit', 'delivered', 'failed'])
                  ->default('pending')->after('delivery_photo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn(['delivery_photo', 'delivery_status']);
        });
    }
};
