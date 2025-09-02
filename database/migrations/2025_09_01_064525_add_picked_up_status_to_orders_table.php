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
        // Update the status enum to include 'picked_up' and 'completed'
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the status enum to previous values (without 'picked_up' and 'completed')
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
            'delivered',
            'cancelled',
            'refunded'
        ) DEFAULT 'created'");
    }
};
