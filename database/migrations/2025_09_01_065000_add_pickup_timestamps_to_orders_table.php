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
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('ready_for_pickup_at')->nullable()->after('ready_to_ship_at');
            $table->timestamp('picked_up_at')->nullable()->after('ready_for_pickup_at');
            $table->timestamp('completed_at')->nullable()->after('delivered_at');
            $table->string('pickup_image')->nullable()->after('receipt_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'ready_for_pickup_at', 
                'picked_up_at',
                'completed_at',
                'pickup_image'
            ]);
        });
    }
};