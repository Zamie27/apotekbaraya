<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.user')]
class Login extends Component
{
    #[Validate('required|email|max:255')]
    public $email = '';

    #[Validate('required|min:8|max:255')]
    public $password = '';

    public $remember = false;

    // Laravel validation will handle input security
    // Removed preg_replace sanitization to rely on Laravel's built-in security

    /**
     * Handle user login with email verification check
     */
    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $user = Auth::user();
            
            // Check if user status is active
            if ($user->status !== 'active') {
                Auth::logout();
                
                session()->flash('error', 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator untuk informasi lebih lanjut.');
                
                return redirect()->route('login');
            }
            
            // Check if email is verified
            if (is_null($user->email_verified_at)) {
                Auth::logout();
                
                session()->flash('warning', 'Akun Anda belum diaktivasi. Silakan cek email dan klik link aktivasi yang telah dikirimkan.');
                
                return redirect()->route('email.verification.notice');
            }
            
            session()->regenerate();

            // Redirect based on user role
            switch ($user->role->name) {
                case 'admin':
                    return redirect('/admin/dashboard');
                case 'apoteker':
                    return redirect('/apoteker/dashboard');
                case 'kurir':
                    return redirect('/kurir/dashboard');
                case 'pelanggan':
                default:
                    return redirect('/dashboard');
            }
        }

        $this->addError('email', 'Email atau password salah.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
