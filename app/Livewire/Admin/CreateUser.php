<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Role;
use App\Models\UserActivityLog;
use App\Services\EmailNotificationService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CreateUser extends Component
{
    // Form properties
    public $name = '';
    public $username = '';
    public $email = '';
    public $phone = '';
    public $password = '';
    public $password_confirmation = '';
    public $role_id = '';
    public $status = 'active';
    public $date_of_birth = '';
    public $gender = '';

    // Modal properties
    public $showModal = false;

    /**
     * Validation rules for user creation
     */
    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,role_id',
            'status' => 'required|in:active,inactive',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female',
        ];
    }

    /**
     * Custom validation messages
     */
    protected $messages = [
        'name.required' => 'Nama lengkap wajib diisi.',
        'username.required' => 'Username wajib diisi.',
        'username.unique' => 'Username sudah digunakan.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email sudah digunakan.',
        'phone.required' => 'Nomor telepon wajib diisi.',
        'phone.unique' => 'Nomor telepon sudah digunakan.',
        'password.required' => 'Password wajib diisi.',
        'password.min' => 'Password minimal 8 karakter.',
        'password.confirmed' => 'Konfirmasi password tidak cocok.',
        'role_id.required' => 'Role wajib dipilih.',
        'role_id.exists' => 'Role tidak valid.',
        'status.required' => 'Status wajib dipilih.',
        'date_of_birth.before' => 'Tanggal lahir harus sebelum hari ini.',
        'gender.in' => 'Jenis kelamin tidak valid.',
    ];

    /**
     * Open create modal
     */
    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * Close create modal
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    /**
     * Reset form fields
     */
    public function resetForm()
    {
        $this->name = '';
        $this->username = '';
        $this->email = '';
        $this->phone = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->role_id = '';
        $this->status = 'active';
        $this->date_of_birth = '';
        $this->gender = '';
    }

    /**
     * Create new user
     */
    public function createUser()
    {
        // Validate form data
        $validatedData = $this->validate();

        try {
            // Create new user
            $user = User::create([
                'name' => $validatedData['name'],
                'username' => $validatedData['username'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'password' => Hash::make($validatedData['password']),
                'role_id' => $validatedData['role_id'],
                'status' => $validatedData['status'],
                'date_of_birth' => !empty($validatedData['date_of_birth']) ? $validatedData['date_of_birth'] : null,
                'gender' => !empty($validatedData['gender']) ? $validatedData['gender'] : null,
                'email_verified_at' => now(), // Auto verify for admin created accounts
            ]);

            // Log activity
            UserActivityLog::logActivity(
                Auth::id(),
                $user->user_id,
                'create_user',
                "Admin membuat akun pengguna baru: {$user->name} ({$user->username})",
                null,
                $user->only(['name', 'username', 'email', 'phone', 'role_id', 'status'])
            );

            // Send email notification
            $emailService = app(EmailNotificationService::class);
            $emailService->sendUserCreatedNotification($user, Auth::user());

            // Close modal and reset form
            $this->closeModal();

            // Show success message
            session()->flash('success', 'Pengguna berhasil dibuat!');

            // Emit event to refresh parent component
            $this->dispatch('userCreated');

        } catch (\Exception $e) {
            // Log error
            \Log::error('Error creating user: ' . $e->getMessage());
            
            // Show error message
            session()->flash('error', 'Terjadi kesalahan saat membuat pengguna. Silakan coba lagi.');
        }
    }

    /**
     * Real-time validation for username
     */
    public function updatedUsername()
    {
        $this->validateOnly('username');
    }

    /**
     * Real-time validation for email
     */
    public function updatedEmail()
    {
        $this->validateOnly('email');
    }

    /**
     * Real-time validation for phone
     */
    public function updatedPhone()
    {
        $this->validateOnly('phone');
    }

    /**
     * Render component
     */
    public function render()
    {
        $roles = Role::all();
        
        return view('livewire.admin.create-user', [
            'roles' => $roles,
        ]);
    }
}
