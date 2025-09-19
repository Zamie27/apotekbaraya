<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EmailVerificationService
{
    /**
     * Generate verification token for user
     *
     * @param User $user
     * @return string
     */
    public function generateVerificationToken(User $user): string
    {
        $token = Str::random(64);
        
        // Set token expiry to 24 hours
        $expiresAt = Carbon::now()->addHours(24);
        
        $user->update([
            'verification_token' => $token,
            'verification_token_expires_at' => $expiresAt,
        ]);
        
        Log::info('Verification token generated', [
            'user_id' => $user->user_id,
            'email' => $user->email,
            'expires_at' => $expiresAt,
        ]);
        
        return $token;
    }
    
    /**
     * Send email verification to user
     *
     * @param User $user
     * @return bool
     */
    public function sendVerificationEmail(User $user): bool
    {
        try {
            // Generate verification token
            $token = $this->generateVerificationToken($user);
            
            // Create verification URL
            $verificationUrl = url('/email/verify/' . $token);
            
            // Send email
            Mail::send('emails.email-verification', [
                'user' => $user,
                'verificationUrl' => $verificationUrl,
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Aktivasi Akun - Apotek Baraya');
            });
            
            Log::info('Verification email sent successfully', [
                'user_id' => $user->user_id,
                'email' => $user->email,
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send verification email', [
                'user_id' => $user->user_id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }
    
    /**
     * Verify email using token
     *
     * @param string $token
     * @return array
     */
    public function verifyEmail(string $token): array
    {
        // Find user by token
        $user = User::where('verification_token', $token)
                   ->where('verification_token_expires_at', '>', Carbon::now())
                   ->first();
        
        if (!$user) {
            Log::warning('Invalid or expired verification token', [
                'token' => $token,
            ]);
            
            return [
                'success' => false,
                'message' => 'Token verifikasi tidak valid atau sudah kedaluwarsa.',
                'user' => null,
            ];
        }
        
        // Check if already verified
        if ($user->email_verified_at) {
            Log::info('Email already verified', [
                'user_id' => $user->user_id,
                'email' => $user->email,
            ]);
            
            return [
                'success' => true,
                'message' => 'Email sudah diverifikasi sebelumnya.',
                'user' => $user,
            ];
        }
        
        // Mark email as verified and clear token
        try {
            $updateResult = $user->update([
                'email_verified_at' => Carbon::now(),
                'verification_token' => null,
                'verification_token_expires_at' => null,
            ]);
            
            Log::info('Email verified successfully', [
                'user_id' => $user->user_id,
                'email' => $user->email,
                'update_result' => $updateResult,
                'email_verified_at_after_update' => $user->fresh()->email_verified_at,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update user verification status', [
                'user_id' => $user->user_id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'message' => 'Gagal memperbarui status verifikasi.',
                'user' => null,
            ];
        }
        
        return [
            'success' => true,
            'message' => 'Email berhasil diverifikasi! Akun Anda sudah aktif.',
            'user' => $user,
        ];
    }
    
    /**
     * Resend verification email
     *
     * @param User $user
     * @return bool
     */
    public function resendVerificationEmail(User $user): bool
    {
        // Check if user already verified
        if ($user->email_verified_at) {
            return false;
        }
        
        // Check if can resend (prevent spam) - allow resend after 1 minute
        if ($user->verification_token_expires_at && 
            Carbon::now()->isBefore(Carbon::parse($user->verification_token_expires_at)->subHours(23)->subMinutes(59))) {
            return false;
        }
        
        return $this->sendVerificationEmail($user);
    }
    
    /**
     * Check if verification token is expired
     *
     * @param User $user
     * @return bool
     */
    public function isTokenExpired(User $user): bool
    {
        if (!$user->verification_token_expires_at) {
            return true;
        }
        
        return Carbon::now()->isAfter($user->verification_token_expires_at);
    }
}