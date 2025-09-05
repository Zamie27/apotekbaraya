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
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('failed_at')->nullable()->after('cancelled_at');
            $table->text('failure_reason')->nullable()->after('failed_at');
        });

        // Update the status enum to include 'failed'
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
            'created',
            'waiting_payment',
            'payment_success', 
            'waiting_confirmation',
            'pending',
            'confirmed',
            'processing',
            'ready_to_ship',
            'ready_for_pickup',
            'shipped',
            'picked_up',
            'delivered',
            'completed',
            'cancelled',
            'failed',
            'refunded'
        ) DEFAULT 'created'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['failed_at', 'failure_reason']);
        });

        // Revert the status enum to previous values (without 'failed')
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
            'created',
            'waiting_payment',
            'payment_success', 
            'waiting_confirmation',
            'pending',
            'confirmed',
            'processing',
            'ready_to_ship',
            'ready_for_pickup',
            'shipped',
            'picked_up',
            'delivered',
            'completed',
            'cancelled',
            'refunded'
        ) DEFAULT 'created'");
    }
};