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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id('session_id');
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->foreignId('admin_id')->constrained('users', 'user_id');
            $table->enum('status', ['active', 'closed'])->default('active');
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id('message_id');
            $table->foreignId('session_id')->constrained('chat_sessions', 'session_id');
            $table->foreignId('sender_id')->constrained('users', 'user_id');
            $table->text('message');
            $table->enum('type', ['text', 'image', 'file'])->default('text');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('chat_sessions');
        Schema::dropIfExists('chat_messages');
    }
};
