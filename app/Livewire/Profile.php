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
use App\Services\AddressService;
// Removed GeocodingService - coordinates no longer needed

class Profile extends Component
{
    use WithFileUploads;

    // Profile fields
    #[Validate('required|string|max:100|regex:/^[a-zA-Z\s]+$/')]
    public $name;

    // Username and email are disabled in the form, no validation needed
    public $username;
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
    
    // Delete confirmation modal
    public $showDeleteModal = false;
    public $addressToDelete = null;
    public $addressToDeleteData = [];
    
    // Address form data
    public $addressForm = [
        'label' => 'rumah',
        'recipient_name' => '',
        'phone' => '',
        'province_key' => '',
        'regency_key' => '',
        'sub_district_key' => '',
        'village_key' => '',
        'postal_code' => '',
        'detailed_address' => '',
        'notes' => '',
        'is_default' => false
    ];
    
    // Cascading dropdown data
    public $provinces = [];
    public $regencies = [];
    public $subDistricts = [];
    public $villages = [];
    public $postalCodes = [];
    public $addressPreview = '';
    
    // Legacy fields for backward compatibility
    #[Validate('required|in:rumah,kantor,kost,lainnya')]
    public $address_label = 'rumah';
    
    #[Validate('required|string|max:100|regex:/^[a-zA-Z\s]+$/')]
    public $recipient_name;
    
    #[Validate('required|string|min:10|max:15|regex:/^[0-9]+$/')]
    public $address_phone;
    
    #[Validate('required|string|max:500')]
    public $address;
    
    // Legacy fields for backward compatibility - initialized with default values
    public $village = ''; // Desa
    public $sub_district = ''; // Kecamatan
    public $district = ''; // Keep for backward compatibility
    public $regency = ''; // Kabupaten
    public $city = ''; // Keep for backward compatibility
    public $province = ''; // Provinsi
    public $postal_code = '';
    public $detailed_address = ''; // Alamat lengkap spesifik untuk kurir
    public $notes = '';
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
        
        // Initialize address dropdown data
        $this->initializeAddressData();
        
