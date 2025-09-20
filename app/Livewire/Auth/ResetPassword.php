<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.user')]
class ResetPassword extends Component
{
    public $token;
    public $email;

    #[Validate('required|min:8|confirmed')]
    public $password = '';

    #[Validate('required|min:8')]
    public $password_confirmation = '';

    public $tokenValid = false;
    public $passwordReset = false;

    /**
     * Mount component and validate token
     */
    public function mount($token)
    {
        $this->token = $token;
        $this->email = request()->get('email');
        
        // Validate token and email
        $this->validateToken();
    }

    /**
     * Validate reset token
     */
    public function validateToken()
    {
        if (!$this->token || !$this->email) {
            $this->tokenValid = false;
            return;
        }

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $this->email)
            ->where('token', $this->token)
            ->first();

        if (!$resetRecord) {
            $this->tokenValid = false;
            return;
        }

        // Check if token is not expired (24 hours)
        $tokenAge = now()->diffInHours($resetRecord->created_at);
        if ($tokenAge > 24) {
            $this->tokenValid = false;
            // Delete expired token
            DB::table('password_reset_tokens')
                ->where('email', $this->email)
                ->delete();
            return;
        }

        $this->tokenValid = true;
    }

    /**
     * Reset user password
     */
    public function resetPassword()
    {
        $this->validate();

        if (!$this->tokenValid) {
            session()->flash('error', 'Token reset password tidak valid atau sudah kedaluwarsa.');
            return;
        }

        // Find user
        $user = User::where('email', $this->email)->first();
        
        if (!$user) {
            session()->flash('error', 'User tidak ditemukan.');
            return;
        }

        // Update password
        $user->update([
            'password' => Hash::make($this->password)
        ]);

        // Delete used token
        DB::table('password_reset_tokens')
            ->where('email', $this->email)
            ->delete();

        $this->passwordReset = true;
        session()->flash('success', 'Password berhasil direset. Silakan login dengan password baru Anda.');
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.auth.reset-password');
    }
}
