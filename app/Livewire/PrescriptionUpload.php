<?php

namespace App\Livewire;

use App\Models\UserAddress;
use App\Models\Prescription;
use App\Models\User;
use App\Mail\NewPrescriptionUploaded;
use App\Services\AddressService;
use App\Services\DistanceCalculatorService;
use App\Models\StoreSetting;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PrescriptionUpload extends Component
{
    use WithFileUploads;

    // Basic prescription fields
    public $doctor_name = '';
    public $patient_name = '';
    public $prescription_image;
    public $notes = '';
    
    // Delivery method
    public $delivery_method = 'pickup'; // pickup or delivery
    
    // Address management
    public $selected_address_id = null;
    public $addresses = [];
    public $show_address_form = false;
    
    // Distance validation properties
    public $distance_warning = '';
    public $is_delivery_available = true;
    public $calculated_distance = null;
    
    // Form submission state
    public $isSubmitting = false;
    
    // New address form
    public $address_form = [
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
    
    // Location data - always initialized as arrays
    public $provinces = [];
    public $regencies = [];
    public $subDistricts = [];
    public $villages = [];
    public $postalCodes = [];
    public $address_preview = '';
    
    protected $address_service;
    protected $distance_calculator;

    /**
     * Validation rules
     */
    protected $rules = [
        'doctor_name' => 'required|string|max:255',
        'patient_name' => 'required|string|max:255',
        'prescription_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        'notes' => 'nullable|string|max:500',
        'delivery_method' => 'required|in:pickup,delivery',
        'selected_address_id' => 'required_if:delivery_method,delivery|nullable|exists:user_addresses,address_id',
    ];

    /**
     * Validation messages
     */
    protected $messages = [
        'doctor_name.required' => 'Nama dokter wajib diisi.',
        'patient_name.required' => 'Nama pasien wajib diisi.',
        'prescription_image.required' => 'Foto resep wajib diunggah.',
        'prescription_image.image' => 'File harus berupa gambar.',
        'prescription_image.mimes' => 'Format gambar harus JPEG, PNG, atau JPG.',
        'prescription_image.max' => 'Ukuran gambar maksimal 2MB.',
        'delivery_method.required' => 'Metode pengambilan obat wajib dipilih.',
        'selected_address_id.required_if' => 'Alamat pengiriman wajib dipilih untuk metode kirim ke alamat.',
    ];

    /**
     * Component initialization
     */
    public function mount()
    {
        try {
            $this->address_service = app(AddressService::class);
            $this->distance_calculator = app(DistanceCalculatorService::class);
            $this->initializeLocationData();
            $this->loadUserAddresses();
            $this->setDefaultUserData();
        } catch (\Exception $e) {
            Log::error('PrescriptionUpload mount error: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat memuat halaman. Silakan refresh halaman.');
        }
    }

    /**
     * Initialize location data safely
     */
    private function initializeLocationData()
    {
        // Always initialize as empty arrays to prevent null access
        $this->provinces = [];
        $this->regencies = [];
        $this->subDistricts = [];
        $this->villages = [];
        $this->postalCodes = [];
        
        try {
            $provinces = $this->address_service->getProvinces();
            if (is_array($provinces)) {
                $this->provinces = $provinces;
                
                // Auto-select Jawa Barat if it's the only province or exists
                $jawaBaratProvince = collect($provinces)->firstWhere('name', 'Jawa Barat');
                if ($jawaBaratProvince) {
                    $this->address_form['province_key'] = $jawaBaratProvince['key'];
                    $this->loadRegenciesForSelectedProvince();
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to load provinces: ' . $e->getMessage());
            $this->provinces = [];
        }
    }

    /**
     * Load regencies for selected province and auto-select Subang
     */
    private function loadRegenciesForSelectedProvince()
    {
        try {
            $regencies = $this->address_service->getRegencies($this->address_form['province_key']);
            if (is_array($regencies)) {
                $this->regencies = $regencies;
                
                // Auto-select Subang if it exists
                $subangRegency = collect($regencies)->firstWhere('name', 'Subang');
                if ($subangRegency) {
                    $this->address_form['regency_key'] = $subangRegency['key'];
                    $this->loadSubDistrictsForSelectedRegency();
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to load regencies: ' . $e->getMessage());
            $this->regencies = [];
        }
    }

    /**
     * Load sub districts for selected regency
     */
    private function loadSubDistrictsForSelectedRegency()
    {
        try {
            $sub_districts = $this->address_service->getSubDistricts($this->address_form['regency_key']);
            if (is_array($sub_districts)) {
                $this->subDistricts = $sub_districts;
            }
        } catch (\Exception $e) {
            Log::error('Failed to load sub districts: ' . $e->getMessage());
            $this->subDistricts = [];
        }
    }

    /**
     * Load user addresses
     */
    private function loadUserAddresses()
    {
        try {
            $this->addresses = UserAddress::where('user_id', Auth::id())
                ->orderBy('is_default', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
                
            // Auto-select default address if exists and delivery method is delivery
            if ($this->delivery_method === 'delivery') {
                $default_address = $this->addresses->where('is_default', true)->first();
                if ($default_address) {
                    $this->selected_address_id = $default_address->address_id;
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to load user addresses: ' . $e->getMessage());
            $this->addresses = collect();
        }
    }

    /**
     * Set default user data
     */
    private function setDefaultUserData()
    {
        try {
            $user = Auth::user();
            if ($user) {
                $this->address_form['recipient_name'] = $user->name ?? '';
                $this->address_form['phone'] = $user->phone ?? '';
            }
        } catch (\Exception $e) {
            Log::error('Failed to set default user data: ' . $e->getMessage());
        }
    }

    /**
     * Handle prescription image upload
     */
    public function updatedPrescriptionImage()
    {
        $this->resetErrorBag('prescription_image');
        
        try {
            $this->validateOnly('prescription_image');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->prescription_image = null;
            throw $e;
        }
    }

    /**
     * Handle delivery method change
     */
    public function updatedDeliveryMethod()
    {
        $this->resetErrorBag('selected_address_id');
        
        if ($this->delivery_method === 'pickup') {
            $this->selected_address_id = null;
        } else {
            // Auto-select default address if available
            $default_address = $this->addresses->where('is_default', true)->first();
            if ($default_address) {
                $this->selected_address_id = $default_address->address_id;
            }
        }
    }

    /**
     * Show new address form
     */
    public function showAddressForm()
    {
        $this->show_address_form = true;
        $this->resetAddressForm();
    }

    /**
     * Hide new address form
     */
    public function hideAddressForm()
    {
        $this->show_address_form = false;
        $this->resetAddressForm();
    }

    /**
     * Toggle address form visibility
     */
    public function toggleAddressForm()
    {
        $this->show_address_form = !$this->show_address_form;
        
        if ($this->show_address_form) {
            $this->resetAddressForm();
        }
    }

    /**
     * Reset address form
     */
    private function resetAddressForm()
    {
        $user = Auth::user();
        $this->address_form = [
            'label' => 'rumah',
            'recipient_name' => $user->name ?? '',
            'phone' => $user->phone ?? '',
            'province_key' => '',
            'regency_key' => '',
            'sub_district_key' => '',
            'village_key' => '',
            'postal_code' => '',
            'detailed_address' => '',
            'notes' => '',
            'is_default' => false
        ];
        
        // Reset dependent fields
        $this->regencies = [];
        $this->subDistricts = [];
        $this->villages = [];
        $this->postalCodes = [];
        $this->address_preview = '';
    }

    /**
     * Handle province selection
     */
    public function updatedAddressFormProvinceKey()
    {
        // Ensure address_service is initialized
        if (!$this->address_service) {
            $this->address_service = app(AddressService::class);
        }
        
        if ($this->address_form['province_key']) {
            try {
                $regencies = $this->address_service->getRegencies($this->address_form['province_key']);
                $this->regencies = is_array($regencies) ? $regencies : [];
                
                // Auto-select "Subang" as default (or first regency if Subang not found)
                if (!empty($this->regencies)) {
                    // Try to find Subang first
                    $subangRegency = collect($this->regencies)->firstWhere('name', 'Subang');
                    if ($subangRegency) {
                        $this->address_form['regency_key'] = $subangRegency['key'];
                    } else {
                        // If Subang not found, select first regency
                        $this->address_form['regency_key'] = $this->regencies[0]['key'];
                    }
                    $this->updatedAddressFormRegencyKey();
                }
                
            } catch (\Exception $e) {
                Log::error('Failed to load regencies: ' . $e->getMessage());
                $this->regencies = [];
            }
        } else {
            // Reset all dependent fields
            $this->regencies = [];
            $this->subDistricts = [];
            $this->villages = [];
            $this->postalCodes = [];
            $this->address_form['regency_key'] = '';
            $this->address_form['sub_district_key'] = '';
            $this->address_form['village_key'] = '';
            $this->address_form['postal_code'] = '';
        }
        
        $this->updateAddressPreview();
    }

    /**
     * Handle regency selection
     */
    public function updatedAddressFormRegencyKey()
    {
        // Ensure address_service is initialized
        if (!$this->address_service) {
            $this->address_service = app(AddressService::class);
        }
        
        if ($this->address_form['regency_key']) {
            try {
                $sub_districts = $this->address_service->getSubDistricts($this->address_form['regency_key']);
                $this->subDistricts = is_array($sub_districts) ? $sub_districts : [];
                
                // Reset dependent fields
                $this->address_form['sub_district_key'] = '';
                $this->villages = [];
                $this->postalCodes = [];
                
            } catch (\Exception $e) {
                Log::error('Failed to load sub districts: ' . $e->getMessage());
                $this->subDistricts = [];
            }
        } else {
            $this->subDistricts = [];
            $this->villages = [];
            $this->postalCodes = [];
            $this->address_form['sub_district_key'] = '';
            $this->address_form['village_key'] = '';
            $this->address_form['postal_code'] = '';
        }
        
        $this->updateAddressPreview();
    }

    /**
     * Handle sub district selection
     */
    public function updatedAddressFormSubDistrictKey()
    {
        // Ensure address_service is initialized
        if (!$this->address_service) {
            $this->address_service = app(AddressService::class);
        }
        
        if ($this->address_form['sub_district_key']) {
            try {
                $villages = $this->address_service->getVillages($this->address_form['sub_district_key']);
                $this->villages = is_array($villages) ? $villages : [];
                
                $postal_codes = $this->address_service->getPostalCodes($this->address_form['sub_district_key']);
                $this->postalCodes = is_array($postal_codes) ? $postal_codes : [];
                
                // Reset dependent fields
                $this->address_form['village_key'] = '';
                $this->address_form['postal_code'] = '';
                
            } catch (\Exception $e) {
                Log::error('Failed to load villages: ' . $e->getMessage());
                $this->villages = [];
                $this->postalCodes = [];
            }
        } else {
            $this->villages = [];
            $this->postalCodes = [];
            $this->address_form['village_key'] = '';
            $this->address_form['postal_code'] = '';
        }
        
        $this->updateAddressPreview();
    }

    /**
     * Handle village selection
     */
    public function updatedAddressFormVillageKey()
    {
        // Ensure address_service is initialized
        if (!$this->address_service) {
            $this->address_service = app(AddressService::class);
        }
        
        if ($this->address_form['village_key']) {
            try {
                $this->postalCodes = $this->address_service->getPostalCodes(
                    $this->address_form['province_key'], 
                    $this->address_form['regency_key'], 
                    $this->address_form['sub_district_key'],
                    $this->address_form['village_key']
                );
                
                // Auto-select postal code if only one is available
                if (count($this->postalCodes) === 1) {
                    $firstPostalCode = array_values($this->postalCodes)[0];
                    $this->address_form['postal_code'] = is_array($firstPostalCode) ? $firstPostalCode['key'] : (string) $firstPostalCode;
                } else {
                    // Clear postal code if multiple options or no options available
                    $this->address_form['postal_code'] = '';
                }
            } catch (\Exception $e) {
                Log::error('Failed to load postal codes: ' . $e->getMessage());
                $this->postalCodes = [];
                $this->address_form['postal_code'] = '';
            }
        } else {
            $this->postalCodes = [];
            $this->address_form['postal_code'] = '';
        }
        
        $this->updateAddressPreview();
        
        // Validate distance for new address if delivery method is delivery
        if ($this->delivery_method === 'delivery') {
            $this->validateNewAddressDistance();
        }
    }

    /**
     * Handle postal code change
     */
    public function updatedAddressFormPostalCode()
    {
        $this->updateAddressPreview();
        
        // Validate distance for new address if delivery method is delivery
        if ($this->delivery_method === 'delivery') {
            $this->validateNewAddressDistance();
        }
    }

    /**
     * Update regencies when province changes (called by address-form component)
     */
    public function updateRegencies()
    {
        $this->updatedAddressFormProvinceKey();
    }

    /**
     * Update sub districts when regency changes (called by address-form component)
     */
    public function updateSubDistricts()
    {
        $this->updatedAddressFormRegencyKey();
    }

    /**
     * Update villages when sub district changes (called by address-form component)
     */
    public function updateVillages()
    {
        $this->updatedAddressFormSubDistrictKey();
    }

    /**
     * Update postal codes when village changes (called by address-form component)
     */
    public function updatePostalCodes()
    {
        $this->updatedAddressFormVillageKey();
    }

    /**
     * Update address preview
     */
    private function updateAddressPreview()
    {
        try {
            // Ensure address_service is initialized
            if (!$this->address_service) {
                $this->address_service = app(AddressService::class);
            }
            
            $this->address_preview = $this->address_service->buildAddressPreview($this->address_form);
        } catch (\Exception $e) {
            Log::error('Failed to update address preview: ' . $e->getMessage());
            $this->address_preview = '';
        }
    }

    /**
     * Save new address
     */
    public function saveNewAddress()
    {
        // Prevent duplicate submissions
        if ($this->isSubmitting ?? false) {
            return;
        }
        
        $this->isSubmitting = true;
        
        // Debug logging
        \Log::info('saveNewAddress function called', [
            'user_id' => Auth::id(),
            'address_form' => $this->address_form
        ]);
        
        try {
            // Validate address form
            $this->validate([
                'address_form.label' => 'required|string|max:50',
                'address_form.recipient_name' => 'required|string|max:255',
                'address_form.phone' => 'required|string|max:20',
                'address_form.province_key' => 'required|string',
                'address_form.regency_key' => 'required|string',
                'address_form.sub_district_key' => 'required|string',
                'address_form.village_key' => 'required|string',
                'address_form.postal_code' => 'required|string|max:10',
                'address_form.detailed_address' => 'required|string|max:500',
            ], [
                'address_form.label.required' => 'Label alamat wajib diisi.',
                'address_form.recipient_name.required' => 'Nama penerima wajib diisi.',
                'address_form.phone.required' => 'Nomor telepon wajib diisi.',
                'address_form.province_key.required' => 'Provinsi wajib dipilih.',
                'address_form.regency_key.required' => 'Kabupaten/Kota wajib dipilih.',
                'address_form.sub_district_key.required' => 'Kecamatan wajib dipilih.',
                'address_form.village_key.required' => 'Desa/Kelurahan wajib dipilih.',
                'address_form.postal_code.required' => 'Kode pos wajib diisi.',
                'address_form.detailed_address.required' => 'Alamat lengkap wajib diisi.',
            ]);

            // Additional validation: Check if postal code is properly selected
            if (empty($this->address_form['postal_code'])) {
                $this->isSubmitting = false;
                $this->dispatch('show-toast', 'error', 'Silakan pilih kode pos terlebih dahulu. Tunggu hingga dropdown kode pos terisi setelah memilih desa.');
                session()->flash('error', 'Silakan pilih kode pos terlebih dahulu. Tunggu hingga dropdown kode pos terisi setelah memilih desa.');
                return;
            }

            // Ensure address_service is initialized
            if (!$this->address_service) {
                $this->address_service = app(AddressService::class);
            }

            // Get address service to convert keys to names
            $addressNames = $this->address_service->getAddressNames(
                $this->address_form['province_key'],
                $this->address_form['regency_key'],
                $this->address_form['sub_district_key'],
                $this->address_form['village_key']
            );

            // Prepare address data with both keys and names
            $addressData = [
                'user_id' => Auth::id(),
                'label' => $this->address_form['label'],
                'recipient_name' => $this->address_form['recipient_name'],
                'phone' => $this->address_form['phone'],
                
                // New cascading dropdown fields
                'province_key' => $this->address_form['province_key'],
                'regency_key' => $this->address_form['regency_key'],
                'sub_district_key' => $this->address_form['sub_district_key'],
                'village_key' => $this->address_form['village_key'],
                
                // Address names from keys
                'province' => $addressNames['province'],
                'regency' => $addressNames['regency'],
                'sub_district' => $addressNames['sub_district'],
                'village' => $addressNames['village'],
                
                // Legacy fields for backward compatibility
                'district' => $addressNames['sub_district'],
                'city' => $addressNames['regency'],
                
                'postal_code' => $this->address_form['postal_code'],
                'detailed_address' => $this->address_form['detailed_address'],
                'address' => $this->address_form['detailed_address'], // For backward compatibility
                'notes' => $this->address_form['notes'],
                'is_default' => $this->address_form['is_default'],
            ];

            // Debug: Address data prepared
            \Log::info('Address data prepared', ['address_data' => $addressData]);

            // Create new address
            $address = UserAddress::create($addressData);
            \Log::info('New address created successfully', ['address_id' => $address->address_id]);

            // Set as default if requested
            if ($this->address_form['is_default']) {
                $address->setAsDefault();
            }

            // Show success message
            $this->dispatch('show-toast', 'success', "Alamat berhasil ditambahkan!");
            session()->flash('success', "Alamat berhasil ditambahkan!");

            // Reload addresses and select the new one
            $this->loadUserAddresses();
            $this->selected_address_id = $address->address_id;
            $this->hideAddressForm();
            $this->resetAddressForm();
            
        } catch (\Exception $e) {
            \Log::error('Failed to save address', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'address_form' => $this->address_form
            ]);
            
            $this->dispatch('show-toast', 'error', 'Gagal menambahkan alamat: ' . $e->getMessage());
            session()->flash('error', 'Gagal menambahkan alamat: ' . $e->getMessage());
        } finally {
            $this->isSubmitting = false;
        }
    }

    /**
     * Get location name safely
     */
    private function getLocationName($locations, $key)
    {
        if (!is_array($locations) || empty($key)) {
            return '';
        }

        foreach ($locations as $location) {
            if (is_array($location) && isset($location['key'], $location['name']) && $location['key'] === $key) {
                return $location['name'];
            }
        }

        return '';
    }

    /**
     * Submit prescription
     */
    public function submit()
    {
        // Reset any previous errors
        $this->resetErrorBag();
        
        try {
            // Validate all fields
            $this->validate();

            // Additional safety check for prescription image
            if (!$this->prescription_image || !is_object($this->prescription_image)) {
                $this->addError('prescription_image', 'Foto resep tidak valid. Silakan unggah ulang.');
                return;
            }
            
            // Generate unique prescription number first for image naming
            $prescription_number = 'RX-' . strtoupper(\Illuminate\Support\Str::random(8));
            
            // Store the uploaded image with custom naming format
            $image_name = 'RESEP-' . $prescription_number . '.jpg';
            $image_path = $this->prescription_image->storeAs('prescriptions', $image_name, 'public');
            
            if (!$image_path) {
                $this->addError('prescription_image', 'Gagal menyimpan foto resep. Silakan coba lagi.');
                return;
            }
            
            // Prepare prescription data
            $prescription_data = [
                'prescription_number' => $prescription_number,
                'user_id' => Auth::id(),
                'doctor_name' => trim($this->doctor_name),
                'patient_name' => trim($this->patient_name),
                'prescription_image' => $image_path,
                'file' => $image_path, // Fill file field with same value as prescription_image
                'notes' => trim($this->notes),
                'delivery_method' => $this->delivery_method,
                'status' => 'pending'
            ];

            // Add delivery address if needed
            if ($this->delivery_method === 'delivery' && $this->selected_address_id) {
                $address = UserAddress::where('address_id', $this->selected_address_id)
                    ->where('user_id', Auth::id())
                    ->first();
                    
                if ($address) {
                    $prescription_data['delivery_address'] = [
                        'address_id' => $address->address_id,
                        'recipient_name' => $address->recipient_name,
                        'phone' => $address->phone,
                        'detailed_address' => $address->detailed_address,
                        'village' => $address->village,
                        'sub_district' => $address->sub_district,
                        'regency' => $address->regency,
                        'province' => $address->province,
                        'postal_code' => $address->postal_code,
                        'notes' => $address->notes,
                    ];
                } else {
                    $this->addError('selected_address_id', 'Alamat pengiriman tidak valid.');
                    return;
                }
            }

            // Create prescription
            $prescription = Prescription::create($prescription_data);
            
            if (!$prescription) {
                $this->addError('general', 'Gagal menyimpan data resep. Silakan coba lagi.');
                return;
            }

            // Send notification to pharmacists
            try {
                // Get all pharmacist users
                $pharmacists = User::whereHas('role', function($query) {
                    $query->where('name', 'apoteker');
                })->get();
                
                foreach ($pharmacists as $pharmacist) {
                    Mail::to($pharmacist->email)
                        ->send(new NewPrescriptionUploaded($prescription));
                }
                
                Log::info('Prescription notification sent to pharmacists', [
                    'prescription_id' => $prescription->prescription_id,
                    'pharmacist_count' => $pharmacists->count()
                ]);
            } catch (\Exception $e) {
                // Log email error but don't fail the transaction
                Log::error('Failed to send prescription notification: ' . $e->getMessage(), [
                    'prescription_id' => $prescription->prescription_id,
                    'trace' => $e->getTraceAsString()
                ]);
            }

            // Log successful submission
            Log::info('Prescription uploaded successfully', [
                'prescription_id' => $prescription->prescription_id,
                'user_id' => Auth::id(),
                'doctor_name' => $this->doctor_name,
                'delivery_method' => $this->delivery_method
            ]);

            // Clear form data
            $this->reset([
                'doctor_name', 
                'patient_name', 
                'prescription_image', 
                'notes', 
                'delivery_method', 
                'selected_address_id'
            ]);
            
            // Set success message and redirect
            session()->flash('success', 'Resep berhasil diunggah! Silakan tunggu konfirmasi dari apoteker.');
            
            return redirect()->route('customer.prescriptions.index');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Let Livewire handle validation errors automatically
            throw $e;
        } catch (\Exception $e) {
            Log::error('Prescription submission error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->addError('general', 'Terjadi kesalahan saat mengunggah resep. Silakan coba lagi.');
        }
    }

    /**
     * Render component
     */
    public function render()
    {
        return view('livewire.prescription-upload');
    }

    /**
     * Handle selected address change
     */
    public function updatedSelectedAddressId()
    {
        if ($this->selected_address_id && $this->delivery_method === 'delivery') {
            $this->validateAddressDistance();
        } else {
            $this->resetDistanceValidation();
        }
    }

    /**
     * Validate distance for selected address
     */
    private function validateAddressDistance()
    {
        try {
            if (!$this->selected_address_id) {
                $this->resetDistanceValidation();
                return;
            }

            $address = UserAddress::where('address_id', $this->selected_address_id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$address) {
                $this->resetDistanceValidation();
                return;
            }

            // Initialize distance calculator if not already done
            if (!$this->distance_calculator) {
                $this->distance_calculator = app(DistanceCalculatorService::class);
            }

            // Get distance using direct distance calculation
            $distance = $this->distance_calculator->getDirectDistance(
                $address->village,
                $address->sub_district,
                $address->postal_code
            );

            if ($distance !== null) {
                $this->calculated_distance = $distance;
                $this->is_delivery_available = $this->distance_calculator->isDeliveryAvailable($distance);
                
                $maxDistance = StoreSetting::get('max_delivery_distance', 15);
                
                if (!$this->is_delivery_available) {
                    $this->distance_warning = "Alamat ini berada di luar jangkauan pengiriman kami (jarak: {$distance} km, maksimal: {$maxDistance} km). Silakan pilih alamat lain atau gunakan metode ambil di apotek.";
                } else if ($distance > ($maxDistance * 0.8)) {
                    // Show warning when distance is more than 80% of max distance
                    $this->distance_warning = "Alamat ini berada di ujung jangkauan pengiriman kami (jarak: {$distance} km dari {$maxDistance} km maksimal). Pastikan alamat sudah benar.";
                } else {
                    $this->distance_warning = '';
                }
            } else {
                // If distance cannot be calculated, show warning
                $this->calculated_distance = null;
                $this->is_delivery_available = true; // Allow delivery but with warning
                $this->distance_warning = 'Tidak dapat menghitung jarak untuk alamat ini. Pastikan alamat sudah benar dan lengkap.';
            }

        } catch (\Exception $e) {
            Log::error('Distance validation error: ' . $e->getMessage());
            $this->resetDistanceValidation();
            $this->distance_warning = 'Terjadi kesalahan saat memvalidasi jarak alamat.';
        }
    }

    /**
     * Validate distance for new address form
     */
    private function validateNewAddressDistance()
    {
        try {
            if (empty($this->address_form['village_key']) || empty($this->address_form['sub_district_key'])) {
                $this->resetDistanceValidation();
                return;
            }

            // Get village and sub-district names
            $village_name = $this->getLocationName($this->villages, $this->address_form['village_key']);
            $sub_district_name = $this->getLocationName($this->subDistricts, $this->address_form['sub_district_key']);

            if (empty($village_name) || empty($sub_district_name)) {
                $this->resetDistanceValidation();
                return;
            }

            // Initialize distance calculator if not already done
            if (!$this->distance_calculator) {
                $this->distance_calculator = app(DistanceCalculatorService::class);
            }

            // Get distance using direct distance calculation
            $distance = $this->distance_calculator->getDirectDistance(
                $village_name,
                $sub_district_name,
                $this->address_form['postal_code']
            );

            if ($distance !== null) {
                $this->calculated_distance = $distance;
                $this->is_delivery_available = $this->distance_calculator->isDeliveryAvailable($distance);
                
                $maxDistance = StoreSetting::get('max_delivery_distance', 15);
                
                if (!$this->is_delivery_available) {
                    $this->distance_warning = "Alamat ini berada di luar jangkauan pengiriman kami (jarak: {$distance} km, maksimal: {$maxDistance} km). Silakan pilih lokasi lain atau gunakan metode ambil di apotek.";
                } else if ($distance > ($maxDistance * 0.8)) {
                    // Show warning when distance is more than 80% of max distance
                    $this->distance_warning = "Alamat ini berada di ujung jangkauan pengiriman kami (jarak: {$distance} km dari {$maxDistance} km maksimal). Pastikan alamat sudah benar.";
                } else {
                    $this->distance_warning = '';
                }
            } else {
                // If distance cannot be calculated, show warning
                $this->calculated_distance = null;
                $this->is_delivery_available = true; // Allow delivery but with warning
                $this->distance_warning = 'Tidak dapat menghitung jarak untuk alamat ini. Pastikan alamat sudah benar dan lengkap.';
            }

        } catch (\Exception $e) {
            Log::error('New address distance validation error: ' . $e->getMessage());
            $this->resetDistanceValidation();
            $this->distance_warning = 'Terjadi kesalahan saat memvalidasi jarak alamat.';
        }
    }

    /**
     * Reset distance validation properties
     */
    private function resetDistanceValidation()
    {
        $this->distance_warning = '';
        $this->is_delivery_available = true;
        $this->calculated_distance = null;
    }
}