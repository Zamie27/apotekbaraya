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
        // Update the delivery_type enum to include 'standard'
        DB::statement("ALTER TABLE deliveries MODIFY COLUMN delivery_type ENUM(
            'regular',
            'express',
            'standard'
        ) DEFAULT 'regular'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the delivery_type enum to previous values (without 'standard')
        DB::statement("ALTER TABLE deliveries MODIFY COLUMN delivery_type ENUM(
            'regular',
            'express'
        ) DEFAULT 'regular'");
    }
};