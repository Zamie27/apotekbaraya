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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('delivery_fee', 8, 2)->default(0);
            $table->decimal('discount_amount', 8, 2)->default(0);
            $table->decimal('total_price', 12, 2);
            $table->enum('status', [
                'created',
                'waiting_payment',
                'payment_success', 
                'waiting_confirmation',
                'pending',
                'confirmed',
                'processing',
                'ready_to_ship',
                'ready_for_pickup',
                'shipped',
                'picked_up',
                'delivered',
                'completed',
                'cancelled',
                'failed',
                'refunded'
            ])->default('created');
            $table->json('shipping_address');
            $table->text('notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->text('confirmation_note')->nullable();
            $table->timestamp('processing_at')->nullable();
            $table->timestamp('ready_to_ship_at')->nullable();
            $table->timestamp('ready_for_pickup_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('waiting_payment_at')->nullable();
            $table->timestamp('waiting_confirmation_at')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id('order_item_id');
            $table->foreignId('order_id')->constrained('orders', 'order_id');
            $table->foreignId('product_id')->constrained('products', 'product_id');
            $table->integer('qty');
            $table->decimal('price', 12, 2);
        });

        Schema::create('deliveries', function (Blueprint $table) {
            $table->id('delivery_id');
            $table->foreignId('order_id')->constrained('orders', 'order_id');
            $table->foreignId('courier_id')->nullable()->constrained('users', 'user_id');
            $table->json('delivery_address');
            $table->decimal('delivery_fee', 8, 2)->default(0);
            $table->enum('delivery_type', ['regular', 'express', 'standard'])->default('regular');
            $table->timestamp('estimated_delivery')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('delivery_notes')->nullable();
            $table->string('delivery_photo')->nullable();
            $table->enum('status', ['pending', 'assigned', 'picked_up', 'in_transit', 'delivered', 'failed', 'ready_to_ship'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('deliveries');
    }
};
