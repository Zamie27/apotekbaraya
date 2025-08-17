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
            $table->enum('shipping_type', ['pickup', 'delivery'])->default('pickup')->after('status');
            $table->decimal('shipping_distance', 8, 2)->nullable()->after('delivery_fee');
            $table->boolean('is_free_shipping')->default(false)->after('shipping_distance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_type', 'shipping_distance', 'is_free_shipping']);
        });
    }
};