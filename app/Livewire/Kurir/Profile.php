<?php

namespace App\Livewire\Kurir;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.kurir')]
class Profile extends Component
{
    use WithFileUploads;

    // User profile fields
    #[Validate('required|string|max:100|regex:/^[a-zA-Z\s]+$/')]
    public $name;

    #[Validate('required|string|max:50|unique:users,username|regex:/^[a-zA-Z0-9_]+$/')]
    public $username;

    #[Validate('required|email|max:255|unique:users,email')]
    public $email;

    #[Validate('nullable|string|min:10|max:15|regex:/^[0-9]+$/')]
    public $phone;

    #[Validate('nullable|date|before:today')]
    public $date_of_birth;

    #[Validate('nullable|in:male,female')]
    public $gender;

    #[Validate('nullable|image|max:2048')]
    public $avatar;

    // Password change fields
    #[Validate('nullable|string|min:8|max:255')]
    public $current_password;

    #[Validate('nullable|string|min:8|max:255|confirmed')]
    public $new_password;

    #[Validate('nullable|string|min:8|max:255')]
    public $new_password_confirmation;

    public $current_avatar;
    public $showPasswordForm = false;

    // Laravel validation will handle input security
    // Removed preg_replace sanitization to rely on Laravel's built-in security

    /**
     * Initialize component with current user data
     */
    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->date_of_birth = $user->date_of_birth?->format('Y-m-d');
        $this->gender = $user->gender;
        $this->current_avatar = $user->avatar;
    }

    /**
     * Update user profile information
     */
    public function updateProfile()
    {
        $user = Auth::user();
        
        // Validate unique fields excluding current user
        $this->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->user_id . ',user_id',
            'email' => 'required|email|unique:users,email,' . $user->user_id . ',user_id',
        ]);

        $this->validate();

        $updateData = [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
        ];

        // Handle avatar upload
        if ($this->avatar) {
            // Delete old avatar if exists
            if ($this->current_avatar && Storage::disk('public')->exists($this->current_avatar)) {
                Storage::disk('public')->delete($this->current_avatar);
            }

            // Store new avatar
            $avatarPath = $this->avatar->store('avatars', 'public');
            $updateData['avatar'] = $avatarPath;
            $this->current_avatar = $avatarPath;
        }

        $user->update($updateData);

        $this->avatar = null;
        session()->flash('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update user password
     */
    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Verify current password
        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Password saat ini tidak sesuai.');
            return;
        }

        // Update password
        $user->update([
            'password' => Hash::make($this->new_password)
        ]);

        // Reset password fields
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->showPasswordForm = false;

        session()->flash('success', 'Password berhasil diperbarui!');
    }

    /**
     * Toggle password form visibility
     */
    public function togglePasswordForm()
    {
        $this->showPasswordForm = !$this->showPasswordForm;
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
    }

    /**
     * Remove current avatar
     */
    public function removeAvatar()
    {
        $user = Auth::user();
        
        if ($this->current_avatar && Storage::disk('public')->exists($this->current_avatar)) {
            Storage::disk('public')->delete($this->current_avatar);
        }

        $user->update(['avatar' => null]);
        $this->current_avatar = null;

        session()->flash('success', 'Foto profil berhasil dihapus!');
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.kurir.profile');
    }
}