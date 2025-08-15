<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\UserAddress;

class Profile extends Component
{
    use WithFileUploads;

    // Profile fields
    #[Validate('required|string|max:100|regex:/^[a-zA-Z\s]+$/')]
    public $name;

    #[Validate('required|string|max:50|regex:/^[a-zA-Z0-9_]+$/')]
    public $username;

    #[Validate('required|email|max:255')]
    public $email;

    #[Validate('nullable|string|min:10|max:15|regex:/^[0-9]+$/')]
    public $phone;

    #[Validate('nullable|date|before:today')]
    public $date_of_birth;

    #[Validate('nullable|in:male,female')]
    public $gender;

    #[Validate('nullable|image|max:2048')]
    public $avatar;

    public $current_avatar;

    // Password fields
    #[Validate('nullable|string|min:8|max:255|regex:/^(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[!@#$%^&*()_+\-=\[\]{};:"\|,.<>\?])[a-zA-Z0-9!@#$%^&*()_+\-=\[\]{};:"\|,.<>\?]+$/')]
    public $current_password;

    #[Validate('nullable|string|min:8|max:255|confirmed|regex:/^(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[!@#$%^&*()_+\-=\[\]{};:"\|,.<>\?])[a-zA-Z0-9!@#$%^&*()_+\-=\[\]{};:"\|,.<>\?]+$/')]
    public $new_password;

    #[Validate('nullable|string|min:8|max:255')]
    public $new_password_confirmation;

    public $showPasswordForm = false;

    // Address fields
    public $addresses = [];
    public $showAddressForm = false;
    public $editingAddressId = null;
    
    #[Validate('required|in:rumah,kantor,kost,lainnya')]
    public $address_label = 'rumah';
    
    #[Validate('required|string|max:100|regex:/^[a-zA-Z\s]+$/')]
    public $recipient_name;
    
    #[Validate('required|string|min:10|max:15|regex:/^[0-9]+$/')]
    public $address_phone;
    
    #[Validate('required|string|max:500')]
    public $address;
    
    #[Validate('required|string|max:100')]
    public $district;
    
    #[Validate('required|string|max:100')]
    public $city;
    
    #[Validate('required|string|max:10|regex:/^[0-9]+$/')]
    public $postal_code;
    
    #[Validate('nullable|string|max:255')]
    public $notes;
    
    #[Validate('boolean')]
    public $is_default = false;

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
     * Sanitize current password input
     */
    public function updatedCurrentPassword($value)
    {
        $this->current_password = preg_replace('/[^a-zA-Z0-9!@#$%^&*()_+\-=\[\]{};:"\|,.<>\?]/', '', $value);
        $this->current_password = substr($this->current_password, 0, 255);
    }

    /**
     * Sanitize new password input
     */
    public function updatedNewPassword($value)
    {
        $this->new_password = preg_replace('/[^a-zA-Z0-9!@#$%^&*()_+\-=\[\]{};:"\|,.<>\?]/', '', $value);
        $this->new_password = substr($this->new_password, 0, 255);
    }

    /**
     * Sanitize recipient name input - only letters and spaces
     */
    public function updatedRecipientName($value)
    {
        $this->recipient_name = preg_replace('/[^a-zA-Z\s]/', '', $value);
        $this->recipient_name = trim(substr($this->recipient_name, 0, 100));
    }

    /**
     * Sanitize address phone input - only numbers
     */
    public function updatedAddressPhone($value)
    {
        $this->address_phone = preg_replace('/[^0-9]/', '', $value);
        $this->address_phone = substr($this->address_phone, 0, 15);
    }

    /**
     * Sanitize postal code input - only numbers
     */
    public function updatedPostalCode($value)
    {
        $this->postal_code = preg_replace('/[^0-9]/', '', $value);
        $this->postal_code = substr($this->postal_code, 0, 10);
    }

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->date_of_birth = $user->date_of_birth;
        $this->gender = $user->gender;
        $this->current_avatar = $user->avatar;
        
