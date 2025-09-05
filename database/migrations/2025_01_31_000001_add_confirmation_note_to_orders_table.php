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
            // Add confirmation_note field if it doesn't exist
            if (!Schema::hasColumn('orders', 'confirmation_note')) {
                $table->text('confirmation_note')->nullable()->after('notes');
            }
            
            // Add receipt_image field if it doesn't exist
            if (!Schema::hasColumn('orders', 'receipt_image')) {
                $table->string('receipt_image')->nullable()->after('confirmation_note');
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
            if (Schema::hasColumn('orders', 'confirmation_note')) {
                $columnsToDrop[] = 'confirmation_note';
            }
            if (Schema::hasColumn('orders', 'receipt_image')) {
                $columnsToDrop[] = 'receipt_image';
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};