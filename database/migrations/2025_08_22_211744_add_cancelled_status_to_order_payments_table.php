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
        Schema::table('order_payments', function (Blueprint $table) {
            // Modify the status enum to include 'cancelled'
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded', 'cancelled'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_payments', function (Blueprint $table) {
            // Revert the status enum to original values
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending')->change();
        });
    }
};
