<?php

namespace App\Livewire\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.user')]
class Register extends Component
{
    #[Validate('required|min:3|max:100|regex:/^[a-zA-Z\s]+$/')]
    public $name = '';

    #[Validate('required|min:3|max:50|unique:users,username|regex:/^[a-zA-Z0-9_]+$/')]
    public $username = '';

    #[Validate('required|email|max:255|unique:users,email')]
    public $email = '';

    #[Validate('required|min:10|max:15|regex:/^[0-9]+$/')]
    public $phone = '';

    #[Validate('required|min:8|max:255')]
    public $password = '';

    #[Validate('required|same:password')]
    public $password_confirmation = '';

    public $terms = false;

    // Laravel validation will handle input security
    // Removed preg_replace sanitization to rely on Laravel's built-in security

    public function register()
    {
        $this->validate([
            'terms' => 'accepted'
        ], [
            'terms.accepted' => 'Anda harus menyetujui syarat dan ketentuan.'
        ]);

        $this->validate();

        // Get pelanggan role (default role for registration)
        $pelangganRole = Role::where('name', 'pelanggan')->first();

        $user = User::create([
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => Hash::make($this->password),
            'role_id' => $pelangganRole->role_id,
        ]);

        // Redirect to login page with success message instead of auto-login
        session()->flash('success', 'Akun berhasil dibuat! Silakan masuk dengan email dan password yang telah Anda buat.');
        
        return redirect('/login');
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
