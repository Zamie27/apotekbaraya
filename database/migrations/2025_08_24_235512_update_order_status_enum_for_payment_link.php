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
            // Add payment link related fields if they don't exist
            if (!Schema::hasColumn('orders', 'payment_link_id')) {
                $table->string('payment_link_id')->nullable()->after('total_price');
            }
            if (!Schema::hasColumn('orders', 'payment_url')) {
                $table->string('payment_url')->nullable()->after('payment_link_id');
            }
            
            // Add status column if it doesn't exist
            if (!Schema::hasColumn('orders', 'status')) {
                $table->enum('status', [
                    'created',
                    'waiting_payment',
                    'payment_success', 
                    'waiting_confirmation',
                    'pending',
                    'confirmed',
                    'processing',
                    'shipped',
                    'delivered',
                    'cancelled',
                    'refunded'
                ])->default('created')->after('payment_url');
            } else {
                // Update existing enum to include new payment link statuses
                DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
                    'created',
                    'waiting_payment',
                    'payment_success', 
                    'waiting_confirmation',
                    'pending',
                    'confirmed',
                    'processing',
                    'shipped',
                    'delivered',
                    'cancelled',
                    'refunded'
                ) DEFAULT 'created'");
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Remove payment link fields if they exist
            if (Schema::hasColumn('orders', 'payment_link_id')) {
                $table->dropColumn('payment_link_id');
            }
            if (Schema::hasColumn('orders', 'payment_url')) {
                $table->dropColumn('payment_url');
            }
            
            // Remove status column if it exists
            if (Schema::hasColumn('orders', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
