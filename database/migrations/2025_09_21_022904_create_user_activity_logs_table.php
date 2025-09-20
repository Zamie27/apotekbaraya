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
        Schema::create('user_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // User yang melakukan aktivitas
            $table->unsignedBigInteger('target_user_id')->nullable(); // User yang menjadi target aktivitas
            $table->string('action'); // Jenis aktivitas (create, update, delete, restore, login, logout)
            $table->string('description'); // Deskripsi aktivitas
            $table->json('old_values')->nullable(); // Data lama sebelum perubahan
            $table->json('new_values')->nullable(); // Data baru setelah perubahan
            $table->string('ip_address')->nullable(); // IP address
            $table->string('user_agent')->nullable(); // User agent
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('target_user_id')->references('user_id')->on('users')->onDelete('cascade');
            
            // Indexes for better performance
            $table->index(['user_id', 'created_at']);
            $table->index(['target_user_id', 'created_at']);
            $table->index(['action', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activity_logs');
    }
};
