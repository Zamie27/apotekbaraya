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

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();

            // Redirect based on user role
            $user = Auth::user();

            switch ($user->role->name) {
                case 'admin':
                    return redirect('/admin/dashboard');
                case 'apoteker':
                    return redirect('/apoteker/dashboard');
                case 'kurir':
                    return redirect('/kurir/dashboard');
                case 'pelanggan':
                default:
                    return redirect('/');
            }
        }

        $this->addError('email', 'Email atau password salah.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
