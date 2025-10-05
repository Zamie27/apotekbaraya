<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to avoid doctrine/dbal requirement for change()
        try {
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE `products` MODIFY `stock` INT UNSIGNED NOT NULL DEFAULT 0');
            }
        } catch (\Throwable $e) {
            // Allow migration to continue on non-MySQL or if table not present in dev
            // Consider logging in real environment
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            $driver = DB::getDriverName();
            if ($driver === 'mysql') {
                // Revert to tinyint unsigned default if needed
                DB::statement('ALTER TABLE `products` MODIFY `stock` TINYINT UNSIGNED NOT NULL DEFAULT 0');
            }
        } catch (\Throwable $e) {
            // No-op on failure
        }
    }
};