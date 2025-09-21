<?php

namespace App\Livewire\Auth;

use App\Models\Role;
use App\Models\User;
use App\Services\EmailVerificationService;
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

    #[Validate('required|min:8|max:255|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$*!])[A-Za-z\d@#$*!]+$/')]
    public $password = '';

    #[Validate('required|same:password')]
    public $password_confirmation = '';

    public $terms = false;

    // Laravel validation will handle input security
    // Removed preg_replace sanitization to rely on Laravel's built-in security

    /**
     * Custom validation messages for better user experience
     */
    protected function messages()
    {
        return [
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.max' => 'Kata sandi maksimal 255 karakter.',
            'password.regex' => 'Kata sandi harus mengandung minimal 1 huruf kecil, 1 huruf kapital, 1 angka, dan 1 simbol (@#$*!).',
            'password_confirmation.required' => 'Konfirmasi kata sandi wajib diisi.',
            'password_confirmation.same' => 'Konfirmasi kata sandi tidak cocok.',
        ];
    }

    /**
     * Handle user registration and send email verification
     */
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
            'email_verified_at' => null, // Email not verified yet
        ]);

        // Send email verification
        $emailVerificationService = new EmailVerificationService();
        $emailSent = $emailVerificationService->sendVerificationEmail($user);

        if ($emailSent) {
            session()->flash('success', 'Akun berhasil dibuat! Email aktivasi telah dikirim ke ' . $user->email . '. Silakan cek email Anda untuk mengaktifkan akun.');
            
            // Redirect to login with notice to check email
            return redirect()->route('login')->with('registration_success', true);
        } else {
            // If email sending failed, delete the user and show error
            $user->delete();
            
            session()->flash('error', 'Gagal mengirim email aktivasi. Silakan coba lagi.');
            
            return;
        }
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
