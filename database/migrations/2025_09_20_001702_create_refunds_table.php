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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders', 'order_id')->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained('order_payments', 'payment_id')->onDelete('set null');
            $table->string('refund_key')->unique(); // Unique identifier for refund
            $table->string('midtrans_transaction_id')->nullable(); // Midtrans transaction ID
            $table->decimal('refund_amount', 15, 2); // Amount to be refunded
            $table->decimal('original_amount', 15, 2); // Original transaction amount
            $table->enum('refund_type', ['full', 'partial'])->default('full');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->string('reason')->nullable(); // Reason for refund
            $table->text('midtrans_response')->nullable(); // Store Midtrans API response
            $table->timestamp('requested_at')->useCurrent(); // When refund was requested
            $table->timestamp('processed_at')->nullable(); // When refund was processed
            $table->foreignId('requested_by')->constrained('users', 'user_id')->onDelete('cascade'); // User who requested refund
            $table->foreignId('processed_by')->nullable()->constrained('users', 'user_id')->onDelete('set null'); // Admin who processed
            $table->text('admin_notes')->nullable(); // Admin notes
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['order_id', 'status']);
            $table->index(['status', 'requested_at']);
            $table->index('refund_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
