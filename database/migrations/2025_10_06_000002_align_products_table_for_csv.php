<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            // Drop deprecated column if exists
            if (Schema::hasColumn('products', 'weight')) {
                $table->dropColumn('weight');
            }

            // Ensure discount_price exists for percentage-based discounts
            if (!Schema::hasColumn('products', 'discount_price')) {
                $table->decimal('discount_price', 10, 2)->nullable()->after('price');
            }

            // Ensure unit exists and has a sensible default
            if (!Schema::hasColumn('products', 'unit')) {
                $table->string('unit', 20)->default('pcs')->after('is_active');
            }

            // Ensure specifications JSON column exists
            if (!Schema::hasColumn('products', 'specifications')) {
                $table->json('specifications')->nullable()->after('unit');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            // Re-add weight for rollback scenarios
            if (!Schema::hasColumn('products', 'weight')) {
                $table->decimal('weight', 10, 2)->nullable()->after('is_active');
            }

            // Drop columns added in up()
            if (Schema::hasColumn('products', 'discount_price')) {
                $table->dropColumn('discount_price');
            }
            if (Schema::hasColumn('products', 'unit')) {
                $table->dropColumn('unit');
            }
            if (Schema::hasColumn('products', 'specifications')) {
                $table->dropColumn('specifications');
            }
        });
    }
};