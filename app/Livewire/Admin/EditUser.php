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

class EditUser extends Component
{
    protected $listeners = ['openEditModal'];

    // Form properties
    public $showModal = false;
    public $userId = null;

    // Form properties
    public $name = '';
    public $username = '';
    public $email = '';
    public $phone = '';
    public $password = '';
    public $password_confirmation = '';
    public $role_id = '';
    public $status = 'active';
    public $gender = '';
    public $date_of_birth = '';

    // Original user data for comparison
    public $originalData = [];

    /**
     * Validation rules
     */
    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('users')->ignore($this->userId, 'user_id'),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->userId, 'user_id'),
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users')->ignore($this->userId, 'user_id'),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,role_id',
            'status' => 'required|in:active,inactive',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date|before:today',
        ];
    }

    /**
     * Custom validation messages
     */
    protected function messages()
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, dash, dan underscore.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.unique' => 'Nomor telepon sudah digunakan.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role_id.required' => 'Role wajib dipilih.',
            'role_id.exists' => 'Role tidak valid.',
            'status.required' => 'Status wajib dipilih.',
            'gender.in' => 'Jenis kelamin tidak valid.',
            'date_of_birth.date' => 'Format tanggal lahir tidak valid.',
            'date_of_birth.before' => 'Tanggal lahir harus sebelum hari ini.',
        ];
    }

    /**
     * Open modal and load user data
     */
    public function openModal($userId)
    {
        $this->userId = $userId;
        $this->loadUserData();
        $this->showModal = true;
    }

    /**
     * Close modal and reset form
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * Load user data for editing
     */
    private function loadUserData()
    {
        $user = User::with('role')->findOrFail($this->userId);
        
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->role_id = $user->role_id;
        $this->status = $user->status;
        $this->gender = $user->gender;
        $this->date_of_birth = $user->date_of_birth;

        // Store original data for comparison
        $this->originalData = [
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'phone' => $user->phone,
            'role_id' => $user->role_id,
            'status' => $user->status,
            'gender' => $user->gender,
            'date_of_birth' => $user->date_of_birth,
        ];
    }

    /**
     * Reset form data
     */
    private function resetForm()
    {
        $this->userId = null;
        $this->name = '';
        $this->username = '';
        $this->email = '';
        $this->phone = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->role_id = '';
        $this->status = 'active';
        $this->gender = '';
        $this->date_of_birth = '';
        $this->originalData = [];
        $this->resetErrorBag();
    }

    /**
     * Update user
     */
    public function updateUser()
    {
        $this->validate();

        try {
            $user = User::findOrFail($this->userId);
            
            // Prepare update data
            $updateData = [
                'name' => $this->name,
                'username' => $this->username,
                'email' => $this->email,
                'phone' => $this->phone,
                'role_id' => $this->role_id,
                'status' => $this->status,
                'gender' => !empty($this->gender) ? $this->gender : null,
                'date_of_birth' => !empty($this->date_of_birth) ? $this->date_of_birth : null,
            ];

            // Add password if provided
            if (!empty($this->password)) {
                $updateData['password'] = Hash::make($this->password);
            }

            // Update user
            $user->update($updateData);

            // Log activity - track what changed
            $changes = [];
            foreach ($updateData as $key => $value) {
                if ($key === 'password') {
                    if (!empty($this->password)) {
                        $changes[] = 'password';
                    }
                } elseif (isset($this->originalData[$key]) && $this->originalData[$key] != $value) {
                    $changes[] = $key;
                }
            }

            if (!empty($changes)) {
                UserActivityLog::create([
                    'user_id' => $user->user_id,
                    'action' => 'update',
                    'description' => 'User data updated by ' . Auth::user()->name . '. Changed fields: ' . implode(', ', $changes),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'performed_by' => Auth::id(),
                ]);

                // Send email notification for changes
                $emailService = app(EmailNotificationService::class);
                $emailService->sendUserUpdatedNotification($user, Auth::user(), $changes);
            }

            // Close modal and reset form
            $this->closeModal();

            // Show success message
            session()->flash('success', 'Data pengguna berhasil diperbarui.');

            // Dispatch event to refresh parent component
            $this->dispatch('userUpdated');

        } catch (\Exception $e) {
            // Log error
            \Log::error('Error updating user: ' . $e->getMessage());
            
            // Show error message
            session()->flash('error', 'Terjadi kesalahan saat memperbarui data pengguna. Silakan coba lagi.');
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
     * Open edit modal and load user data
     */
    public function openEditModal($userId)
    {
        $this->userId = $userId;
        $this->loadUserData();
        $this->showModal = true;
    }

    /**
     * Render component
     */
    public function render()
    {
        $roles = Role::all();
        
        return view('livewire.admin.edit-user', [
            'roles' => $roles,
        ]);
    }
}
