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
    #[Validate('required|min:3')]
    public $name = '';

    #[Validate('required|unique:users,username')]
    public $username = '';

    #[Validate('required|email|unique:users,email')]
    public $email = '';

    #[Validate('required|min:10')]
    public $phone = '';

    #[Validate('required|min:6')]
    public $password = '';

    #[Validate('required|same:password')]
    public $password_confirmation = '';

    public $terms = false;

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

        Auth::login($user);

        session()->regenerate();

        return redirect('/pelanggan/dashboard');
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
