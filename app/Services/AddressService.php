<?php

namespace App\Services;

class AddressService
{
    private $addressData;
    
    public function __construct()
    {
        $this->loadAddressData();
    }
    
    /**
     * Load address data from JSON file
     */
    private function loadAddressData()
    {
        $jsonPath = base_path('alamatsubang.json');
        
        if (!file_exists($jsonPath)) {
            throw new \Exception('Address data file not found: ' . $jsonPath);
        }
        
        $jsonContent = file_get_contents($jsonPath);
        $this->addressData = json_decode($jsonContent, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON in address data file: ' . json_last_error_msg());
        }
    }
    
    /**
     * Get all provinces (fixed to Jawa Barat only)
     */
    public function getProvinces()
    {
        return [
            'jawa_barat' => 'Jawa Barat'
        ];
    }
    
    /**
     * Get all regencies for given province (fixed to Subang only)
     */
    public function getRegencies($province = null)
    {
        return [
            'subang' => 'Subang'
        ];
    }
    
    /**
     * Get all sub-districts (kecamatan) for Subang regency
     * Accepts multiple parameters for compatibility with cascading dropdown calls
     */
    public function getSubDistricts($province = null, $regency = null)
    {
        if (!$this->addressData) {
            return [];
        }
        
        $subDistricts = [];
        foreach ($this->addressData as $kecamatan => $data) {
            $subDistricts[strtolower(str_replace(' ', '_', $kecamatan))] = $kecamatan;
        }
        
        return $subDistricts;
    }
    
    /**
     * Get all villages (desa) for given sub-district
     * Accepts multiple parameters for compatibility with cascading dropdown calls
     */
    public function getVillages($province = null, $regency = null, $subDistrict = null)
    {
        // If called with single parameter (backward compatibility)
        if ($province && !$regency && !$subDistrict) {
            $subDistrict = $province;
        }
        
        if (!$this->addressData || !$subDistrict) {
            return [];
        }
        
        // Convert sub-district key back to original format
        $originalKey = $this->findOriginalKey($subDistrict);
        
        if (!$originalKey || !isset($this->addressData[$originalKey])) {
            return [];
        }
        
        $villages = [];
        $data = $this->addressData[$originalKey];
        
        // Check if this is the new Subang format with postal codes as keys
        if ($this->isSubangNewFormat($data)) {
            // Handle Subang's new format where postal codes are keys
            foreach ($data as $postalCode => $postalData) {
                if (isset($postalData['desa']) && is_array($postalData['desa'])) {
                    foreach ($postalData['desa'] as $desa) {
                        $villages[strtolower(str_replace(' ', '_', $desa))] = $desa;
                    }
                }
            }
        } else {
            // Handle standard format
            if (isset($data['desa'])) {
                foreach ($data['desa'] as $desa) {
                    $villages[strtolower(str_replace(' ', '_', $desa))] = $desa;
                }
            }
        }
        
        return $villages;
    }
    
    /**
     * Get postal codes for given sub-district and village
     * Accepts multiple parameters for compatibility with cascading dropdown calls
     */
    public function getPostalCodes($province = null, $regency = null, $subDistrict = null, $village = null)
    {
        // Handle backward compatibility - if called with 2 parameters
        if ($province && $regency && !$subDistrict && !$village) {
            $subDistrict = $province;
            $village = $regency;
        }
        // Handle backward compatibility - if called with 1 parameter
        elseif ($province && !$regency && !$subDistrict && !$village) {
            $subDistrict = $province;
        }
        
        if (!$this->addressData || !$subDistrict) {
            return [];
        }
        
        $originalKey = $this->findOriginalKey($subDistrict);
        
        if (!$originalKey || !isset($this->addressData[$originalKey])) {
            return [];
        }
        
        $data = $this->addressData[$originalKey];
        $postalCodes = [];
        
        // Check if this is the new Subang format with postal codes as keys
        if ($this->isSubangNewFormat($data)) {
            // Handle Subang's new format where postal codes are keys
            foreach ($data as $postalCode => $postalData) {
                if (isset($postalData['kodepos']) && isset($postalData['desa'])) {
                    // If village is specified, check if it exists in this postal code group
                    if ($village) {
                        $originalVillageKey = $this->findOriginalVillageKey($village, $postalData['desa']);
                        if ($originalVillageKey) {
                            $postalCodes[$postalCode] = $postalCode;
                        }
                    } else {
                        // If no village specified, return all postal codes
                        $postalCodes[$postalCode] = $postalCode;
                    }
                }
            }
        } else {
            // Handle standard format
            if (isset($data['kodepos'])) {
                if (is_array($data['kodepos'])) {
                    // Multiple postal codes
                    foreach ($data['kodepos'] as $code) {
                        $postalCodes[$code] = $code;
                    }
                } else {
                    // Single postal code
                    $postalCodes[$data['kodepos']] = $data['kodepos'];
                }
            }
        }
        
        return $postalCodes;
    }
    
