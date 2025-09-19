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
        Schema::table('users', function (Blueprint $table) {
            // Remove OTP fields
            $table->dropColumn(['otp_code', 'otp_expires_at']);
            
            // Add email verification token
            $table->string('verification_token')->nullable()->after('email_verified_at');
            $table->timestamp('verification_token_expires_at')->nullable()->after('verification_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove verification token fields
            $table->dropColumn(['verification_token', 'verification_token_expires_at']);
            
            // Add back OTP fields
            $table->string('otp_code', 6)->nullable()->after('email_verified_at');
            $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
        });
    }
};
