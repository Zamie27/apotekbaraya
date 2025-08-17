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
    public $store_latitude = '';
    public $store_longitude = '';

    // Shipping Settings Properties
    public $shipping_rate_per_km = 0;
    public $max_delivery_distance = 0;
    public $free_shipping_minimum = 0;

    // Test Distance Calculator Properties
    public $testAddress = '';
    public $testResult = null;
    public $isLoadingCoordinates = false;
    public $isTestingDistance = false;

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
        'store_latitude' => 'nullable|numeric|between:-90,90',
        'store_longitude' => 'nullable|numeric|between:-180,180',
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
            $this->store_latitude = StoreSetting::get('store_latitude', '');
            $this->store_longitude = StoreSetting::get('store_longitude', '');

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
            StoreSetting::set('store_latitude', $this->store_latitude, 'number');
            StoreSetting::set('store_longitude', $this->store_longitude, 'number');

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
     * Get coordinates from store address using geocoding with multiple strategies
     * Uses the same approach as customer address geocoding for consistency
     */
    public function getCoordinatesFromAddress()
    {
        try {
            // Validate required address fields - using same field names as customer address form
            $requiredFields = [
                'store_village' => 'Desa/Kelurahan',
                'store_district' => 'Kecamatan', // This maps to sub_district in customer form
                'store_regency' => 'Kabupaten/Kota',
                'store_province' => 'Provinsi'
            ];

            $missingFields = [];
            foreach ($requiredFields as $field => $label) {
                if (empty($this->{$field})) {
                    $missingFields[] = $label;
                }
            }

            if (!empty($missingFields)) {
                $this->errorMessage = 'Field berikut harus diisi: ' . implode(', ', $missingFields);
                return;
            }

            $this->isLoadingCoordinates = true;
            $this->errorMessage = '';
            $this->successMessage = '';

            // Build address with multiple strategies for better geocoding accuracy
            // IMPORTANT: Only use administrative fields (village, sub_district, regency, province, postal_code)
            // DO NOT use detailed address (store_address) as it causes inaccurate coordinates
            $village = $this->store_village ?? '';
            $subDistrict = $this->store_district ?? ''; // This maps to sub_district in customer form
            $regency = $this->store_regency ?? '';
            $province = $this->store_province ?? '';
            $postalCode = $this->store_postal_code ?? '';
            
            $addressStrategies = [
                // Strategy 1: Full administrative address with postal code (most specific)
                $this->buildAdministrativeStoreAddress($village, $subDistrict, $regency, $province, $postalCode),
                // Strategy 2: Administrative areas without postal code
                $this->buildAdministrativeStoreAddress($village, $subDistrict, $regency, $province),
                // Strategy 3: Sub-district, regency and province with postal code
                trim($subDistrict . ', ' . $regency . ', ' . $province . ', ' . $postalCode, ', '),
                // Strategy 4: Sub-district, regency and province only
                trim($subDistrict . ', ' . $regency . ', ' . $province, ', '),
                // Strategy 5: Regency and province only (fallback)
                trim($regency . ', ' . $province, ', ')
            ];
            
            // Debug: Log all strategies to see what's being generated
            foreach ($addressStrategies as $index => $strategy) {
                Log::info("Strategy " . ($index + 1) . " generated: '{$strategy}'");
            }
            
            $lastException = null;
            $distanceService = new DistanceCalculatorService();
            
            // Try each strategy until one succeeds
            foreach ($addressStrategies as $index => $fullAddress) {
                // Skip empty strategies
                if (empty(trim($fullAddress))) {
                    continue;
                }
                
                try {
                    Log::info("Trying geocoding strategy " . ($index + 1) . " for store address: {$fullAddress}");
                    
                    $coordinates = $distanceService->getCoordinatesFromAddress($fullAddress);
                    
                    // Validate coordinates response
                    if (!is_array($coordinates) || !isset($coordinates['latitude']) || !isset($coordinates['longitude'])) {
                        throw new \Exception('Invalid coordinates response from geocoding service.');
                    }

                    $this->store_latitude = $coordinates['latitude'];
                    $this->store_longitude = $coordinates['longitude'];

                    // Auto-save all address fields and coordinates to database
                    StoreSetting::set('store_address', $this->store_address, 'string');
                    StoreSetting::set('store_village', $this->store_village, 'string');
                    StoreSetting::set('store_district', $this->store_district, 'string');
                    StoreSetting::set('store_regency', $this->store_regency, 'string');
                    StoreSetting::set('store_province', $this->store_province, 'string');
                    StoreSetting::set('store_postal_code', $this->store_postal_code, 'string');
                    StoreSetting::set('store_latitude', $this->store_latitude, 'number');
                    StoreSetting::set('store_longitude', $this->store_longitude, 'number');
                    
                    // Clear cache to ensure fresh data
                    StoreSetting::clearCache();

                    $successMsg = 'Koordinat berhasil didapatkan dan disimpan ke database!';
                    $successMsg .= ' (Strategi pencarian: ' . ($index + 1) . ')';
                    if (isset($coordinates['formatted_address'])) {
                        $successMsg .= ' Alamat yang ditemukan: ' . $coordinates['formatted_address'];
                    }
                    
                    $this->successMessage = $successMsg;
                    
                    Log::info('Geocoding successful using strategy ' . ($index + 1) . ' for store address. Coordinates saved: ' . $this->store_latitude . ', ' . $this->store_longitude);
                    
                    // Refresh the page to show updated data
                    $this->dispatch('refresh-page');
                    return;
                    
                } catch (\Exception $e) {
                    $lastException = $e;
                    Log::warning("Geocoding strategy " . ($index + 1) . " failed: " . $e->getMessage());
                    continue;
                }
            }
            
            // If all strategies fail, throw the last exception
            if ($lastException) {
                throw $lastException;
            }
            
            throw new \Exception('Semua strategi geocoding gagal. Silakan periksa alamat yang dimasukkan.');


            
        } catch (\Exception $e) {
            Log::error('Error getting coordinates for store address: ' . $e->getMessage());
            $this->errorMessage = 'Gagal mendapatkan koordinat: ' . $e->getMessage();
        } finally {
            $this->isLoadingCoordinates = false;
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
     * Build administrative store address from area components only
     * Using exact same method as CheckoutService->buildAdministrativeAddress()
     * Now supports optional postal code parameter for more accurate geocoding
     */
    private function buildAdministrativeStoreAddress(string $village, string $subDistrict, string $regency, string $province, string $postalCode = ''): string
    {
        $addressParts = array_filter([
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
     * Test distance calculation from test address to store
     */
    public function testDistanceCalculation()
    {
        try {
            if (empty($this->testAddress)) {
                $this->errorMessage = 'Alamat test harus diisi.';
                return;
            }

            if (empty($this->store_latitude) || empty($this->store_longitude)) {
                $this->errorMessage = 'Koordinat toko belum diatur. Silakan atur koordinat toko terlebih dahulu.';
                return;
            }

            $this->isTestingDistance = true;
            $this->errorMessage = '';

            $distanceService = new DistanceCalculatorService();
            $result = $distanceService->calculateDistanceFromAddress($this->testAddress);

            // Calculate shipping cost
            $shippingData = $distanceService->calculateShippingCost(
                $result['distance_km'],
                $this->free_shipping_minimum // Use minimum as test order total
            );

            $this->testResult = [
                'address' => $result['formatted_address'],
                'distance_km' => $result['distance_km'],
                'distance_text' => $result['distance_text'],
                'duration_text' => $result['duration_text'],
                'shipping_cost' => $shippingData['final_cost'],
                'is_free_shipping' => $shippingData['is_free_shipping'],
                'delivery_available' => $distanceService->isDeliveryAvailable($result['distance_km'])
            ];

            $this->successMessage = 'Test perhitungan jarak berhasil!';
        } catch (\Exception $e) {
            Log::error('Error testing distance calculation: ' . $e->getMessage());
            $this->errorMessage = 'Gagal menghitung jarak: ' . $e->getMessage();
            $this->successMessage = '';
        } finally {
            $this->isTestingDistance = false;
        }
    }

    /**
     * Clear test result
     */
    public function clearTestResult()
    {
        $this->testResult = null;
        $this->testAddress = '';
        $this->successMessage = '';
        $this->errorMessage = '';
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
