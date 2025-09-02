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
            // Add ready_for_pickup_at timestamp first
            if (!Schema::hasColumn('orders', 'ready_for_pickup_at')) {
                $table->timestamp('ready_for_pickup_at')->nullable()->after('delivered_at');
            }
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('orders', 'picked_up_at')) {
                $table->timestamp('picked_up_at')->nullable()->after('ready_for_pickup_at');
            }
            if (!Schema::hasColumn('orders', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('picked_up_at');
            }
            if (!Schema::hasColumn('orders', 'pickup_image')) {
                $table->string('pickup_image')->nullable()->after('completed_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Only drop columns that exist
            $columnsToDrop = [];
            if (Schema::hasColumn('orders', 'ready_for_pickup_at')) {
                $columnsToDrop[] = 'ready_for_pickup_at';
            }
            if (Schema::hasColumn('orders', 'picked_up_at')) {
                $columnsToDrop[] = 'picked_up_at';
            }
            if (Schema::hasColumn('orders', 'completed_at')) {
                $columnsToDrop[] = 'completed_at';
            }
            if (Schema::hasColumn('orders', 'pickup_image')) {
                $columnsToDrop[] = 'pickup_image';
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};