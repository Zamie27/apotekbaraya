<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OTPService
{
    /**
     * Generate a 6-digit OTP code
     *
     * @return string
     */
    public function generateOTP(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Send OTP to user's email
     *
     * @param User $user
     * @return bool
     */
    public function sendOTP(User $user): bool
    {
        try {
            // Generate new OTP
            $otpCode = $this->generateOTP();
            $expiresAt = Carbon::now()->addMinutes(10); // OTP expires in 10 minutes

            // Update user with OTP data
            $user->update([
                'otp_code' => $otpCode,
                'otp_expires_at' => $expiresAt,
            ]);

            // Send email with OTP
            Mail::send('emails.otp-verification', [
                'user' => $user,
                'otpCode' => $otpCode,
                'expiresAt' => $expiresAt,
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Verifikasi Email - Apotek Baraya');
            });

            Log::info('OTP sent successfully', [
                'user_id' => $user->user_id,
                'email' => $user->email,
                'otp_expires_at' => $expiresAt,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send OTP', [
                'user_id' => $user->user_id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Verify OTP code
     *
     * @param User $user
     * @param string $otpCode
     * @return bool
     */
    public function verifyOTP(User $user, string $otpCode): bool
    {
        // Check if OTP matches and hasn't expired
        if ($user->otp_code === $otpCode && 
            $user->otp_expires_at && 
            Carbon::now()->isBefore($user->otp_expires_at)) {
            
            // Mark email as verified and clear OTP data
            $user->update([
                'email_verified_at' => Carbon::now(),
                'otp_code' => null,
                'otp_expires_at' => null,
            ]);

            Log::info('Email verified successfully', [
                'user_id' => $user->user_id,
                'email' => $user->email,
            ]);

            return true;
        }

        Log::warning('Invalid OTP verification attempt', [
            'user_id' => $user->user_id,
            'email' => $user->email,
            'provided_otp' => $otpCode,
            'stored_otp' => $user->otp_code,
            'expires_at' => $user->otp_expires_at,
        ]);

        return false;
    }

    /**
     * Check if OTP has expired
     *
     * @param User $user
     * @return bool
     */
    public function isOTPExpired(User $user): bool
    {
        return !$user->otp_expires_at || Carbon::now()->isAfter($user->otp_expires_at);
    }

    /**
     * Resend OTP to user
     *
     * @param User $user
     * @return bool
     */
    public function resendOTP(User $user): bool
    {
        // Check if user can request new OTP (prevent spam) - allow resend after 1 minute
        if ($user->otp_expires_at && Carbon::now()->isBefore($user->otp_expires_at->subMinutes(9))) {
            Log::warning('OTP resend attempt too soon', [
                'user_id' => $user->user_id,
                'email' => $user->email,
                'last_otp_expires_at' => $user->otp_expires_at,
            ]);
            
            return false;
        }

        return $this->sendOTP($user);
    }
}