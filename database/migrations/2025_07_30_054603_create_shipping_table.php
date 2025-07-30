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
        Schema::create('shipping_statuses', function (Blueprint $table) {
            $table->id('shipping_status_id');
            $table->foreignId('order_id')->constrained('orders', 'order_id');
            $table->enum('status', ['pending', 'picked_up', 'in_transit', 'delivered', 'failed']);
            $table->text('notes')->nullable();
            $table->timestamp('updated_at');
        });

        Schema::create('carts', function (Blueprint $table) {
            $table->id('cart_id');
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->foreignId('product_id')->constrained('products', 'product_id');
            $table->integer('quantity');
            $table->timestamps();
            $table->unique(['user_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_statuses');
        Schema::dropIfExists('carts');
    }
};