    /**
     * Check if data uses Subang's new format with postal codes as keys
     */
    private function isSubangNewFormat($data)
    {
        // Check if the data has numeric keys (postal codes) instead of 'kodepos' and 'desa' keys
        foreach ($data as $key => $value) {
            if (is_numeric($key) && is_array($value) && isset($value['kodepos']) && isset($value['desa'])) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Find original key from formatted key
     */
    private function findOriginalKey($formattedKey)
    {
        foreach ($this->addressData as $originalKey => $data) {
            // Try multiple comparison formats
            $normalizedOriginal = strtolower(str_replace(' ', '_', $originalKey));
            $normalizedInput = strtolower(str_replace(' ', '_', $formattedKey));
            
            if ($normalizedOriginal === $normalizedInput || 
                strtolower($originalKey) === strtolower($formattedKey) ||
                $originalKey === $formattedKey) {
                return $originalKey;
            }
        }
        return null;
    }
    
    /**
     * Find original village key from formatted key
     */
    private function findOriginalVillageKey($formattedKey, $villages)
    {
        if (is_array($villages)) {
            foreach ($villages as $village) {
                // Try multiple comparison formats
                $normalizedOriginal = strtolower(str_replace(' ', '_', $village));
                $normalizedInput = strtolower(str_replace(' ', '_', $formattedKey));
                
                if ($normalizedOriginal === $normalizedInput || 
                    strtolower($village) === strtolower($formattedKey) ||
                    $village === $formattedKey) {
                    return $village;
                }
            }
        }
        return null;
    }
    
    /**
     * Build complete address string for geocoding
     */
    public function buildCompleteAddress($village, $subDistrict, $regency, $province, $postalCode = null, $detailedAddress = null)
    {
        $addressParts = [];
        
        // Add detailed address if provided
        if ($detailedAddress) {
            $addressParts[] = $detailedAddress;
        }
        
        // Convert keys back to original format
        $originalVillage = $this->getOriginalName($village, 'village', $subDistrict);
        $originalSubDistrict = $this->getOriginalName($subDistrict, 'subdistrict');
        
        // Build address components
        if ($originalVillage) {
            $addressParts[] = $originalVillage;
        }
        
        if ($originalSubDistrict) {
            $addressParts[] = $originalSubDistrict;
        }
        
        $addressParts[] = 'Subang';
        $addressParts[] = 'Jawa Barat';
        
        if ($postalCode) {
            $addressParts[] = $postalCode;
        }
        
        return implode(', ', $addressParts);
    }
    
    /**
     * Get original name from formatted key
     */
    private function getOriginalName($formattedKey, $type, $parentKey = null)
    {
        if ($type === 'village' && $parentKey) {
            // Find the parent sub-district first
            $originalParentKey = $this->findOriginalKey($parentKey);
            if ($originalParentKey && isset($this->addressData[$originalParentKey]['desa'])) {
                foreach ($this->addressData[$originalParentKey]['desa'] as $desa) {
                    if (strtolower(str_replace(' ', '_', $desa)) === $formattedKey) {
                        return $desa;
                    }
                }
            }
        } elseif ($type === 'subdistrict') {
            // For subdistrict, find the original key and return it
            $originalKey = $this->findOriginalKey($formattedKey);
            return $originalKey;
        }
        
        return null;
    }
    
    /**
     * Get address names from keys
     * 
     * @param string $provinceKey
     * @param string $regencyKey
     * @param string $subDistrictKey
     * @param string $villageKey
     * @return array
     */
    public function getAddressNames($provinceKey, $regencyKey, $subDistrictKey, $villageKey)
    {
        $province = 'Jawa Barat'; // Fixed province
        $regency = 'Subang'; // Fixed regency
        
        // Find sub district name from addressData using findOriginalKey
        $subDistrict = '';
        $originalSubDistrictKey = $this->findOriginalKey($subDistrictKey);
        if ($originalSubDistrictKey) {
            $subDistrict = $originalSubDistrictKey;
        }
        
        // Find village name from addressData
        $village = '';
        if ($originalSubDistrictKey && isset($this->addressData[$originalSubDistrictKey])) {
            $data = $this->addressData[$originalSubDistrictKey];
            
            // Check if this is the new Subang format with postal codes as keys
            if ($this->isSubangNewFormat($data)) {
                // Handle Subang's new format where postal codes are keys
                foreach ($data as $postalCode => $postalData) {
                    if (isset($postalData['desa']) && is_array($postalData['desa'])) {
                        foreach ($postalData['desa'] as $desa) {
                            $formattedKey = strtolower(str_replace(' ', '_', $desa));
                            if ($formattedKey === $villageKey) {
                                $village = $desa;
                                break 2; // Break out of both loops
                            }
                        }
                    }
                }
            } else {
                // Handle standard format
                if (isset($data['desa'])) {
                    foreach ($data['desa'] as $desa) {
                        $formattedKey = strtolower(str_replace(' ', '_', $desa));
                        if ($formattedKey === $villageKey) {
                            $village = $desa;
                            break;
                        }
                    }
                }
            }
        }
        
        return [
            'province' => $province,
            'regency' => $regency,
            'sub_district' => $subDistrict,
            'village' => $village
        ];
    }
    
    /**
     * Get manual coordinates from JSON data for a specific village
     * NOTE: This method is deprecated as we now use direct distance data instead of coordinates
     * 
     * @param string $village
     * @param string $subDistrict
     * @param string $postalCode
     * @return array|null Returns ['latitude' => float, 'longitude' => float] or null if not found
     * @deprecated Use getDirectDistance() from DistanceCalculatorService instead
     */
    public function getManualCoordinates($village, $subDistrict, $postalCode = null)
    {
        // This method is deprecated as alamatsubang.json now uses direct distance data
        // instead of coordinates. Use DistanceCalculatorService::getDirectDistance() instead.
        return null;
    }
    
    /**
     * Get direct distance from JSON data for a specific village
     * 
     * @param string $village
     * @param string $subDistrict
     * @param string $postalCode
     * @return float|null Distance in kilometers, null if not found
     */
    public function getDirectDistance($village, $subDistrict, $postalCode = null)
    {
        if (!$this->addressData || !$village || !$subDistrict) {
            return null;
        }
        
        $originalSubDistrictKey = $this->findOriginalKey($subDistrict);
        
        if (!$originalSubDistrictKey || !isset($this->addressData[$originalSubDistrictKey])) {
            return null;
        }
        
        $data = $this->addressData[$originalSubDistrictKey];
        
        // Check if this is the new Subang format with postal codes as keys
        if ($this->isSubangNewFormat($data)) {
            // Handle Subang's new format where postal codes are keys
            if ($postalCode && isset($data[$postalCode]['distances'])) {
                $distances = $data[$postalCode]['distances'];
                $originalVillageKey = $this->findOriginalVillageKey($village, $data[$postalCode]['desa']);
                
                if ($originalVillageKey && isset($distances[$originalVillageKey])) {
                    return (float) $distances[$originalVillageKey];
                }
            }
            
            // If no postal code provided or not found, search through all postal codes
            foreach ($data as $postalCodeKey => $postalData) {
                if (is_numeric($postalCodeKey) && isset($postalData['distances']) && isset($postalData['desa'])) {
                    $originalVillageKey = $this->findOriginalVillageKey($village, $postalData['desa']);
                    
                    if ($originalVillageKey && isset($postalData['distances'][$originalVillageKey])) {
                        return (float) $postalData['distances'][$originalVillageKey];
                    }
                }
            }
        } else {
            // Handle standard format
            if (isset($data['distances'])) {
                $originalVillageKey = $this->findOriginalVillageKey($village, $data['desa']);
                
                if ($originalVillageKey && isset($data['distances'][$originalVillageKey])) {
                    return (float) $data['distances'][$originalVillageKey];
                }
            }
        }
        
        return null;
    }
    
    /**
     * Check if village exists in the address data (for validation)
     * 
     * @param string $village
     * @param string $subDistrict
     * @param string $postalCode
     * @return bool
     */
    public function isValidAddress($village, $subDistrict, $postalCode = null)
    {
        if (!$this->addressData || !$village || !$subDistrict) {
            return false;
        }
        
        $originalSubDistrictKey = $this->findOriginalKey($subDistrict);
        
        if (!$originalSubDistrictKey || !isset($this->addressData[$originalSubDistrictKey])) {
            return false;
        }
        
        $data = $this->addressData[$originalSubDistrictKey];
        
        // Check if this is the new Subang format with postal codes as keys
        if ($this->isSubangNewFormat($data)) {
            // Handle Subang's new format where postal codes are keys
            if ($postalCode && isset($data[$postalCode]['desa'])) {
                $originalVillageKey = $this->findOriginalVillageKey($village, $data[$postalCode]['desa']);
                return $originalVillageKey !== null;
            }
        } else {
            // Handle standard format
            if (isset($data['desa'])) {
                $originalVillageKey = $this->findOriginalVillageKey($village, $data['desa']);
                return $originalVillageKey !== null;
            }
        }
        
        return false;
    }
    
    /**
     * Build address variations for better geocoding accuracy
     * 
     * @param string $village
     * @param string $subDistrict
     * @param string $regency
     * @param string $province
     * @param string $postalCode
     * @param string $detailedAddress
     * @return array
     */
    public function buildAddressVariations($village, $subDistrict, $regency, $province, $postalCode, $detailedAddress = '')
    {
        $variations = [];
        
        // Full address with detailed address
        if (!empty($detailedAddress)) {
            $variations[] = trim("{$detailedAddress}, {$village}, {$subDistrict}, {$regency}, {$province}, {$postalCode}");
        }
        
        // Address variations for Nominatim API
        $variations[] = "{$village}, {$subDistrict}, {$regency}, {$province}, {$postalCode}";
        $variations[] = "{$village}, {$subDistrict}, {$regency}, {$province}";
        $variations[] = "{$subDistrict}, {$regency}, {$province}, {$postalCode}";
        $variations[] = "{$regency}, {$province}, {$postalCode}";
        $variations[] = "{$province}, {$postalCode}";
        
        return $variations;
    }
    
    /**
     * Get multiple address variations for better geocoding accuracy
     */
    public function getAddressVariations($village, $subDistrict, $regency, $province, $postalCode = null, $detailedAddress = null)
    {
        $originalVillage = $this->getOriginalName($village, 'village', $subDistrict);
        $originalSubDistrict = $this->getOriginalName($subDistrict, 'subdistrict');
        
        $variations = [];
        
        // Variation 1: Full address with detailed address
        if ($detailedAddress) {
            $variations[] = $this->buildCompleteAddress($village, $subDistrict, $regency, $province, $postalCode, $detailedAddress);
        }
        
        // Variation 2: Desa + Kecamatan format (most specific for Indonesian addresses)
        if ($originalVillage && $originalSubDistrict) {
            $variations[] = "Desa {$originalVillage}, Kecamatan {$originalSubDistrict}, Kabupaten Subang, Jawa Barat, Indonesia";
            if ($postalCode) {
                $variations[] = "Desa {$originalVillage}, Kecamatan {$originalSubDistrict}, Kabupaten Subang, Jawa Barat {$postalCode}, Indonesia";
            }
        }
        
        // Variation 3: Standard format without prefixes
        if ($originalVillage && $originalSubDistrict && $postalCode) {
            $variations[] = implode(', ', [$originalVillage, $originalSubDistrict, 'Kabupaten Subang', 'Jawa Barat', $postalCode, 'Indonesia']);
        }
        
        // Variation 4: Village and subdistrict only
        if ($originalVillage && $originalSubDistrict) {
            $variations[] = implode(', ', [$originalVillage, $originalSubDistrict, 'Subang', 'Jawa Barat', 'Indonesia']);
        }
        
        // Variation 5: Kecamatan format for better recognition
        if ($originalSubDistrict) {
            $variations[] = "Kecamatan {$originalSubDistrict}, Kabupaten Subang, Jawa Barat, Indonesia";
            if ($postalCode) {
                $variations[] = "Kecamatan {$originalSubDistrict}, Kabupaten Subang, Jawa Barat {$postalCode}, Indonesia";
            }
        }
        
        // Variation 6: Kabupaten format
        $variations[] = 'Kabupaten Subang, Jawa Barat, Indonesia';
        if ($postalCode) {
            $variations[] = "Kabupaten Subang, Jawa Barat {$postalCode}, Indonesia";
        }
        
        return array_unique($variations);
    }

    /**
     * Build full address string from keys
     * 
     * @param string $provinceKey
     * @param string $regencyKey
     * @param string $subDistrictKey
     * @param string $villageKey
     * @param string|null $postalCode
     * @param string|null $detailedAddress
     * @return string
     */
    public function buildFullAddress($provinceKey, $regencyKey, $subDistrictKey, $villageKey, $postalCode = null, $detailedAddress = null)
    {
        $addressNames = $this->getAddressNames($provinceKey, $regencyKey, $subDistrictKey, $villageKey);
        
        $parts = array_filter([
            $detailedAddress,
            $addressNames['village'],
            $addressNames['sub_district'],
            $addressNames['regency'],
            $addressNames['province'],
            $postalCode
        ]);
        
        return implode(', ', $parts);
    }
}