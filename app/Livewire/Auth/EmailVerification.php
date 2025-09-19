<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Services\OTPService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class EmailVerification extends Component
{
    public $otpCode = '';
    public $isLoading = false;
    public $canResend = true;
    public $resendCountdown = 0;
    public $user;

    protected $rules = [
        'otpCode' => 'required|string|size:6',
    ];

    protected $messages = [
        'otpCode.required' => 'Kode OTP wajib diisi.',
        'otpCode.size' => 'Kode OTP harus 6 digit.',
    ];

    /**
     * Initialize component
     */
    public function mount()
    {
        // Get user from session or redirect to login
        $this->user = Auth::user();
        
        if (!$this->user) {
            return redirect()->route('login');
        }

        // If already verified, redirect to dashboard
        if ($this->user->email_verified_at) {
            return redirect()->route('dashboard');
        }

        // Check if OTP is still valid for resend countdown
        $this->checkResendAvailability();
    }

    /**
     * Verify OTP code
     */
    public function verifyOTP()
    {
        $this->validate();
        $this->isLoading = true;

        try {
            $otpService = new OTPService();
            
            if ($otpService->verifyOTP($this->user, $this->otpCode)) {
                // Refresh user data
                $this->user->refresh();
                
                session()->flash('success', 'Email berhasil diverifikasi! Selamat datang di Apotek Baraya.');
                
                // Redirect to dashboard based on role
                return $this->redirectToDashboard();
            } else {
                $this->addError('otpCode', 'Kode OTP tidak valid atau sudah kedaluwarsa.');
            }
        } catch (\Exception $e) {
            $this->addError('otpCode', 'Terjadi kesalahan saat verifikasi. Silakan coba lagi.');
        }

        $this->isLoading = false;
        $this->otpCode = '';
    }

    /**
     * Resend OTP code
     */
    public function resendOTP()
    {
        if (!$this->canResend) {
            return;
        }

        $this->isLoading = true;

        try {
            $otpService = new OTPService();
            
            if ($otpService->resendOTP($this->user)) {
                session()->flash('success', 'Kode OTP baru telah dikirim ke email Anda.');
                $this->startResendCountdown();
            } else {
                session()->flash('error', 'Gagal mengirim ulang kode OTP. Silakan tunggu beberapa saat.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat mengirim ulang kode OTP.');
        }

        $this->isLoading = false;
    }

    /**
     * Check if resend is available
     */
    private function checkResendAvailability()
    {
        if ($this->user->otp_expires_at) {
            $expiresAt = Carbon::parse($this->user->otp_expires_at);
            $canResendAt = $expiresAt->subMinutes(9); // Can resend 1 minute after sending (10-9=1)
            
            if (Carbon::now()->isBefore($canResendAt)) {
                $this->canResend = false;
                $this->resendCountdown = Carbon::now()->diffInSeconds($canResendAt);
            }
        }
    }

    /**
     * Start resend countdown
     */
    private function startResendCountdown()
    {
        $this->canResend = false;
        $this->resendCountdown = 60; // 1 minute cooldown
    }

    /**
     * Redirect to appropriate dashboard based on user role
     */
    private function redirectToDashboard()
    {
        $role = $this->user->role->name ?? 'pelanggan';
        
        switch ($role) {
            case 'admin':
                return redirect('/admin/dashboard');
            case 'apoteker':
                return redirect()->route('apoteker.dashboard');
            case 'kurir':
                return redirect()->route('kurir.dashboard');
            default:
                return redirect()->route('dashboard');
        }
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.auth.email-verification')
            ->layout('components.layouts.user');
    }
}
