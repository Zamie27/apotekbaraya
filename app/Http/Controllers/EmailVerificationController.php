<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EmailVerificationService;
use Illuminate\Support\Facades\Log;

class EmailVerificationController extends Controller
{
    protected $emailVerificationService;
    
    public function __construct(EmailVerificationService $emailVerificationService)
    {
        $this->emailVerificationService = $emailVerificationService;
    }
    
    /**
     * Handle email verification via token from email link
     *
     * @param string $token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify($token)
    {
        // Verify email using token
        $result = $this->emailVerificationService->verifyEmail($token);
        
        if ($result['success']) {
            // Redirect to login with success message
            return redirect()->route('login')
                           ->with('success', $result['message'])
                           ->with('email_verified', true);
        } else {
            // Redirect to login with error message
            return redirect()->route('login')
                           ->with('error', $result['message'])
                           ->with('verification_failed', true);
        }
    }
    
    /**
     * Show email verification notice page
     *
     * @return \Illuminate\View\View
     */
    public function notice()
    {
        return view('auth.email-verification-notice');
    }
    
    /**
     * Resend verification email
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        
        $user = \App\Models\User::where('email', $request->email)->first();
        
        if ($user->email_verified_at) {
            return back()->with('info', 'Email sudah diverifikasi sebelumnya.');
        }
        
        $sent = $this->emailVerificationService->resendVerificationEmail($user);
        
        if ($sent) {
            return back()->with('success', 'Email verifikasi telah dikirim ulang. Silakan cek inbox Anda.');
        } else {
            return back()->with('error', 'Gagal mengirim email verifikasi. Silakan coba lagi dalam beberapa saat.');
        }
    }
}
