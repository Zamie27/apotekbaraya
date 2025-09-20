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
        Schema::create('email_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Foreign key to users table
            $table->string('type'); // Type of notification (user_created, user_updated, etc.)
            $table->string('subject'); // Email subject
            $table->text('body'); // Email body content
            $table->string('recipient_email'); // Recipient email address
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending'); // Notification status
            $table->timestamp('sent_at')->nullable(); // When the email was sent
            $table->text('error_message')->nullable(); // Error message if failed
            $table->json('metadata')->nullable(); // Additional data (user info, etc.)
            $table->integer('retry_count')->default(0); // Number of retry attempts
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('set null');
            
            // Indexes for better performance
            $table->index(['status', 'created_at']);
            $table->index(['type', 'status']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_notifications');
    }
};
