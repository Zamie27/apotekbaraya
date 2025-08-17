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
use App\Services\CheckoutService;

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
    #[Validate('nullable|string|min:8|max:255')]
    public $current_password;

    #[Validate('nullable|string|min:8|max:255|confirmed')]
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
    public $village; // Desa
    
    #[Validate('required|string|max:100')]
    public $sub_district; // Kecamatan
    
    #[Validate('required|string|max:100')]
    public $district; // Keep for backward compatibility
    
    #[Validate('required|string|max:100')]
    public $regency; // Kabupaten
    
    #[Validate('required|string|max:100')]
    public $city; // Keep for backward compatibility
    
    #[Validate('required|string|max:100')]
    public $province; // Provinsi
    
    #[Validate('required|string|max:10|regex:/^[0-9]+$/')]
    public $postal_code;
    
    #[Validate('nullable|string|max:1000')]
    public $detailed_address; // Alamat lengkap spesifik untuk kurir
    
    #[Validate('nullable|string|max:255')]
    public $notes;
    
    #[Validate('boolean')]
    public $is_default = false;

    // Laravel validation will handle input security
    // Removed preg_replace sanitization to rely on Laravel's built-in security

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
            'new_password' => 'required|string|min:8|max:255|confirmed',
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
        $this->village = '';
        $this->sub_district = '';
        $this->district = '';
        $this->regency = '';
        $this->city = '';
        $this->province = '';
        $this->postal_code = '';
        $this->detailed_address = '';
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
            $this->village = $address->village ?? '';
            $this->sub_district = $address->sub_district ?? $address->district; // Fallback to old district
            $this->district = $address->district;
            $this->regency = $address->regency ?? $address->city; // Fallback to old city
            $this->city = $address->city;
            $this->province = $address->province ?? '';
            $this->postal_code = $address->postal_code;
            $this->detailed_address = $address->detailed_address ?? '';
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
        // Debug logging
        \Log::info('saveAddress function called', [
            'user_id' => Auth::id(),
            'address_label' => $this->address_label,
            'recipient_name' => $this->recipient_name,
            'editing_address_id' => $this->editingAddressId
        ]);
        
        // Auto-fill district and city for backward compatibility
        $this->district = $this->sub_district;
        $this->city = $this->regency;
        
        try {
            $this->validate([
                'address_label' => 'required|in:rumah,kantor,kost,lainnya',
                'recipient_name' => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
                'address_phone' => 'required|string|min:10|max:15|regex:/^[0-9]+$/',
                'village' => 'required|string|max:100',
                'sub_district' => 'required|string|max:100',
                'district' => 'required|string|max:100',
                'regency' => 'required|string|max:100',
                'city' => 'required|string|max:100',
                'province' => 'required|string|max:100',
                'postal_code' => 'required|string|max:10|regex:/^[0-9]+$/',
                'detailed_address' => 'required|string|max:1000',
                'notes' => 'nullable|string|max:255',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Address validation failed', [
                'errors' => $e->errors(),
                'input_data' => [
                    'address_label' => $this->address_label,
                    'recipient_name' => $this->recipient_name,
                    'address_phone' => $this->address_phone,
                    'village' => $this->village,
                    'sub_district' => $this->sub_district,
                    'regency' => $this->regency,
                    'province' => $this->province,
                    'postal_code' => $this->postal_code,
                    'detailed_address' => $this->detailed_address,
                ]
            ]);
            throw $e;
        }
        
        // Debug: Validation passed
        \Log::info('Address validation passed', [
            'all_data' => [
                'address_label' => $this->address_label,
                'recipient_name' => $this->recipient_name,
                'address_phone' => $this->address_phone,
                'village' => $this->village,
                'sub_district' => $this->sub_district,
                'district' => $this->district,
                'regency' => $this->regency,
                'city' => $this->city,
                'province' => $this->province,
                'postal_code' => $this->postal_code,
                'detailed_address' => $this->detailed_address,
                'notes' => $this->notes,
                'is_default' => $this->is_default
            ]
        ]);

        $addressData = [
            'user_id' => Auth::id(),
            'label' => $this->address_label,
            'recipient_name' => $this->recipient_name,
            'phone' => $this->address_phone,
            'village' => $this->village,
            'sub_district' => $this->sub_district,
            'district' => $this->district, // Keep for backward compatibility
            'regency' => $this->regency,
            'city' => $this->city, // Keep for backward compatibility
            'province' => $this->province,
            'postal_code' => $this->postal_code,
            'detailed_address' => $this->detailed_address,
            'notes' => $this->notes,
            'is_default' => $this->is_default,
        ];

        try {
            if ($this->editingAddressId) {
                // Update existing address
                $address = UserAddress::where('address_id', $this->editingAddressId)
                    ->where('user_id', Auth::id())
                    ->first();

                if ($address) {
                    $address->update($addressData);
                    \Log::info('Address updated successfully', ['address_id' => $address->address_id]);
                    
                    // Auto-geocoding: Get coordinates for updated address
                    $checkoutService = app(CheckoutService::class);
                    $geocodingSuccess = $checkoutService->updateAddressCoordinates($address);
                    
                    // Set as default if requested
                    if ($this->is_default) {
                        $address->setAsDefault();
                    }
                    
                    if ($geocodingSuccess) {
                        session()->flash('success', 'Alamat berhasil diperbarui dan koordinat lokasi telah diperoleh!');
                    } else {
                        session()->flash('success', 'Alamat berhasil diperbarui! (Koordinat lokasi akan diperbarui secara otomatis)');
                    }
                } else {
                    \Log::error('Address not found for update', ['address_id' => $this->editingAddressId]);
                    session()->flash('error', 'Alamat tidak ditemukan!');
                    return;
                }
            } else {
                // Create new address
                $address = UserAddress::create($addressData);
                \Log::info('New address created successfully', ['address_id' => $address->address_id]);
                
                // Auto-geocoding: Get coordinates for new address
                $checkoutService = app(CheckoutService::class);
                $geocodingSuccess = $checkoutService->updateAddressCoordinates($address);
                
                // Set as default if requested
                if ($this->is_default) {
                    $address->setAsDefault();
                }
                
                if ($geocodingSuccess) {
                    session()->flash('success', 'Alamat berhasil ditambahkan dan koordinat lokasi telah diperoleh!');
                } else {
                    session()->flash('success', 'Alamat berhasil ditambahkan! (Koordinat lokasi akan diperbarui secara otomatis)');
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to save address', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'address_data' => $addressData
            ]);
            
            session()->flash('error', 'Gagal menyimpan alamat. Silakan coba lagi.');
            return;
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