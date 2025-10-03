<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Role;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.admin')]
#[Title('Manajemen Pengguna')]

class UserManagement extends Component
{
    use WithPagination;

    protected $listeners = [
        'userCreated' => 'refreshData',
        'userUpdated' => 'refreshData'
    ];

    // Search and filter properties
    public $search = '';
    public $roleFilter = 'all';
    public $statusFilter = 'all';
    public $perPage = 10;
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $showDeletedUsers = false;

    // Modal properties
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $selectedUserId = null;

    // Form properties for create/edit
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

    protected $paginationTheme = 'tailwind';

    /**
     * Validation rules for user creation/editing
     */
    protected function rules()
    {
        $userId = $this->selectedUserId;
        
        return [
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($userId, 'user_id')
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId, 'user_id')
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'phone')->ignore($userId, 'user_id')
            ],
            'password' => $userId ? 'nullable|min:8|confirmed' : 'required|min:8|confirmed',
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
        'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, dash, dan underscore.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email sudah terdaftar.',
        'phone.required' => 'Nomor telepon wajib diisi.',
        'phone.unique' => 'Nomor telepon sudah terdaftar.',
        'password.required' => 'Password wajib diisi.',
        'password.min' => 'Password minimal 8 karakter.',
        'password.confirmed' => 'Konfirmasi password tidak cocok.',
        'role_id.required' => 'Role wajib dipilih.',
        'role_id.exists' => 'Role tidak valid.',
        'date_of_birth.before' => 'Tanggal lahir harus sebelum hari ini.',
        'gender.in' => 'Jenis kelamin tidak valid.',
    ];

    /**
     * Reset pagination when search changes
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when filters change
     */
    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    /**
     * Sort data by column
     */
    public function sortBy($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Open create user modal
     */
    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    /**
     * Edit user - load user data and open modal
     */
    public function editUser($userId)
    {
        try {
            $user = User::findOrFail($userId);
            
            // Set the selected user ID for validation
            $this->selectedUserId = $userId;
            
            // Load user data into form fields
            $this->name = $user->name;
            $this->username = $user->username;
            $this->email = $user->email;
            $this->phone = $user->phone;
            $this->role_id = $user->role_id;
            $this->status = $user->status;
            $this->date_of_birth = $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '';
            $this->gender = $user->gender ?? '';
            
            // Clear password fields
            $this->password = '';
            $this->password_confirmation = '';
            
            // Clear any previous errors
            $this->resetErrorBag();
            
            // Open the edit modal
            $this->showEditModal = true;
            
        } catch (\Exception $e) {
            session()->flash('error', 'Pengguna tidak ditemukan.');
        }
    }

    /**
     * Open delete confirmation modal
     */
    public function openDeleteModal($userId)
    {
        $this->selectedUserId = $userId;
        $this->showDeleteModal = true;
    }

    /**
     * Close all modals
     */
    public function closeModals()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showDeleteModal = false;
        $this->resetForm();
    }

    /**
     * Reset form fields
     */
    private function resetForm()
    {
        $this->selectedUserId = null;
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
        $this->resetErrorBag();
    }

    /**
     * Create new user
     */
    public function createUser()
    {
        $this->validate();

        try {
            $userData = [
                'name' => $this->name,
                'username' => $this->username,
                'email' => $this->email,
                'phone' => $this->phone,
                'password' => Hash::make($this->password),
                'role_id' => $this->role_id,
                'status' => $this->status,
                'date_of_birth' => !empty($this->date_of_birth) ? $this->date_of_birth : null,
                'gender' => !empty($this->gender) ? $this->gender : null,
                'email_verified_at' => now(), // Auto verify for admin created accounts
            ];

            $user = User::create($userData);
            
            // Load the role relationship for proper access
            $user->load('role');

            // Log the activity
            UserActivityLog::logActivity(
                'create_user',
                "Admin membuat akun baru untuk {$user->name} dengan role {$user->role->name}",
                $user->user_id,
                null,
                $userData
            );

            // Try to send email notification, but don't fail if it errors
            try {
                app(\App\Services\EmailNotificationService::class)->sendUserCreatedNotification($user, Auth::user());
            } catch (\Exception $emailError) {
                \Log::warning('Failed to send user creation notification email: ' . $emailError->getMessage());
            }

            session()->flash('success', 'Pengguna berhasil dibuat!');
            $this->closeModals();
            
        } catch (\Exception $e) {
            \Log::error('Error creating user: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat membuat pengguna. Silakan coba lagi.');
        }
    }

    /**
     * Update existing user
     */
    public function updateUser()
    {
        $this->validate();

        try {
            $user = User::findOrFail($this->selectedUserId);
            
            // Prevent admin from changing their own role or status
            if ($user->user_id === Auth::id()) {
                if ($user->role_id != $this->role_id) {
                    session()->flash('error', 'Anda tidak dapat mengubah role akun Anda sendiri.');
                    return;
                }
                if ($user->status != $this->status && $this->status === 'inactive') {
                    session()->flash('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
                    return;
                }
            }

            $userData = [
                'name' => $this->name,
                'username' => $this->username,
                'email' => $this->email,
                'phone' => $this->phone,
                'role_id' => $this->role_id,
                'status' => $this->status,
                'date_of_birth' => $this->date_of_birth ?: null,
                'gender' => $this->gender ?: null,
            ];

            // Store old values for logging
            $oldValues = $user->only(['name', 'username', 'email', 'phone', 'role_id', 'status', 'date_of_birth', 'gender']);

            // Only update password if provided
            if (!empty($this->password)) {
                $userData['password'] = Hash::make($this->password);
            }

            $user->update($userData);

            // Log the activity
            UserActivityLog::logActivity(
                'update_user',
                "Updated user: {$user->name}",
                $user->user_id,
                $oldValues,
                $user->only(['name', 'username', 'email', 'phone', 'role_id', 'status', 'date_of_birth', 'gender'])
            );

            // Try to send email notification, but don't fail if it errors
            try {
                $changes = array_diff_assoc($user->only(['name', 'username', 'email', 'phone', 'role_id', 'status', 'date_of_birth', 'gender']), $oldValues);
                app(\App\Services\EmailNotificationService::class)->sendUserUpdatedNotification($user, Auth::user(), $changes);
            } catch (\Exception $emailError) {
                \Log::warning('Failed to send user update notification email: ' . $emailError->getMessage());
            }

            session()->flash('success', 'Data pengguna berhasil diperbarui!');
            $this->closeModals();
            
        } catch (\Exception $e) {
            \Log::error('Error updating user: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat memperbarui data pengguna. Silakan coba lagi.');
        }
    }

    /**
     * Delete user (soft delete)
     */
    public function deleteUser()
    {
        try {
            $user = User::findOrFail($this->selectedUserId);
            
            // Prevent admin from deleting their own account
            if ($user->user_id === Auth::id()) {
                session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
                $this->closeModals();
                return;
            }

            // Log the activity before deletion
            UserActivityLog::logActivity(
                Auth::id(),
                $user->user_id,
                'delete_user',
                "Deleted user: {$user->name} with role {$user->role->name}",
                $user->only(['name', 'username', 'email', 'phone', 'role_id', 'status', 'date_of_birth', 'gender']),
                null
            );

            $user->delete();

            session()->flash('success', 'Pengguna berhasil dihapus!');
            $this->closeModals();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus pengguna: ' . $e->getMessage());
        }
    }

    /**
     * Show deleted users
     */
    public function showDeleted()
    {
        $this->showDeletedUsers = !$this->showDeletedUsers;
    }

    /**
     * Restore deleted user
     */
    public function restoreUser($userId)
    {
        try {
            $user = User::withTrashed()->findOrFail($userId);
            $user->restore();

            // Log the activity
            UserActivityLog::logActivity(
                Auth::id(),
                $user->user_id,
                'restore_user',
                "Restored user: {$user->name}",
                null,
                $user->only(['name', 'username', 'email', 'phone', 'role_id', 'status', 'date_of_birth', 'gender'])
            );

            session()->flash('success', 'Pengguna berhasil dipulihkan!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memulihkan pengguna: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete user
     */
    public function forceDeleteUser($userId)
    {
        try {
            $user = User::withTrashed()->findOrFail($userId);
            
            // Prevent admin from permanently deleting their own account
            if ($user->user_id === Auth::id()) {
                session()->flash('error', 'Anda tidak dapat menghapus permanen akun Anda sendiri.');
                return;
            }

            // Log the activity before permanent deletion
            UserActivityLog::logActivity(
                Auth::id(),
                $user->user_id,
                'force_delete_user',
                "Permanently deleted user: {$user->name}",
                $user->only(['name', 'username', 'email', 'phone', 'role_id', 'status', 'date_of_birth', 'gender']),
                null
            );

            $user->forceDelete();

            session()->flash('success', 'Pengguna berhasil dihapus permanen!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus permanen pengguna: ' . $e->getMessage());
        }
    }

    /**
     * Get users with filters and pagination
     */
    public function render()
    {
        $query = User::with('role');
        
        // Show deleted or non-deleted users based on toggle
        if ($this->showDeletedUsers) {
            $query->onlyTrashed();
        } else {
            $query->whereNull('deleted_at');
        }
        
        $query->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('username', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->roleFilter !== 'all', function ($query) {
                $query->whereHas('role', function ($q) {
                    $q->where('name', $this->roleFilter);
                });
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection);

        $users = $query->paginate($this->perPage);
        $roles = Role::all();

        return view('livewire.admin.user-management', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    /**
     * Refresh data when user is created from CreateUser component
     */
    public function refreshData()
    {
        $this->resetPage();
        $this->dispatch('$refresh');
    }
}