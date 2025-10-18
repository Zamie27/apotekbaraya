<?php

namespace App\Livewire;

use App\Models\UserAddress;
use App\Models\Prescription;
use App\Services\AddressService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PrescriptionUpload extends Component
{
    use WithFileUploads;

    // Form fields
    public $doctor_name = '';
    public $patient_name = '';
    public $prescription_image;
    public $notes = '';

    // Delivery method fields
    public $delivery_method = 'pickup'; // pickup or delivery
    public $selected_address_id = null;
    public $addresses = [];
    public $show_address_form = false;

    // New address form fields
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

    // Cascading dropdown data
    public $provinces = [];
    public $regencies = [];
    public $sub_districts = [];
    public $villages = [];
    public $postal_codes = [];
    public $address_preview = '';

    protected $address_service;

    protected $rules = [
        'doctor_name' => 'required|string|max:255',
        'patient_name' => 'required|string|max:255',
        'prescription_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        'notes' => 'nullable|string|max:500',
        'delivery_method' => 'required|in:pickup,delivery',
        'selected_address_id' => 'required_if:delivery_method,delivery|exists:user_addresses,address_id',
    ];

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

    public function mount()
    {
        $this->address_service = app(AddressService::class);
        $this->initializeAddressData();
        $this->loadAddresses();

        // Set default recipient name and phone from user
        $user = Auth::user();
        $this->address_form['recipient_name'] = $user->name;
        $this->address_form['phone'] = $user->phone ?? '';
    }

    /**
     * Initialize address dropdown data
     */
    public function initializeAddressData()
    {
        try {
            $this->provinces = $this->address_service->getProvinces();
        } catch (\Exception $e) {
            $this->provinces = [];
        }

        // Initialize other location arrays as empty to prevent null access
        $this->regencies = [];
        $this->sub_districts = [];
        $this->villages = [];
        $this->postal_codes = [];
    }

    /**
     * Load user addresses
     */
    public function loadAddresses()
    {
        $this->addresses = UserAddress::where('user_id', Auth::id())
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Auto-select default address if exists
        $default_address = $this->addresses->where('is_default', true)->first();
        if ($default_address && $this->delivery_method === 'delivery') {
            $this->selected_address_id = $default_address->address_id;
        }
    }

    /**
     * Handle delivery method change
     */
    public function updatedDeliveryMethod()
    {
        if ($this->delivery_method === 'pickup') {
            $this->selected_address_id = null;
        } else {
            // Auto-select default address for delivery
            $default_address = $this->addresses->where('is_default', true)->first();
            if ($default_address) {
                $this->selected_address_id = $default_address->address_id;
            }
        }
    }

    /**
     * Handle prescription image upload
     */
    public function updatedPrescriptionImage()
    {
        // Reset any previous errors first
        $this->resetErrorBag('prescription_image');

        // Check if file is uploaded and not null
        if ($this->prescription_image && is_object($this->prescription_image)) {
            try {
                // Additional safety check for UploadedFile instance
                if (!($this->prescription_image instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)) {
                    throw new \Exception('Invalid file upload object.');
                }

                // Check file size before validation (PHP upload limit check)
                $maxSize = 2 * 1024 * 1024; // 2MB in bytes
                if (method_exists($this->prescription_image, 'getSize') && $this->prescription_image->getSize() > $maxSize) {
                    throw new \Exception('File terlalu besar. Maksimal 2MB.');
                }

                // Validate the uploaded file immediately
                $this->validateOnly('prescription_image');

                // Add success message
                session()->flash('file_uploaded', 'Foto resep berhasil dipilih');
            } catch (\Illuminate\Validation\ValidationException $e) {
                // Reset the file if validation fails
                $this->prescription_image = null;

                // Safely get error message
                $errors = $e->validator->errors();
                $errorMessage = $errors->has('prescription_image')
                    ? $errors->first('prescription_image')
                    : 'File tidak valid';

                $this->addError('prescription_image', $errorMessage);
            } catch (\Exception $e) {
                // Handle other exceptions including upload errors
                $this->prescription_image = null;
                $errorMessage = $e->getMessage();

                // Handle specific upload errors
                if (strpos($errorMessage, 'upload') !== false || strpos($errorMessage, 'size') !== false) {
                    $this->addError('prescription_image', 'File gagal diunggah. Pastikan ukuran file maksimal 2MB.');
                } else {
                    $this->addError('prescription_image', 'Terjadi kesalahan saat mengunggah file: ' . $errorMessage);
                }
            }
        } else {
            // Handle case where upload failed or file is null
            if ($this->prescription_image === null) {
                // Don't show error immediately, user might be clearing the field
                // Only show error if they try to submit without a file
            } else {
                $this->addError('prescription_image', 'File gagal diunggah. Silakan coba lagi dengan file yang lebih kecil (maksimal 2MB).');
            }
        }
    }

    /**
     * Toggle address form visibility
     */
    public function toggleAddressForm()
    {
        $this->show_address_form = !$this->show_address_form;

        if ($this->show_address_form) {
            // Reset form when opening
            $user = Auth::user();
            $this->address_form = [
                'label' => 'rumah',
                'recipient_name' => $user->name,
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
            $this->regencies = [];
            $this->sub_districts = [];
            $this->villages = [];
            $this->postal_codes = [];
            $this->address_preview = '';
        }
    }

    /**
     * Handle province selection
     */
    public function updatedAddressFormProvinceKey()
    {
        if ($this->address_form['province_key']) {
            try {
                $this->regencies = $this->address_service->getRegencies($this->address_form['province_key']);
                $this->address_form['regency_key'] = '';
                $this->sub_districts = [];
                $this->villages = [];
                $this->postal_codes = [];
                $this->updateAddressPreview();
            } catch (\Exception $e) {
                $this->regencies = [];
            }
        }
    }

    /**
     * Handle regency selection
     */
    public function updatedAddressFormRegencyKey()
    {
        if ($this->address_form['regency_key']) {
            try {
                $this->sub_districts = $this->address_service->getSubDistricts($this->address_form['regency_key']);
                $this->address_form['sub_district_key'] = '';
                $this->villages = [];
                $this->postal_codes = [];
                $this->updateAddressPreview();
            } catch (\Exception $e) {
                $this->sub_districts = [];
            }
        }
    }

    /**
     * Handle sub district selection
     */
    public function updatedAddressFormSubDistrictKey()
    {
        if ($this->address_form['sub_district_key']) {
            try {
                $this->villages = $this->address_service->getVillages($this->address_form['sub_district_key']);
                $this->postal_codes = $this->address_service->getPostalCodes($this->address_form['sub_district_key']);
                $this->address_form['village_key'] = '';
                $this->address_form['postal_code'] = '';
                $this->updateAddressPreview();
            } catch (\Exception $e) {
                $this->villages = [];
                $this->postal_codes = [];
            }
        }
    }

    /**
     * Handle village selection
     */
    public function updatedAddressFormVillageKey()
    {
        $this->updateAddressPreview();
    }

    /**
     * Update address preview
     */
    public function updateAddressPreview()
    {
        try {
            $this->address_preview = $this->address_service->buildAddressPreview($this->address_form);
        } catch (\Exception $e) {
            $this->address_preview = '';
        }
    }

    /**
     * Save new address
     */
    public function saveNewAddress()
    {
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

        try {
            // Get location names with safety checks
            $province_data = collect($this->provinces)->firstWhere('key', $this->address_form['province_key']);
            $province_name = (is_array($province_data) && isset($province_data['name'])) ? $province_data['name'] : '';

            $regency_data = collect($this->regencies)->firstWhere('key', $this->address_form['regency_key']);
            $regency_name = (is_array($regency_data) && isset($regency_data['name'])) ? $regency_data['name'] : '';

            $sub_district_data = collect($this->sub_districts)->firstWhere('key', $this->address_form['sub_district_key']);
            $sub_district_name = (is_array($sub_district_data) && isset($sub_district_data['name'])) ? $sub_district_data['name'] : '';

            $village_data = collect($this->villages)->firstWhere('key', $this->address_form['village_key']);
            $village_name = (is_array($village_data) && isset($village_data['name'])) ? $village_data['name'] : '';

            // Create new address
            $address = UserAddress::create([
                'user_id' => Auth::id(),
                'label' => $this->address_form['label'],
                'recipient_name' => $this->address_form['recipient_name'],
                'phone' => $this->address_form['phone'],
                'province' => $province_name,
                'regency' => $regency_name,
                'sub_district' => $sub_district_name,
                'village' => $village_name,
                'postal_code' => $this->address_form['postal_code'],
                'detailed_address' => $this->address_form['detailed_address'],
                'notes' => $this->address_form['notes'],
                'is_default' => $this->address_form['is_default'],
            ]);

            // If this is set as default, unset other defaults
            if ($this->address_form['is_default']) {
                UserAddress::where('user_id', Auth::id())
                    ->where('address_id', '!=', $address->address_id)
                    ->update(['is_default' => false]);
            }

            // Reload addresses and select the new one
            $this->loadAddresses();
            $this->selected_address_id = $address->address_id;

            // Hide form
            $this->show_address_form = false;

            session()->flash('success', 'Alamat berhasil ditambahkan.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menambahkan alamat: ' . $e->getMessage());
        }
    }

    /**
     * Submit prescription
     */
    public function submit()
    {
        $this->validate();

        try {
            // Safety check for prescription image
            if (!$this->prescription_image || !is_object($this->prescription_image)) {
                throw new \Exception('Foto resep tidak valid. Silakan unggah ulang.');
            }

            // Store the uploaded image
            $image_path = $this->prescription_image->store('prescriptions', 'public');

            // Create prescription data
            $prescription_data = [
                'user_id' => Auth::id(),
                'doctor_name' => $this->doctor_name,
                'patient_name' => $this->patient_name,
                'prescription_image' => $image_path,
                'notes' => $this->notes,
                'delivery_method' => $this->delivery_method,
                'status' => 'pending'
            ];

            // Add address if delivery method is delivery
            if ($this->delivery_method === 'delivery' && $this->selected_address_id) {
                $address = UserAddress::find($this->selected_address_id);
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
                }
            }

            // Create prescription
            Prescription::create($prescription_data);

            session()->flash('success', 'Resep berhasil diunggah! Silakan tunggu konfirmasi dari apoteker.');

            return redirect()->route('customer.prescriptions.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengunggah resep: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.prescription-upload');
    }
}
