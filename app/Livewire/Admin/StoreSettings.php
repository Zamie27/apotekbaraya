<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\StoreSetting;
use App\Services\DistanceCalculatorService;
use Illuminate\Support\Facades\Log;

class StoreSettings extends Component
{
    // Store Information Properties
    public $store_name = '';
    public $store_address = ''; // This will be used as detailed_address (specific street address)
    public $store_village = '';
    public $store_district = ''; // This maps to sub_district in customer form (kecamatan)
    public $store_regency = '';
    public $store_province = '';
    public $store_postal_code = '';
    public $store_phone = '';
    public $store_email = '';
    public $store_hours = ''; // Store operating hours for pickup

    // Shipping Settings Properties
    public $shipping_rate_per_km = 0;
    public $max_delivery_distance = 0;
    public $free_shipping_minimum = 0;



    // Success/Error Messages
    public $successMessage = '';
    public $errorMessage = '';

    protected $rules = [
        'store_name' => 'required|string|max:255',
        'store_address' => 'required|string|max:500', // Detailed street address
        'store_village' => 'required|string|max:255', // Desa/Kelurahan
        'store_district' => 'required|string|max:255', // Kecamatan (maps to sub_district)
        'store_regency' => 'required|string|max:255', // Kabupaten/Kota
        'store_province' => 'required|string|max:255', // Provinsi
        'store_postal_code' => 'required|string|max:10', // Kode Pos
        'store_phone' => 'required|string|max:20',
        'store_email' => 'required|email|max:255',
        'store_hours' => 'required|string|max:255', // Store operating hours
        'shipping_rate_per_km' => 'required|numeric|min:0',
        'max_delivery_distance' => 'required|numeric|min:1',
        'free_shipping_minimum' => 'required|numeric|min:0',
    ];

    protected $messages = [
        'store_name.required' => 'Nama toko wajib diisi.',
        'store_address.required' => 'Alamat toko wajib diisi.',
        'store_village.required' => 'Desa/Kelurahan wajib diisi.',
        'store_district.required' => 'Kecamatan wajib diisi.',
        'store_regency.required' => 'Kabupaten/Kota wajib diisi.',
        'store_province.required' => 'Provinsi wajib diisi.',
        'store_postal_code.required' => 'Kode pos wajib diisi.',
        'store_phone.required' => 'Nomor telepon wajib diisi.',
        'store_email.required' => 'Email toko wajib diisi.',
        'store_email.email' => 'Format email tidak valid.',
        'store_hours.required' => 'Jam operasional toko wajib diisi.',
        'shipping_rate_per_km.required' => 'Tarif pengiriman per km wajib diisi.',
        'shipping_rate_per_km.numeric' => 'Tarif pengiriman harus berupa angka.',
        'max_delivery_distance.required' => 'Jarak pengiriman maksimal wajib diisi.',
        'free_shipping_minimum.required' => 'Minimum gratis ongkir wajib diisi.',
    ];

    /**
     * Component initialization
     */
    public function mount()
    {
        $this->loadSettings();
    }

    /**
     * Load all settings from database
     */
    public function loadSettings()
    {
        try {
            // Load store information
            $this->store_name = StoreSetting::get('store_name', 'Apotek Baraya');
            $this->store_address = StoreSetting::get('store_address', '');
            $this->store_village = StoreSetting::get('store_village', '');
            $this->store_district = StoreSetting::get('store_district', '');
            $this->store_regency = StoreSetting::get('store_regency', '');
            $this->store_province = StoreSetting::get('store_province', '');
            $this->store_postal_code = StoreSetting::get('store_postal_code', '');
            $this->store_phone = StoreSetting::get('store_phone', '');
            $this->store_email = StoreSetting::get('store_email', '');
            $this->store_hours = StoreSetting::get('store_hours', 'Senin-Sabtu: 08:00-20:00');

            // Load shipping settings
            $this->shipping_rate_per_km = StoreSetting::get('shipping_rate_per_km', 2000);
            $this->max_delivery_distance = StoreSetting::get('max_delivery_distance', 15);
            $this->free_shipping_minimum = StoreSetting::get('free_shipping_minimum', 100000);
        } catch (\Exception $e) {
            Log::error('Error loading store settings: ' . $e->getMessage());
            $this->errorMessage = 'Gagal memuat pengaturan toko.';
        }
    }

    /**
     * Update all settings to database
     */
    public function updateSettings()
    {
        try {
            $this->validate();

            // Save store information
            StoreSetting::set('store_name', $this->store_name, 'string');
            StoreSetting::set('store_address', $this->store_address, 'string');
            StoreSetting::set('store_village', $this->store_village, 'string');
            StoreSetting::set('store_district', $this->store_district, 'string');
            StoreSetting::set('store_regency', $this->store_regency, 'string');
            StoreSetting::set('store_province', $this->store_province, 'string');
            StoreSetting::set('store_postal_code', $this->store_postal_code, 'string');
            StoreSetting::set('store_phone', $this->store_phone, 'string');
            StoreSetting::set('store_email', $this->store_email, 'string');
            StoreSetting::set('store_hours', $this->store_hours, 'string');

            // Save shipping settings
            StoreSetting::set('shipping_rate_per_km', $this->shipping_rate_per_km, 'number');
            StoreSetting::set('max_delivery_distance', $this->max_delivery_distance, 'number');
            StoreSetting::set('free_shipping_minimum', $this->free_shipping_minimum, 'number');

            $this->successMessage = 'Pengaturan toko berhasil disimpan!';
            $this->errorMessage = '';

            // Clear cache
            StoreSetting::clearCache();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->errorMessage = 'Validasi gagal. Periksa kembali data yang dimasukkan.';
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error updating store settings: ' . $e->getMessage());
            $this->errorMessage = 'Gagal menyimpan pengaturan toko.';
            $this->successMessage = '';
        }
    }



    /**
     * Build full store address string from detailed components
     * Using exact same method as CheckoutService->buildFullAddress()
     */
    private function buildFullStoreAddress(string $detailedAddress, string $village, string $subDistrict, string $regency, string $province, string $postalCode = ''): string
    {
        $addressParts = array_filter([
            $detailedAddress,
            $village,
            $subDistrict,
            $regency,
            $province,
            $postalCode
        ], function($part) {
            return !empty(trim($part));
        });
        
        return implode(', ', $addressParts);
    }



    /**
     * Simplify detailed address by removing RT/RW and other very specific details
     * that might not be recognized by geocoding services
     * Using exact same method as CheckoutService->simplifyDetailedAddress()
     */
    private function simplifyDetailedAddress(string $address): string
    {
        // Remove RT/RW patterns
        $simplified = preg_replace('/\b(?:RT|RW)\s*[\.\/]?\s*\d+\b/i', '', $address);
        
        // Remove Dusun/Dukuh patterns if they're at the beginning
        $simplified = preg_replace('/^(?:Dusun|Dukuh)\s+[^,]+,?\s*/i', '', $simplified);
        
        // Remove multiple commas and spaces
        $simplified = preg_replace('/,\s*,+/', ',', $simplified);
        $simplified = preg_replace('/\s+/', ' ', $simplified);
        
        // Remove leading/trailing commas and spaces
        $simplified = trim($simplified, ', ');
        
        return $simplified;
    }



    /**
     * Clear all messages
     */
    public function clearMessages()
    {
        $this->successMessage = '';
        $this->errorMessage = '';
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.admin.store-settings')
            ->layout('components.layouts.admin');
    }
}
