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

    #[Validate('required|min:8|max:255|regex:/^(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[!@#$%^&*()_+\-=\[\]{};:"\|,.<>\?])[a-zA-Z0-9!@#$%^&*()_+\-=\[\]{};:"\|,.<>\?]+$/')]
    public $password = '';

    #[Validate('required|same:password')]
    public $password_confirmation = '';

    public $terms = false;

    /**
     * Sanitize name input - only letters and spaces
     */
    public function updatedName($value)
    {
        $this->name = preg_replace('/[^a-zA-Z\s]/', '', $value);
        $this->name = trim(substr($this->name, 0, 100));
    }

    /**
     * Sanitize username input - only letters, numbers, and underscore
     */
    public function updatedUsername($value)
    {
        $this->username = preg_replace('/[^a-zA-Z0-9_]/', '', $value);
        $this->username = trim(substr($this->username, 0, 50));
    }

    /**
     * Sanitize phone input - only numbers
     */
    public function updatedPhone($value)
    {
        $this->phone = preg_replace('/[^0-9]/', '', $value);
        $this->phone = substr($this->phone, 0, 15);
    }

    /**
     * Sanitize password input - allow letters, numbers, and specific symbols
     */
    public function updatedPassword($value)
    {
        $this->password = preg_replace('/[^a-zA-Z0-9!@#$%^&*()_+\-=\[\]{};:"\|,.<>\?]/', '', $value);
        $this->password = substr($this->password, 0, 255);
    }

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
