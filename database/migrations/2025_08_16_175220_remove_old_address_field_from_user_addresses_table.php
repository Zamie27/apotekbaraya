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
        Schema::table('user_addresses', function (Blueprint $table) {
            // Make detailed_address required (not nullable)
            $table->text('detailed_address')->nullable(false)->change();
            
            // Make village required (not nullable)
            $table->string('village')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            // Make detailed_address nullable again
            $table->text('detailed_address')->nullable()->change();
            
            // Make village nullable again
            $table->string('village')->nullable()->change();
        });
    }
};
