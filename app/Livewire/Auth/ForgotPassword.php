<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.user')]
class ForgotPassword extends Component
{
    #[Validate('required|email|max:255')]
    public $email = '';

    public $emailSent = false;

    /**
     * Send password reset email to user
     */
    public function sendResetLink()
    {
        $this->validate();

        // Check if user exists
        $user = User::where('email', $this->email)->first();
        
        if (!$user) {
            session()->flash('error', 'Email tidak ditemukan dalam sistem kami.');
            return;
        }

        // Generate reset token
        $token = Str::random(64);
        
        // Store token in database
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $this->email],
            [
                'email' => $this->email,
                'token' => $token,
                'created_at' => now()
            ]
        );

        // Send email with reset link
        try {
            Mail::send('emails.password-reset', [
                'user' => $user,
                'token' => $token,
                'resetUrl' => url('/reset-password/' . $token . '?email=' . urlencode($this->email))
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Reset Password - Apotek Baraya');
            });

            $this->emailSent = true;
            session()->flash('success', 'Link reset password telah dikirim ke email Anda. Silakan cek inbox atau folder spam.');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengirim email. Silakan coba lagi nanti.');
        }
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