        // Load user addresses
        $this->loadAddresses();
    }
    
    /**
     * Load user addresses
     */
    public function loadAddresses()
    {
        $this->addresses = Auth::user()->addresses()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get()->toArray();
    }

    public function updateProfile()
    {
        $user = Auth::user();
        
        // Validate unique fields excluding current user
        $this->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $user->user_id . ',user_id|regex:/^[a-zA-Z0-9_]+$/',
            'email' => 'required|email|max:255|unique:users,email,' . $user->user_id . ',user_id',
        ]);

        $this->validate();
        
        // Handle avatar upload
        if ($this->avatar) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::exists($user->avatar)) {
                Storage::delete($user->avatar);
            }
            
            // Store new avatar
            $avatarPath = $this->avatar->store('avatars', 'public');
            $this->current_avatar = $avatarPath;
        } else {
            $avatarPath = $user->avatar;
        }

        // Update user data
        $user->update([
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'avatar' => $avatarPath,
        ]);

        // Reset avatar upload field
        $this->avatar = null;
        $this->current_avatar = $avatarPath;

        session()->flash('success', 'Profil berhasil diperbarui!');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|max:255|confirmed|regex:/^(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[!@#$%^&*()_+\-=\[\]{};:"\|,.<>\?])[a-zA-Z0-9!@#$%^&*()_+\-=\[\]{};:"\|,.<>\?]+$/',
            'new_password_confirmation' => 'required|string',
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

    public function togglePasswordForm()
    {
        $this->showPasswordForm = !$this->showPasswordForm;
        
        // Reset password fields when hiding form
        if (!$this->showPasswordForm) {
            $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        }
    }

    public function removeAvatar()
    {
        $user = Auth::user();
        
        if ($user->avatar && Storage::exists($user->avatar)) {
            Storage::delete($user->avatar);
        }
        
        $user->update(['avatar' => null]);
        $this->current_avatar = null;
        
        session()->flash('success', 'Foto profil berhasil dihapus!');
    }

    /**
     * Show address form
     */
    public function openAddressForm()
    {
        $this->showAddressForm = true;
        $this->resetAddressForm();
    }

    /**
     * Hide address form
     */
    public function hideAddressForm()
    {
        $this->showAddressForm = false;
        $this->editingAddressId = null;
        $this->resetAddressForm();
    }

    /**
     * Reset address form
     */
    public function resetAddressForm()
    {
        $this->address_label = 'rumah';
        $this->recipient_name = '';
        $this->address_phone = '';
        $this->address = '';
        $this->district = '';
        $this->city = '';
        $this->postal_code = '';
        $this->notes = '';
        $this->is_default = false;
    }

    /**
     * Edit address
     */
    public function editAddress($addressId)
    {
        $address = UserAddress::where('address_id', $addressId)
            ->where('user_id', Auth::id())
            ->first();

        if ($address) {
            $this->editingAddressId = $addressId;
            $this->address_label = $address->label;
            $this->recipient_name = $address->recipient_name;
            $this->address_phone = $address->phone;
            $this->address = $address->address;
            $this->district = $address->district;
            $this->city = $address->city;
            $this->postal_code = $address->postal_code;
            $this->notes = $address->notes;
            $this->is_default = $address->is_default;
            $this->showAddressForm = true;
            
            // Dispatch browser event for toast notification
            $this->dispatch('show-toast', 'info', 'Mode edit alamat diaktifkan.');
            
            session()->flash('info', 'Mode edit alamat diaktifkan.');
        } else {
            $this->dispatch('show-toast', 'error', 'Alamat tidak ditemukan.');
            
            session()->flash('error', 'Alamat tidak ditemukan.');
        }
    }

    /**
     * Save address (create or update)
     */
    public function saveAddress()
    {
        $this->validate([
            'address_label' => 'required|in:rumah,kantor,kost,lainnya',
            'recipient_name' => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'address_phone' => 'required|string|min:10|max:15|regex:/^[0-9]+$/',
            'address' => 'required|string|max:500',
            'district' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10|regex:/^[0-9]+$/',
            'notes' => 'nullable|string|max:255',
        ]);

        $addressData = [
            'user_id' => Auth::id(),
            'label' => $this->address_label,
            'recipient_name' => $this->recipient_name,
            'phone' => $this->address_phone,
            'address' => $this->address,
            'district' => $this->district,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'notes' => $this->notes,
            'is_default' => $this->is_default,
        ];

        if ($this->editingAddressId) {
            // Update existing address
            $address = UserAddress::where('address_id', $this->editingAddressId)
                ->where('user_id', Auth::id())
                ->first();

            if ($address) {
                $address->update($addressData);
                
                // Set as default if requested
                if ($this->is_default) {
                    $address->setAsDefault();
                }
                
                session()->flash('success', 'Alamat berhasil diperbarui!');
            }
        } else {
            // Create new address
            $address = UserAddress::create($addressData);
            
            // Set as default if requested
            if ($this->is_default) {
                $address->setAsDefault();
            }
            
            session()->flash('success', 'Alamat berhasil ditambahkan!');
        }

        $this->loadAddresses();
        $this->hideAddressForm();
    }

    /**
     * Delete address
     */
    public function deleteAddress($addressId)
    {
        $address = UserAddress::where('address_id', $addressId)
            ->where('user_id', Auth::id())
            ->first();

        if ($address) {
            $address->delete();
            $this->loadAddresses();
            
            // Dispatch browser event for toast notification
            $this->dispatch('show-toast', 'success', 'Alamat berhasil dihapus!');
            
            session()->flash('success', 'Alamat berhasil dihapus!');
        } else {
            $this->dispatch('show-toast', 'error', 'Alamat tidak ditemukan atau tidak dapat dihapus.');
            
            session()->flash('error', 'Alamat tidak ditemukan atau tidak dapat dihapus.');
        }
    }

    /**
     * Set address as default
     */
    public function setDefaultAddress($addressId)
    {
        $address = UserAddress::where('address_id', $addressId)
            ->where('user_id', Auth::id())
            ->first();

        if ($address) {
            $address->setAsDefault();
            $this->loadAddresses();
            
            // Dispatch browser event for toast notification
            $this->dispatch('show-toast', 'success', 'Alamat default berhasil diubah!');
            
            session()->flash('success', 'Alamat default berhasil diubah!');
        } else {
            $this->dispatch('show-toast', 'error', 'Alamat tidak ditemukan atau tidak dapat diubah.');
            
            session()->flash('error', 'Alamat tidak ditemukan atau tidak dapat diubah.');
        }
    }

    public function render()
    {
        return view('livewire.profile')
            ->layout('components.layouts.user');
    }
}