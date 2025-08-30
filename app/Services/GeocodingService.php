<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Service for geocoding addresses using manual coordinates from alamatsubang.json
 * Only uses coordinates that are manually defined in the JSON file
 */
class GeocodingService
{
    
    private AddressService $addressService;
    
    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }
    
    /**
     * Geocode an address to get latitude and longitude coordinates
     * Now uses direct distance data from alamatsubang.json instead of coordinates
     * 
     * @param string $village
     * @param string $subDistrict
     * @param string $regency
     * @param string $province
     * @param string|null $postalCode
     * @param string|null $detailedAddress
     * @return array|null Returns ['lat' => float, 'lon' => float, 'source' => 'direct_distance'] or null if not found
     */
    public function geocodeAddress($village, $subDistrict, $regency, $province, $postalCode = null, $detailedAddress = null)
    {
        // First, validate if the address exists in our JSON data
        if (!$this->addressService->isValidAddress($village, $subDistrict, $postalCode)) {
            Log::warning('Address validation failed - address not found in JSON data', [
                'village' => $village,
                'sub_district' => $subDistrict,
                'postal_code' => $postalCode
            ]);
            return null; // Return null to indicate "alamat diluar jangkauan"
        }
        
        // Check if we have direct distance data for this village
        $distanceCalculator = app(\App\Services\DistanceCalculatorService::class);
        $directDistance = $distanceCalculator->getDirectDistance($village, $subDistrict, $postalCode);
        
        if ($directDistance !== null) {
            Log::info('Address found in direct distance data', [
                'village' => $village,
                'sub_district' => $subDistrict,
                'postal_code' => $postalCode,
                'distance' => $directDistance
            ]);
            
            // Return dummy coordinates since we now use direct distance data
            // The actual distance calculation will use the direct distance, not these coordinates
            return [
                'lat' => -6.5616, // Dummy coordinate for Subang area
                'lon' => 107.7539, // Dummy coordinate for Subang area
                'source' => 'direct_distance',
                'distance_km' => $directDistance
            ];
        }
        
        // If no direct distance data found, address is not covered for delivery
        Log::info('No direct distance data found - address not covered for delivery', [
            'village' => $village,
            'sub_district' => $subDistrict,
            'postal_code' => $postalCode
        ]);
        
        return null; // Return null to indicate address is not covered for delivery
    }


}