        // Load user addresses
        $this->loadAddresses();
    }
    
    /**
     * Initialize address dropdown data
     */
    public function initializeAddressData()
    {
        $addressService = app(AddressService::class);
        $this->provinces = $addressService->getProvinces();
        
        // Auto-select Jawa Barat as default
        if (!empty($this->provinces)) {
            $this->addressForm['province_key'] = $this->provinces[0]['key'];
            $this->updateRegencies();
        }
    }
    
    /**
     * Load user addresses
     */
    public function loadAddresses()
    {
        $this->addresses = Auth::user()->addresses()
            ->select([
                'address_id',
                'label',
                'recipient_name',
                'phone',
                'village',
                'sub_district',
                'district',
                'regency',
                'city',
                'province',
                'postal_code',
                'detailed_address',
                'notes',
                'is_default',
                'province_key',
                'regency_key',
                'sub_district_key',
                'village_key',
                'address'
            ])
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }
    
    /**
     * Update regencies based on selected province
     */
    public function updateRegencies()
    {
        $addressService = app(AddressService::class);
        $this->regencies = $addressService->getRegencies($this->addressForm['province_key']);
        
        // Reset dependent dropdowns
        $this->addressForm['regency_key'] = '';
        $this->subDistricts = [];
        $this->villages = [];
        $this->postalCodes = [];
        $this->addressForm['sub_district_key'] = '';
        $this->addressForm['village_key'] = '';
        $this->addressForm['postal_code'] = '';
        
        // Auto-select Subang if available
        if (!empty($this->regencies)) {
            $this->addressForm['regency_key'] = $this->regencies[0]['key'];
            $this->updateSubDistricts();
        }
        
        $this->updateAddressPreview();
    }
    
    /**
     * Update sub districts based on selected regency
     */
    public function updateSubDistricts()
    {
        $addressService = app(AddressService::class);
        $this->subDistricts = $addressService->getSubDistricts($this->addressForm['province_key'], $this->addressForm['regency_key']);
        
        // Reset dependent dropdowns
        $this->villages = [];
        $this->postalCodes = [];
        $this->addressForm['sub_district_key'] = '';
        $this->addressForm['village_key'] = '';
        $this->addressForm['postal_code'] = '';
        
        $this->updateAddressPreview();
    }
    
    /**
     * Update villages based on selected sub district
     */
    public function updateVillages()
    {
        $addressService = app(AddressService::class);
        $this->villages = $addressService->getVillages(
            $this->addressForm['province_key'], 
            $this->addressForm['regency_key'], 
            $this->addressForm['sub_district_key']
        );
        
        // Reset dependent dropdowns
        $this->postalCodes = [];
        $this->addressForm['village_key'] = '';
        $this->addressForm['postal_code'] = '';
        
        $this->updateAddressPreview();
    }
    
    /**
     * Update postal codes based on selected village
     */
    public function updatePostalCodes()
    {
        $addressService = app(AddressService::class);
        $this->postalCodes = $addressService->getPostalCodes(
            $this->addressForm['province_key'], 
            $this->addressForm['regency_key'], 
            $this->addressForm['sub_district_key'],
            $this->addressForm['village_key']
        );
        
        // Auto-select postal code if only one is available
        if (count($this->postalCodes) === 1) {
            $firstPostalCode = array_values($this->postalCodes)[0];
            $this->addressForm['postal_code'] = is_array($firstPostalCode) ? $firstPostalCode['key'] : (string) $firstPostalCode;
        } else {
            // Clear postal code if multiple options or no options available
            $this->addressForm['postal_code'] = '';
        }
        
        $this->updateAddressPreview();
    }
    
    /**
     * Update address preview
     */
    public function updateAddressPreview()
    {
        $addressService = app(AddressService::class);
        $this->addressPreview = $addressService->buildFullAddress(
            $this->addressForm['province_key'],
            $this->addressForm['regency_key'],
            $this->addressForm['sub_district_key'],
            $this->addressForm['village_key'],
            $this->addressForm['postal_code'],
            $this->addressForm['detailed_address']
        );
    }

    /**
     * Update user profile information
     */
    public function updateProfile()
    {
        $user = Auth::user();
        
        // Validate all fields including unique constraints
        $this->validate([
            'name' => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'phone' => 'nullable|string|min:10|max:15|regex:/^[0-9]+$/',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female',
            'avatar' => 'nullable|image|max:2048'
        ]);
        
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

        // Update user data (excluding email and username as they are disabled)
        $user->update([
            'name' => $this->name,
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
        $this->addressForm = [
            'label' => 'rumah',
            'recipient_name' => '',
            'phone' => '',
            'province_key' => '',
            'regency_key' => '',
            'sub_district_key' => '',
            'village_key' => '',
            'postal_code' => '',
            'detailed_address' => '',
            'notes' => '',
            'is_default' => false
        ];
        
        // Reset dropdown data
        $this->regencies = [];
        $this->subDistricts = [];
        $this->villages = [];
        $this->postalCodes = [];
        $this->addressPreview = '';
        
        // Reset legacy fields
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
        
        // Re-initialize with default province
        if (!empty($this->provinces)) {
            $this->addressForm['province_key'] = $this->provinces[0]['key'];
            $this->updateRegencies();
        }
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
            
            // Fill addressForm with existing data
            $this->addressForm = [
                'label' => $address->label,
                'recipient_name' => $address->recipient_name,
                'phone' => $address->phone,
                'province_key' => $address->province_key ?? '',
                'regency_key' => $address->regency_key ?? '',
                'sub_district_key' => $address->sub_district_key ?? '',
                'village_key' => $address->village_key ?? '',
                'postal_code' => $address->postal_code,
                'detailed_address' => $address->detailed_address ?? '',
                'notes' => $address->notes ?? '',
                'is_default' => $address->is_default
            ];
            
            // Load cascading dropdown data based on existing address
            $this->loadCascadingDataForEdit();
            
            // Fill legacy fields for backward compatibility
            $this->address_label = $address->label;
            $this->recipient_name = $address->recipient_name;
            $this->address_phone = $address->phone;
            $this->village = $address->village ?? '';
            $this->sub_district = $address->sub_district ?? $address->district;
            $this->district = $address->district;
            $this->regency = $address->regency ?? $address->city;
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
     * Load cascading dropdown data for editing
     */
    public function loadCascadingDataForEdit()
    {
        $addressService = app(AddressService::class);
        
        // Load regencies if province is selected
        if (!empty($this->addressForm['province_key'])) {
            $this->regencies = $addressService->getRegencies($this->addressForm['province_key']);
            
            // Load sub districts if regency is selected
            if (!empty($this->addressForm['regency_key'])) {
                $this->subDistricts = $addressService->getSubDistricts(
                    $this->addressForm['province_key'], 
                    $this->addressForm['regency_key']
                );
                
                // Load villages if sub district is selected
                if (!empty($this->addressForm['sub_district_key'])) {
                    $this->villages = $addressService->getVillages(
                        $this->addressForm['province_key'], 
                        $this->addressForm['regency_key'], 
                        $this->addressForm['sub_district_key']
                    );
                    
                    // Load postal codes if village is selected
                    if (!empty($this->addressForm['village_key'])) {
                        $this->postalCodes = $addressService->getPostalCodes(
                            $this->addressForm['province_key'], 
                            $this->addressForm['regency_key'], 
                            $this->addressForm['sub_district_key'],
                            $this->addressForm['village_key']
                        );
                    }
                }
            }
        }
        
        $this->updateAddressPreview();
    }

    // Property to prevent duplicate submissions
    public $isSubmitting = false;

    /**
     * Save address (create or update)
     */
    public function saveAddress()
    {
        // Prevent duplicate submissions
        if ($this->isSubmitting) {
            return;
        }
        
        $this->isSubmitting = true;
        
        // Debug logging
        \Log::info('saveAddress function called', [
            'user_id' => Auth::id(),
            'addressForm' => $this->addressForm,
            'editing_address_id' => $this->editingAddressId
        ]);
        
        try {
            // Validate addressForm
            $this->validate([
                'addressForm.label' => 'required|in:rumah,kantor,kost,lainnya',
                'addressForm.recipient_name' => 'required|string|max:100|regex:/^[a-zA-Z\s]+$/',
                'addressForm.phone' => 'required|string|min:10|max:15|regex:/^[0-9]+$/',
                'addressForm.province_key' => 'required|string',
                'addressForm.regency_key' => 'required|string',
                'addressForm.sub_district_key' => 'required|string',
                'addressForm.village_key' => 'required|string',
                'addressForm.postal_code' => 'required|string|max:10|regex:/^[0-9]+$/',
                'addressForm.detailed_address' => 'required|string|max:1000',
                'addressForm.notes' => 'nullable|string|max:255',
            ]);
            
            // Additional validation: Check if postal code is properly selected
            if (empty($this->addressForm['postal_code'])) {
                $this->isSubmitting = false;
                $this->dispatch('show-toast', 'error', 'Silakan pilih kode pos terlebih dahulu. Tunggu hingga dropdown kode pos terisi setelah memilih desa.');
                session()->flash('error', 'Silakan pilih kode pos terlebih dahulu. Tunggu hingga dropdown kode pos terisi setelah memilih desa.');
                return;
            }
            
            // Validate address completeness - coordinates no longer needed
            // Distance calculation now uses manual JSON data
            
            // Get address service to convert keys to names
            $addressService = app(AddressService::class);
            $addressNames = $addressService->getAddressNames(
                $this->addressForm['province_key'],
                $this->addressForm['regency_key'],
                $this->addressForm['sub_district_key'],
                $this->addressForm['village_key']
            );
            
            // Prepare address data with both keys and names
            $addressData = [
                'user_id' => Auth::id(),
                'label' => $this->addressForm['label'],
                'recipient_name' => $this->addressForm['recipient_name'],
                'phone' => $this->addressForm['phone'],
                
                // New cascading dropdown fields
                'province_key' => $this->addressForm['province_key'],
                'regency_key' => $this->addressForm['regency_key'],
                'sub_district_key' => $this->addressForm['sub_district_key'],
                'village_key' => $this->addressForm['village_key'],
                
                // Address names from keys
                'province' => $addressNames['province'],
                'regency' => $addressNames['regency'],
                'sub_district' => $addressNames['sub_district'],
                'village' => $addressNames['village'],
                
                // Legacy fields for backward compatibility
                'district' => $addressNames['sub_district'],
                'city' => $addressNames['regency'],
                
                'postal_code' => $this->addressForm['postal_code'],
                'detailed_address' => $this->addressForm['detailed_address'],
                'address' => $this->addressForm['detailed_address'], // For backward compatibility
                'notes' => $this->addressForm['notes'],
                'is_default' => $this->addressForm['is_default'],
                
                // Coordinates removed - distance calculation uses JSON data
            ];
            
            // Debug: Address data prepared
            \Log::info('Address data prepared', ['address_data' => $addressData]);

            if ($this->editingAddressId) {
                // Update existing address
                $address = UserAddress::where('address_id', $this->editingAddressId)
                    ->where('user_id', Auth::id())
                    ->first();

                if ($address) {
                    $address->update($addressData);
                    \Log::info('Address updated successfully', ['address_id' => $address->address_id]);
                    
                    // Set as default if requested
                    if ($this->addressForm['is_default']) {
                        $address->setAsDefault();
                    }
                    
                    // Show success message
                    $this->dispatch('show-toast', 'success', 'Alamat berhasil diperbarui!');
                    session()->flash('success', 'Alamat berhasil diperbarui!');
                } else {
                    $this->isSubmitting = false;
                    \Log::error('Address not found for update', ['address_id' => $this->editingAddressId]);
                    $this->dispatch('show-toast', 'error', 'Alamat tidak ditemukan!');
                    session()->flash('error', 'Alamat tidak ditemukan!');
                    return;
                }
            } else {
                // Create new address
                $address = UserAddress::create($addressData);
                \Log::info('New address created successfully', ['address_id' => $address->address_id]);
                
                // Set as default if requested
                if ($this->addressForm['is_default']) {
                    $address->setAsDefault();
                }
                
                // Show success message
                $this->dispatch('show-toast', 'success', 'Alamat berhasil ditambahkan!');
                session()->flash('success', 'Alamat berhasil ditambahkan!');
            }
            
            $this->loadAddresses();
            $this->hideAddressForm();
            
        } catch (\Exception $e) {
            \Log::error('Failed to save address', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'address_data' => $addressData ?? []
            ]);
            
            $this->dispatch('show-toast', 'error', 'Gagal menyimpan alamat. Silakan coba lagi.');
            session()->flash('error', 'Gagal menyimpan alamat. Silakan coba lagi.');
        } finally {
            // Always reset the submission flag
            $this->isSubmitting = false;
        }
    }

    /**
     * Show delete confirmation modal
     */
    public function confirmDeleteAddress($addressId)
    {
        $address = UserAddress::where('address_id', $addressId)
            ->where('user_id', Auth::id())
            ->first();

        if ($address) {
            $this->addressToDelete = $addressId;
            $this->addressToDeleteData = [
                'label' => $address->label,
                'recipient_name' => $address->recipient_name,
                'detailed_address' => $address->detailed_address,
                'village_name' => $address->village_name
            ];
            $this->showDeleteModal = true;
        }
    }

    /**
     * Cancel delete confirmation
     */
    public function cancelDeleteAddress()
    {
        $this->showDeleteModal = false;
        $this->addressToDelete = null;
        $this->addressToDeleteData = [];
    }

    /**
     * Delete address after confirmation
     */
    public function deleteAddress()
    {
        if (!$this->addressToDelete) {
            return;
        }

        $address = UserAddress::where('address_id', $this->addressToDelete)
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

        // Close modal and reset
        $this->cancelDeleteAddress();
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