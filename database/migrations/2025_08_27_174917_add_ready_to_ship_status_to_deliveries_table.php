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
        // Add 'ready_to_ship' status to delivery_status enum
        DB::statement("ALTER TABLE deliveries MODIFY COLUMN delivery_status ENUM(
            'pending', 
            'ready_to_ship', 
            'in_transit', 
            'delivered', 
            'failed'
        ) DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'ready_to_ship' status from delivery_status enum
        DB::statement("ALTER TABLE deliveries MODIFY COLUMN delivery_status ENUM(
            'pending', 
            'in_transit', 
            'delivered', 
            'failed'
        ) DEFAULT 'pending'");
    }
};
