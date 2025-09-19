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
        // Update payment method COD name to Transfer
        DB::table('payment_methods')
            ->where('code', 'cod')
            ->update(['name' => 'Transfer']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback payment method name to original COD
        DB::table('payment_methods')
            ->where('code', 'cod')
            ->update(['name' => 'Cash on Delivery (COD)']);
    }
};
