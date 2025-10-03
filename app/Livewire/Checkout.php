<?php

namespace App\Livewire;

use App\Models\UserAddress;
use App\Models\PaymentMethod;
use App\Services\CheckoutService;
use App\Services\DistanceCalculatorService;
use App\Services\AddressService;
// Removed GeocodingService - coordinates no longer needed
use App\Services\MidtransService;
use App\Rules\ReCaptcha;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Checkout extends Component
{
    public $shippingType = 'pickup'; // pickup or delivery
    public $selectedAddressId = null;
    public $addresses = [];
    public $checkoutSummary = [];
    public $notes = '';
    public $showAddressForm = false;
    public $isProcessing = false;
    public $recaptchaToken = '';
    
    // Livewire 3 menggunakan format berbeda untuk listeners
    // Listeners akan didefinisikan dengan method tersendiri
    

    
    // New address form fields using array structure
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
    
    // Property to prevent duplicate submissions
    public $isSubmitting = false;
    
    // Cascading dropdown data
    public $provinces = [];
    public $regencies = [];
    public $subDistricts = [];
    public $villages = [];
    public $postalCodes = [];
    public $addressPreview = '';
    

    
    protected $addressService;

    protected $rules = [
        'shippingType' => 'required|in:pickup,delivery',
        'selectedAddressId' => 'required_if:shippingType,delivery|exists:user_addresses,address_id',
        'notes' => 'nullable|string|max:500'
    ];
    
    // Separate rules for new address form (only used when creating new address)
     protected $newAddressRules = [
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
     ];

    public function mount()
    {
        $this->addressService = app(AddressService::class);
        $this->initializeAddressData();
        $this->loadAddresses();
        $this->calculateSummary();
        
        // Set default recipient name and phone from user
        $user = Auth::user();
        $this->addressForm['recipient_name'] = $user->name;
        $this->addressForm['phone'] = $user->phone ?? '';
    }
    
    /**
     * Hydrate method to ensure services are available after Livewire hydration
     */
    public function hydrate()
    {
        if (!$this->addressService) {
            $this->addressService = app(AddressService::class);
        }
    }
    
    /**
     * Initialize address dropdown data
     */
    public function initializeAddressData()
    {
        // Ensure addressService is initialized
        if (!$this->addressService) {
            $this->addressService = app(AddressService::class);
        }
        
        $this->provinces = $this->addressService->getProvinces();
        
        // Auto-select "Jawa Barat" as default
        if (!empty($this->provinces)) {
            $this->addressForm['province_key'] = $this->provinces[0]['key'];
            $this->updateRegencies();
        }
    }

    public function loadAddresses()
    {
        $this->addresses = UserAddress::where('user_id', Auth::id())
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Auto-select default address if exists
        $defaultAddress = $this->addresses->where('is_default', true)->first();
        if ($defaultAddress && $this->shippingType === 'delivery') {
            $this->selectedAddressId = $defaultAddress->address_id;
        }
    }



    public function updatedShippingType()
    {
        if ($this->shippingType === 'pickup') {
            $this->selectedAddressId = null;
        } else {
            // Auto-select default address for delivery
            $defaultAddress = $this->addresses->where('is_default', true)->first();
            if ($defaultAddress) {
                $this->selectedAddressId = $defaultAddress->address_id;
            }
        }
        
        $this->calculateSummary();
    }

    public function updatedSelectedAddressId()
    {
        $this->calculateSummary();
    }
    
    /**
     * Update regencies when province changes
     */
    public function updateRegencies()
    {
        // Ensure addressService is initialized
        if (!$this->addressService) {
            $this->addressService = app(AddressService::class);
        }
        
        if ($this->addressForm['province_key']) {
            $this->regencies = $this->addressService->getRegencies($this->addressForm['province_key']);
            
            // Auto-select "Subang" as default
            if (!empty($this->regencies)) {
                $this->addressForm['regency_key'] = $this->regencies[0]['key'];
                $this->updateSubDistricts();
            }
        } else {
            $this->regencies = [];
            $this->subDistricts = [];
            $this->villages = [];
            $this->postalCodes = [];
        }
        
        $this->updateAddressPreview();
    }
    
    /**
     * Update sub districts when regency changes
     */
    public function updateSubDistricts()
    {
        // Ensure addressService is initialized
        if (!$this->addressService) {
            $this->addressService = app(AddressService::class);
        }
        
        if ($this->addressForm['regency_key']) {
            $this->subDistricts = $this->addressService->getSubDistricts($this->addressForm['regency_key']);
            $this->addressForm['sub_district_key'] = '';
            $this->villages = [];
            $this->postalCodes = [];
        } else {
            $this->subDistricts = [];
            $this->villages = [];
            $this->postalCodes = [];
        }
        
        $this->updateAddressPreview();
    }
    
    /**
     * Update villages when sub district changes
     */
    public function updateVillages()
    {
        // Ensure addressService is initialized
        if (!$this->addressService) {
            $this->addressService = app(AddressService::class);
        }
        
        if ($this->addressForm['sub_district_key']) {
            $this->villages = $this->addressService->getVillages(
                $this->addressForm['province_key'],
                $this->addressForm['regency_key'],
                $this->addressForm['sub_district_key']
            );
            $this->addressForm['village_key'] = '';
            $this->postalCodes = [];
        } else {
            $this->villages = [];
            $this->postalCodes = [];
        }
        
        $this->updateAddressPreview();
    }
    
    /**
     * Update postal codes when village changes
     */
    public function updatePostalCodes()
    {
        // Ensure addressService is initialized
        if (!$this->addressService) {
            $this->addressService = app(AddressService::class);
        }
        
        if ($this->addressForm['village_key']) {
            $this->postalCodes = $this->addressService->getPostalCodes(
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
        } else {
            $this->postalCodes = [];
            $this->addressForm['postal_code'] = '';
        }
        
        $this->updateAddressPreview();
    }
    
    /**
     * Update address preview
     */
    public function updateAddressPreview()
    {
        $preview = [];
        
        if ($this->addressForm['village_key'] && isset($this->villages[$this->addressForm['village_key']])) {
            $preview[] = $this->villages[$this->addressForm['village_key']];
        }
        
        if ($this->addressForm['sub_district_key'] && isset($this->subDistricts[$this->addressForm['sub_district_key']])) {
            $preview[] = $this->subDistricts[$this->addressForm['sub_district_key']];
        }
        
        if ($this->addressForm['regency_key'] && isset($this->regencies[$this->addressForm['regency_key']])) {
            $preview[] = $this->regencies[$this->addressForm['regency_key']];
        }
        
        if ($this->addressForm['province_key'] && isset($this->provinces[$this->addressForm['province_key']])) {
            $preview[] = $this->provinces[$this->addressForm['province_key']];
        }
        
        $this->addressPreview = implode(', ', $preview);
    }

    public function calculateSummary()
    {
        try {
            $checkoutService = app(CheckoutService::class);
            
            $this->checkoutSummary = $checkoutService->calculateCheckoutSummary(
                Auth::id(),
                $this->shippingType,
                $this->selectedAddressId
            );
        } catch (\Exception $e) {
            $this->checkoutSummary = [
                'error' => $e->getMessage(),
                'cart_items' => [],
                'subtotal' => 0,
                'shipping_cost' => 0,
                'total' => 0,
                'delivery_available' => false
            ];
        }
    }

    public function toggleAddressForm()
    {
        $this->showAddressForm = !$this->showAddressForm;
        
        if (!$this->showAddressForm) {
            $this->resetNewAddressForm();
        }
    }

    public function saveNewAddress()
    {
        // Prevent duplicate submissions
        if ($this->isSubmitting) {
            return;
        }
        
        $this->isSubmitting = true;
        
        // Debug logging
        \Log::info('saveNewAddress function called', [
            'user_id' => Auth::id(),
            'addressForm' => $this->addressForm
        ]);
        
        try {
            // Validate addressForm
            $this->validate($this->newAddressRules);
            
            // Ensure addressService is initialized
            if (!$this->addressService) {
                $this->addressService = app(AddressService::class);
            }
            
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
            $addressNames = $this->addressService->getAddressNames(
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
            
            // Create new address
            $address = UserAddress::create($addressData);
            \Log::info('New address created successfully', ['address_id' => $address->address_id]);
            
            // Set as default if requested
            if ($this->addressForm['is_default']) {
                $address->setAsDefault();
            }
            
            // Show success message
            $this->dispatch('show-toast', 'success', "Alamat berhasil ditambahkan!");
            session()->flash('success', "Alamat berhasil ditambahkan!");
            
            $this->loadAddresses();
            $this->selectedAddressId = $address->address_id;
            $this->showAddressForm = false;
            $this->resetNewAddressForm();
            $this->calculateSummary();
            
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
     * Set reCAPTCHA token from JavaScript
     * 
     * @param string $token
     * @return void
     */
    #[\Livewire\Attributes\On('set-recaptcha-token')]
    public function setRecaptchaToken($token)
    {
        $this->recaptchaToken = $token;
        Log::info('reCAPTCHA token set', ['token_length' => strlen($this->recaptchaToken)]);
    }

    #[\Livewire\Attributes\On('process-checkout')]
    public function processCheckout()
    {
        try {
            // Prevent duplicate submissions
            if ($this->isSubmitting) {
                Log::info('Preventing duplicate submission');
                return;
            }
            
            $this->isSubmitting = true;
            $this->isProcessing = true;
            
            Log::info('Processing checkout', [
                'shipping_type' => $this->shippingType,
                'has_recaptcha_token' => !empty($this->recaptchaToken),
                'token_length' => strlen($this->recaptchaToken)
            ]);
            
            // Validate reCAPTCHA token first
            $this->validate([
                'recaptchaToken' => ['required', new ReCaptcha(0.5)]
            ], [
                'recaptchaToken.required' => 'Verifikasi keamanan diperlukan. Silakan coba lagi.'
            ]);
            
            // Custom validation for pickup vs delivery
            if ($this->shippingType === 'pickup') {
                // For pickup, only validate required fields except address
                $this->validate([
                    'shippingType' => 'required|in:pickup,delivery',
                    'notes' => 'nullable|string|max:500'
                ]);
            } else {
                // For delivery, validate all fields including address
                $this->validate();
            }
        } catch (\Exception $e) {
            \Log::error('Checkout validation failed', [
                'error' => $e->getMessage(),
                'shipping_type' => $this->shippingType,
                'selected_address_id' => $this->selectedAddressId,
                'validation_rules' => $this->rules
            ]);
            session()->flash('error', 'Validasi gagal: ' . $e->getMessage());
            return;
        }
        
        if (empty($this->checkoutSummary['cart_items'])) {
            session()->flash('error', 'Keranjang kosong!');
            return;
        }

        if ($this->shippingType === 'delivery' && !$this->checkoutSummary['delivery_available']) {
            session()->flash('error', 'Pengiriman tidak tersedia untuk alamat ini (melebihi jarak maksimal)!');
            return;
        }
        


        try {
            $checkoutService = app(CheckoutService::class);
            
            $order = $checkoutService->processCheckout(Auth::id(), [
                'shipping_type' => $this->shippingType,
                'address_id' => $this->selectedAddressId,
                'notes' => $this->notes
            ]);

            Log::info('Order created successfully', [
                'order_id' => $order->order_id,
                'order_number' => $order->order_number,
                'total_amount' => $order->total_amount
            ]);

            // Create Midtrans SNAP Token
            $midtransService = app(MidtransService::class);
            $snapResult = $midtransService->createSnapToken($order);
            
            if ($snapResult['success']) {
                // Update payment with SNAP token
                $order->payment->update([
                    'snap_token' => $snapResult['snap_token']
                ]);
                
                // Update order status to waiting payment
                $order->update([
                    'status' => 'waiting_payment',
                    'waiting_payment_at' => now()
                ]);
                
                Log::info('SNAP Token created successfully', [
                    'order_id' => $order->order_id,
                    'order_number' => $order->order_number
                ]);
                
                session()->flash('success', 'Pesanan berhasil dibuat! Nomor pesanan: ' . $order->order_number);
                
                // Store in session and redirect to payment page
                session([
                    'order_id' => $order->order_id,
                    'snap_token' => $snapResult['snap_token']
                ]);
                
                return redirect()->route('payment.snap');
            } else {
                Log::error('Failed to create Midtrans SNAP Token', [
                    'order_id' => $order->order_id,
                    'error' => $snapResult['message']
                ]);
                
                // Update order status to pending (SNAP token failed)
                $order->update(['status' => 'pending']);
                
                session()->flash('error', 'Pesanan dibuat tetapi gagal membuat token pembayaran: ' . $snapResult['message']);
                return redirect()->route('pelanggan.orders.show', $order->order_id);
            }
            
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        } finally {
            $this->isProcessing = false;
            $this->isSubmitting = false;
        }
    }

    private function resetNewAddressForm()
    {
        $user = Auth::user();
        
        // Reset new addressForm
        $this->addressForm = [
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
        
        // Reset cascading dropdown data
        $this->regencies = [];
        $this->subDistricts = [];
        $this->villages = [];
        $this->postalCodes = [];
        $this->addressPreview = '';
        
        // Reinitialize with default province
        $this->initializeAddressData();
    }

    public function render()
    {
        return view('livewire.checkout')
            ->layout('components.layouts.user');
    }
}