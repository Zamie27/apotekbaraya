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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id('prescription_id');
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->string('file');
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('note')->nullable();
            $table->timestamp('created_at');
        });

        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id('prescription_item_id');
            $table->foreignId('prescription_id')->constrained('prescriptions', 'prescription_id');
            $table->foreignId('product_id')->constrained('products', 'product_id');
            $table->integer('qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('prescription_items');
    }
};
