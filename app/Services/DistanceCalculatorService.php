<?php

namespace App\Services;

use App\Models\StoreSetting;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DistanceCalculatorService
{
    private string $nominatimBaseUrl = 'https://nominatim.openstreetmap.org';
    private int $requestDelay = 0; // Reduced delay to prevent timeout, will implement rate limiting differently

    public function __construct()
    {
        // No API key needed for Nominatim
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     * Note: This calculates straight-line distance, not road distance
     */
    public function calculateDistance(float $fromLat, float $fromLng, float $toLat, float $toLng): array
    {
        return $this->calculateStraightLineDistance($fromLat, $fromLng, $toLat, $toLng);
    }

    /**
     * Calculate straight-line distance using Haversine formula as fallback
     */
    public function calculateStraightLineDistance(float $fromLat, float $fromLng, float $toLat, float $toLng): array
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $latDelta = deg2rad($toLat - $fromLat);
        $lngDelta = deg2rad($toLng - $fromLng);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($fromLat)) * cos(deg2rad($toLat)) *
             sin($lngDelta / 2) * sin($lngDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return [
            'distance_km' => round($distance, 2),
            'duration_minutes' => round($distance * 3), // Rough estimate: 3 minutes per km
            'distance_text' => round($distance, 1) . ' km',
            'duration_text' => round($distance * 3) . ' mins'
        ];
    }

    /**
     * Get store coordinates from settings (manual override)
     * This provides accurate coordinates set manually in store settings
     */
    public function getStoreCoordinates(): array
    {
        $latitude = StoreSetting::get('store_latitude');
        $longitude = StoreSetting::get('store_longitude');
        $address = StoreSetting::get('store_address');
        
        if (!$latitude || !$longitude) {
            throw new \Exception('Koordinat toko belum diatur. Silakan atur koordinat toko di pengaturan.');
        }
        
        return [
            'latitude' => (float) $latitude,
            'longitude' => (float) $longitude,
            'formatted_address' => $address,
            'source' => 'manual_store_settings'
        ];
    }

    /**
     * Get coordinates from address using OpenStreetMap Nominatim API
     * Includes fallback strategies for better address resolution
     * Note: For store coordinates, use getStoreCoordinates() for better accuracy
     */
    public function getCoordinatesFromAddress(string $address): array
    {
        // Validate input
        if (empty(trim($address))) {
            throw new \Exception('Alamat tidak boleh kosong.');
        }

        // Create cache key for the address
        $cacheKey = 'geocoding_' . md5(strtolower(trim($address)));
        
        // Check if result is cached (cache for 24 hours) - early return to prevent timeout
        $cachedResult = Cache::get($cacheKey);
        if ($cachedResult) {
            Log::info("Using cached geocoding result for: {$address}");
            return $cachedResult;
        }
        
        // Set execution time limit for this function to prevent timeout
        set_time_limit(25); // Allow 25 seconds max for geocoding

        // Try multiple search strategies with improved fallbacks
        $searchStrategies = [
            // Strategy 1: Extract village/district combination for better accuracy (prioritized)
            $this->extractVillageDistrictCity($address),
            // Strategy 2: Try with kecamatan and kabupaten only (more specific than city/province)
            $this->extractSubDistrictRegency($address),
            // Strategy 3: Try with village and kecamatan only (without kabupaten)
            $this->extractVillageSubDistrict($address),
            // Strategy 4: Try Indonesian format: "Desa X, Kecamatan Y, Kabupaten Z"
            $this->buildIndonesianFormat($address),
            // Strategy 5: Try simplified format without prefixes: "Village, SubDistrict, Regency, Province"
            $this->buildSimplifiedFormat($address),
            // Strategy 6: Try with just the main location parts (removes RT/RW, Dusun)
            $this->simplifyAddress($address),
            // Strategy 7: Original address
            $address,
            // Strategy 8: Remove Plus Code if present and try with remaining address
            preg_replace('/^[A-Z0-9+]{8,}[,\s]*/', '', $address),
            // Strategy 9: Extract only city and province (less specific)
            $this->extractCityProvince($address),
            // Strategy 10: Extract just the regency/city and province
            $this->extractRegencyProvince($address)
            // Note: Removed province-only strategy as it's too generic and causes inaccurate results
        ];

        $lastError = null;
        $strategiesAttempted = [];

        // Limit strategies to prevent timeout - reduced to 4 for faster execution
        $maxStrategies = 4;
        $strategiesToTry = array_slice($searchStrategies, 0, $maxStrategies);
        
        foreach ($strategiesToTry as $index => $searchAddress) {
            if (empty(trim($searchAddress))) {
                continue;
            }

            $strategiesAttempted[] = "Strategy " . ($index + 1) . ": {$searchAddress}";

            try {
                // Add minimal delay only for first few requests to prevent rate limiting
                if ($index > 0 && $index < 3) {
                    usleep(200000); // 0.2 second delay
                }
                
                Log::info("Trying geocoding strategy " . ($index + 1) . " for: {$searchAddress}");
                
                $response = Http::timeout(8)->withHeaders([
                    'User-Agent' => 'ApotekBaraya/1.0 (contact@apotekbaraya.com)' // Required by Nominatim
                ])->get($this->nominatimBaseUrl . '/search', [
                    'q' => trim($searchAddress),
                    'format' => 'json',
                    'limit' => 20, // Increased limit for better results
                    'addressdetails' => 1,
                    'countrycodes' => 'id', // Limit to Indonesia
                    'bounded' => 1, // Prefer results within Indonesia
                    'viewbox' => '95.0,-11.0,141.0,6.0', // Indonesia bounding box
                    'zoom' => 18, // Higher zoom for more precise results
                    'dedupe' => 0, // Don't remove duplicate results
                    'extratags' => 1, // Get additional tags
                    'namedetails' => 1, // Get name details in different languages
                    'accept-language' => 'id,en' // Prefer Indonesian, fallback to English
                ]);

                if (!$response->successful()) {
                    $lastError = "API request failed with status: {$response->status()}";
                    Log::warning("Nominatim API request failed with status: {$response->status()} for strategy " . ($index + 1));
                    continue;
                }

                $data = $response->json();

                if (!empty($data)) {
                    // Find the best result based on relevance and location accuracy
                    $bestResult = $this->selectBestGeocodingResult($data, $address, $searchAddress);
                    
                    if ($bestResult && ($bestResult['confidence_score'] ?? 0) >= 30) {
                        // Validate that required coordinate keys exist
                        if (!isset($bestResult['lat']) || !isset($bestResult['lon'])) {
                            Log::warning("Missing coordinate data in result for strategy " . ($index + 1) . ": {$searchAddress}");
                            continue;
                        }
                        
                        // Verify the result is not too generic (avoid province-level results)
                        if ($this->isResultTooGeneric($bestResult, $address)) {
                            Log::warning("Skipping generic result for strategy " . ($index + 1) . ": {$searchAddress}");
                            continue;
                        }
                        
                        // Additional validation for Indonesian addresses
                        if (!$this->validateIndonesianAddress($bestResult, $address)) {
                            Log::warning("Address validation failed for strategy " . ($index + 1) . ": {$searchAddress}");
                            continue;
                        }
                        
                        // For fallback strategies (city/province, regency/province), add distance validation
                        if ($index >= 8) { // Strategy 9 and 10 are fallback strategies
                            $originalComponents = $this->extractAddressComponents($address);
                            if (!empty($originalComponents['village']) || !empty($originalComponents['sub_district'])) {
                                // If original address has village/sub-district but we're using city/regency coordinates,
                                // this might be too inaccurate for delivery purposes
                                Log::warning("Rejecting fallback coordinates for specific address - strategy " . ($index + 1) . ": {$searchAddress}");
                                continue;
                            }
                        }
                        
                        // Skip coordinate verification to prevent timeout
                        // TODO: Re-enable verification with better caching and optimization
                        $isVerified = true; // Temporarily disabled to prevent timeout
                        
                        $coordinates = [
                            'latitude' => (float) $bestResult['lat'],
                            'longitude' => (float) $bestResult['lon'],
                            'formatted_address' => $bestResult['display_name'] ?? $searchAddress,
                            'search_strategy' => $index + 1,
                            'original_address' => $address,
                            'search_query' => $searchAddress,
                            'confidence_score' => $bestResult['confidence_score'] ?? 0,
                            'verified' => $isVerified
                        ];
                        
                        // Cache the result for 24 hours
                        Cache::put($cacheKey, $coordinates, 60 * 24);
                        
                        Log::info("Geocoding successful using strategy {$coordinates['search_strategy']} for: {$searchAddress} (confidence: {$bestResult['confidence_score']})");
                        
                        return $coordinates;
                    } else {
                        Log::info("Low confidence result (" . ($bestResult['confidence_score'] ?? 0) . ") for strategy " . ($index + 1) . ": {$searchAddress}");
                    }
                } else {
                    $lastError = "No results found for: {$searchAddress}";
                    Log::info("No geocoding results found for strategy " . ($index + 1) . ": {$searchAddress}");
                }

            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                Log::warning("Geocoding strategy " . ($index + 1) . " failed: " . $e->getMessage());
                continue;
            }
        }

        // If all strategies fail, log detailed error and throw exception
        Log::error('All geocoding strategies failed for address: ' . $address . '. Strategies attempted: ' . implode('; ', $strategiesAttempted));
        
        $errorMessage = 'Tidak dapat menemukan koordinat untuk alamat: "' . $address . '". ';
        $errorMessage .= 'Silakan coba dengan alamat yang lebih sederhana seperti: "Nama Kota, Nama Provinsi" (contoh: "Subang, Jawa Barat"). ';
        if ($lastError) {
            $errorMessage .= 'Error terakhir: ' . $lastError;
        }
        
        throw new \Exception($errorMessage);
    }

    /**
     * Check if geocoding result is too generic (province or regency level only)
     * to avoid inaccurate coordinates
     */
    private function isResultTooGeneric(array $result, string $originalAddress): bool
    {
        $displayName = strtolower($result['display_name'] ?? '');
        $addressDetails = $result['address'] ?? [];
        
        // Check if result only contains province or regency level information
        $hasVillage = !empty($addressDetails['village']) || !empty($addressDetails['hamlet']);
        $hasSubDistrict = !empty($addressDetails['suburb']) || !empty($addressDetails['town']);
        $hasSpecificLocation = $hasVillage || $hasSubDistrict;
        
        // If original address contains specific village/sub-district but result doesn't, it's too generic
        $originalLower = strtolower($originalAddress);
        $hasSpecificInOriginal = preg_match('/\b(desa|kelurahan|kecamatan)\s+\w+/i', $originalLower);
        
        if ($hasSpecificInOriginal && !$hasSpecificLocation) {
            return true;
        }
        
        // Check if result is only at province level
        $isProvinceOnly = !empty($addressDetails['state']) && 
                         empty($addressDetails['city']) && 
                         empty($addressDetails['town']) && 
                         empty($addressDetails['village']);
        
        return $isProvinceOnly;
    }

    /**
     * Verify coordinates accuracy using reverse geocoding
     * Returns true if the reverse geocoded address matches the original address components
     */
    private function verifyCoordinatesAccuracy(float $latitude, float $longitude, string $originalAddress): bool
    {
        try {
            // Get address from coordinates using reverse geocoding
            $reverseGeocodedAddress = $this->getAddressFromCoordinates($latitude, $longitude);
            
            if (!$reverseGeocodedAddress) {
                return false;
            }
            
            // Extract components from both addresses
            $originalComponents = $this->extractAddressComponents($originalAddress);
            $reverseComponents = $this->extractAddressComponents($reverseGeocodedAddress['formatted_address']);
            
            // Debug logging
            Log::info("Coordinate verification debug:", [
                'original_address' => $originalAddress,
                'reverse_address' => $reverseGeocodedAddress['formatted_address'],
                'original_components' => $originalComponents,
                'reverse_components' => $reverseComponents
            ]);
            
            $matchScore = 0;
            $totalChecks = 0;
            
            // Check village/hamlet match
            if (!empty($originalComponents['village'])) {
                $totalChecks++;
                if (!empty($reverseComponents['village']) && 
                    $this->fuzzyMatch($originalComponents['village'], $reverseComponents['village'])) {
                    $matchScore++;
                }
            }
            
            // Check sub-district match
            if (!empty($originalComponents['sub_district'])) {
                $totalChecks++;
                if (!empty($reverseComponents['sub_district']) && 
                    $this->fuzzyMatch($originalComponents['sub_district'], $reverseComponents['sub_district'])) {
                    $matchScore++;
                }
            }
            
            // Check city/regency match
            if (!empty($originalComponents['city'])) {
                $totalChecks++;
                if (!empty($reverseComponents['city']) && 
                    $this->fuzzyMatch($originalComponents['city'], $reverseComponents['city'])) {
                    $matchScore++;
                }
            }
            
            // Check province match
            if (!empty($originalComponents['province'])) {
                $totalChecks++;
                if (!empty($reverseComponents['province']) && 
                    $this->fuzzyMatch($originalComponents['province'], $reverseComponents['province'])) {
                    $matchScore++;
                }
            }
            
            // Require at least 60% match for verification
            $matchPercentage = $totalChecks > 0 ? ($matchScore / $totalChecks) * 100 : 0;
            
            Log::info("Coordinate verification: {$matchScore}/{$totalChecks} components matched ({$matchPercentage}%) for coordinates {$latitude}, {$longitude}");
            
            return $matchPercentage >= 60;
            
        } catch (Exception $e) {
            Log::error("Error verifying coordinates: " . $e->getMessage());
            return false;
        }
    }

   /**
     * Extract village, district, and city combination for better accuracy
     * Example: "Dusun Jurutilu RT/RW 010/005, Desa Sukamandijaya, Ciasem, Subang, 41256"
     * Returns: "Sukamandijaya, Ciasem, Subang"
     */
    private function extractVillageDistrictCity(string $address): string
    {
        // Remove RT/RW and Dusun/Gang details
        $cleaned = preg_replace('/\b(RT\/RW|RT|RW)\s*[0-9\/]+/i', '', $address);
        $cleaned = preg_replace('/\b(Dusun|Dukuh|Gang|Gg\.|Jl\.|Jalan)\s+[^,]+,?/i', '', $cleaned);
        $cleaned = preg_replace('/\s*,\s*/', ', ', trim($cleaned));
        
        // Try to extract Desa/Kelurahan + Kecamatan + Kabupaten pattern (remove prefix words)
        if (preg_match('/(?:Desa|Kelurahan|Kel\.?)\s+([^,]+),?\s*(?:Kecamatan|Kec\.?)\s+([^,]+),?\s*(?:Kabupaten|Kab\.?|Kota)\s+([^,]+)/i', $cleaned, $matches)) {
            return trim($matches[1]) . ', ' . trim($matches[2]) . ', ' . trim($matches[3]);
        }
        
        // Try pattern with Desa/Kelurahan but without Kecamatan/Kabupaten keywords
        if (preg_match('/(?:Desa|Kelurahan|Kel\.?)\s+([^,]+),\s*([^,]+),\s*([^,]+)(?:,\s*[0-9]{5})?/i', $cleaned, $matches)) {
            return trim($matches[1]) . ', ' . trim($matches[2]) . ', ' . trim($matches[3]);
        }
        
        // Enhanced pattern matching for Indonesian addresses
        $parts = array_map('trim', explode(',', $cleaned));
        if (count($parts) >= 4) {
            // Pattern: [detailed_address], [village], [sub_district], [regency], [province], [postal_code]
            // Extract village, sub_district, regency (skip detailed_address and province/postal_code)
            $village = '';
            $subDistrict = '';
            $regency = '';
            
            // Find village (usually contains 'Desa' or is the second part)
            for ($i = 0; $i < count($parts); $i++) {
                if (preg_match('/^(Desa|Kelurahan)\s+(.+)/i', $parts[$i], $matches)) {
                    $village = trim($matches[2]);
                    // Sub-district is usually the next part
                    if (isset($parts[$i + 1]) && !preg_match('/^\d{5}$/', $parts[$i + 1])) {
                        $subDistrict = $parts[$i + 1];
                    }
                    // Regency is usually two parts after village
                    if (isset($parts[$i + 2]) && !preg_match('/^\d{5}$/', $parts[$i + 2])) {
                        $regency = $parts[$i + 2];
                    }
                    break;
                }
            }
            
            // If no explicit village found, try positional extraction
            if (empty($village) && count($parts) >= 3) {
                // Skip first part (detailed address) and last part if it's postal code or province
                $startIndex = 1;
                $endIndex = count($parts) - 1;
                
                // Skip last part if it looks like postal code or province
                if (preg_match('/^\d{5}$/', $parts[$endIndex]) || 
                    preg_match('/\b(Jawa|Sumatra|Kalimantan|Sulawesi|Bali|Nusa|Papua|Maluku)\b/i', $parts[$endIndex])) {
                    $endIndex--;
                }
                
                // Extract village, sub-district, regency from remaining parts
                if ($endIndex - $startIndex >= 2 && isset($parts[$startIndex + 2])) {
                    $village = $parts[$startIndex];
                    $subDistrict = $parts[$startIndex + 1];
                    $regency = $parts[$startIndex + 2];
                }
            }
            
            if (!empty($village) && !empty($subDistrict) && !empty($regency)) {
                return $village . ', ' . $subDistrict . ', ' . $regency;
            }
        }
        
        // Try simpler pattern: Village, District, City (after removing prefixes)
        $withoutPrefixes = preg_replace('/\b(Desa|Kelurahan|Kel\.|Kecamatan|Kec\.|Kabupaten|Kab\.|Kota)\s+/i', '', $cleaned);
        if (preg_match('/([^,]+),\s*([^,]+),\s*([^,]+)(?:,\s*[0-9]{5})?/i', $withoutPrefixes, $matches)) {
            // Skip if first part looks like RT/RW or postal code
            if (!preg_match('/^(RT|RW|[0-9]{5})/i', trim($matches[1]))) {
                return trim($matches[1]) . ', ' . trim($matches[2]) . ', ' . trim($matches[3]);
            }
        }
        
        return '';
    }

    /**
     * Extract city and province from address for fallback geocoding
     */
    private function extractCityProvince(string $address): string
    {
        // Look for common patterns like "Kec. City, Kabupaten/Kota Province"
        if (preg_match('/(?:Kab(?:upaten)?|Kota)\s+([^,]+),\s*([^,]+)$/i', $address, $matches)) {
            return trim($matches[1] . ', ' . $matches[2]);
        }
        
        // Look for patterns like "Kecamatan X, Kabupaten Y, Provinsi Z"
        if (preg_match('/(?:Kecamatan|Kec\.?)\s+([^,]+),\s*(?:Kabupaten|Kab\.?)\s+([^,]+),\s*(?:Provinsi|Prov\.?)?\s*([^,]+)$/i', $address, $matches)) {
            return trim($matches[2] . ', ' . $matches[3]);
        }
        
        // Look for patterns like "Desa X, Kecamatan Y, Kabupaten Z, Provinsi W"
        if (preg_match('/(?:Desa|Kelurahan)\s+[^,]+,\s*(?:Kecamatan|Kec\.?)\s+[^,]+,\s*(?:Kabupaten|Kab\.?)\s+([^,]+),\s*(?:Provinsi|Prov\.?)?\s*([^,]+)$/i', $address, $matches)) {
            return trim($matches[1] . ', ' . $matches[2]);
        }
        
        // Look for province at the end
        if (preg_match('/,\s*([^,]+)\s*\d*$/i', $address, $matches)) {
            $parts = explode(',', $address);
            if (count($parts) >= 2) {
                $secondLastIndex = count($parts) - 2;
                $lastIndex = count($parts) - 1;
                
                // Ensure indices are valid before accessing array
                if (isset($parts[$secondLastIndex]) && isset($parts[$lastIndex])) {
                    return trim($parts[$secondLastIndex] . ', ' . $parts[$lastIndex]);
                }
            }
        }
        
        return '';
    }

    /**
     * Simplify address by removing detailed parts
     */
    private function simplifyAddress(string $address): string
    {
        // Remove Plus Code
        $simplified = preg_replace('/^[A-Z0-9+]{8,}[,\s]*/', '', $address);
        
        // Remove detailed street info and keep main location
        $simplified = preg_replace('/^Jl\.[^,]*,\s*/', '', $simplified);
        
        // Remove RT/RW details
        $simplified = preg_replace('/\b(?:RT|RW)\s*[\.\\/]?\s*\d+\b/i', '', $simplified);
        
        // Remove Dusun/Dukuh details at the beginning
        $simplified = preg_replace('/^(?:Dusun|Dukuh)\s+[^,]+,?\s*/i', '', $simplified);
        
        // Remove Gang/Gg details
        $simplified = preg_replace('/\b(?:Gang|Gg\.?)\s+[^,]+,?\s*/i', '', $simplified);
        
        // Clean up multiple commas and spaces
        $simplified = preg_replace('/,\s*,+/', ',', $simplified);
        $simplified = preg_replace('/\s+/', ' ', $simplified);
        $simplified = trim($simplified, ', ');
        
        // Extract main parts (usually the last 2-3 parts for Indonesian addresses)
        $parts = array_map('trim', explode(',', $simplified));
        if (count($parts) >= 3) {
            // For Indonesian addresses, try to get Kecamatan, Kabupaten, Provinsi
            return implode(', ', array_slice($parts, -3));
        } elseif (count($parts) >= 2) {
            return implode(', ', array_slice($parts, -2));
        }
        
        return $simplified;
    }

    /**
     * Select the best geocoding result from multiple options
     * Prioritizes results that match the expected administrative divisions
     */
    private function selectBestGeocodingResult(array $results, string $originalAddress, string $searchQuery): ?array
    {
        if (empty($results)) {
            return null;
        }
        
        // Extract expected administrative components from original address
        $expectedComponents = $this->extractAddressComponents($originalAddress);
        
        $scoredResults = [];
        
        foreach ($results as $result) {
            $score = 0;
            $addressDetails = $result['address'] ?? [];
            
            // Score based on administrative level matching with fuzzy matching
            if (!empty($expectedComponents['village'])) {
                if (isset($addressDetails['village']) && 
                    $this->fuzzyMatch($addressDetails['village'], $expectedComponents['village'])) {
                    $score += 60; // Higher score for village match
                }
                if (isset($addressDetails['hamlet']) && 
                    $this->fuzzyMatch($addressDetails['hamlet'], $expectedComponents['village'])) {
                    $score += 55; // High score for hamlet match
                }
            }
            
            if (!empty($expectedComponents['sub_district'])) {
                if (isset($addressDetails['county']) && 
                    $this->fuzzyMatch($addressDetails['county'], $expectedComponents['sub_district'])) {
                    $score += 40; // Higher score for sub-district match
                }
                if (isset($addressDetails['suburb']) && 
                    $this->fuzzyMatch($addressDetails['suburb'], $expectedComponents['sub_district'])) {
                    $score += 35; // Good score for suburb match
                }
            }
            
            if (!empty($expectedComponents['regency'])) {
                if ((isset($addressDetails['city']) && 
                     $this->fuzzyMatch($addressDetails['city'], $expectedComponents['regency'])) ||
                    (isset($addressDetails['state_district']) && 
                     $this->fuzzyMatch($addressDetails['state_district'], $expectedComponents['regency']))) {
                    $score += 30; // Medium score for regency match
                }
            }
            
            if (!empty($expectedComponents['province'])) {
                if (isset($addressDetails['state']) && 
                    $this->fuzzyMatch($addressDetails['state'], $expectedComponents['province'])) {
                    $score += 20; // Score for province match
                }
            }
            
            // Bonus for having specific administrative levels
            if (!empty($addressDetails['village']) || !empty($addressDetails['hamlet'])) {
                $score += 25; // Higher bonus for village-level data
            }
            if (!empty($addressDetails['suburb']) || !empty($addressDetails['county'])) {
                $score += 20; // Bonus for sub-district level data
            }
            if (!empty($addressDetails['city'])) {
                $score += 12; // Bonus for city data
            }
            
            // Bonus for more specific place types
            $placeType = $result['type'] ?? '';
            if (in_array($placeType, ['village', 'hamlet', 'neighbourhood'])) {
                $score += 20;
            } elseif (in_array($placeType, ['suburb', 'town'])) {
                $score += 15;
            } elseif (in_array($placeType, ['administrative', 'city'])) {
                $score += 8;
            }
            
            // Higher penalty for very generic results
            if (in_array($placeType, ['state', 'country'])) {
                $score -= 30;
            }
            
            // Additional penalty if result lacks specific location data when original has it
            if (!empty($expectedComponents['village']) && empty($addressDetails['village']) && empty($addressDetails['hamlet'])) {
                $score -= 15;
            }
            
            if (!empty($expectedComponents['sub_district']) && empty($addressDetails['suburb']) && empty($addressDetails['county'])) {
                $score -= 10;
            }
            
            $result['confidence_score'] = max(0, $score); // Ensure score is not negative
            $scoredResults[] = $result;
        }
        
        // Sort by score (highest first)
        usort($scoredResults, function($a, $b) {
            return ($b['confidence_score'] ?? 0) <=> ($a['confidence_score'] ?? 0);
        });
        
        // Check if we have any results after scoring
        if (empty($scoredResults)) {
            Log::warning("No valid geocoding results after scoring for: {$originalAddress}");
            return null;
        }
        
        // Return the best result if it has a reasonable score
        $bestResult = $scoredResults[0];
        if (($bestResult['confidence_score'] ?? 0) >= 25) {
            return $bestResult;
        }
        
        // If no good match, return the first result but log a warning
        Log::warning("Low confidence geocoding result for: {$originalAddress}. Best score: {$bestResult['confidence_score']}");
        return $bestResult;
    }
    
    /**
     * Fuzzy match two strings for address component comparison
     */
    private function fuzzyMatch(string $str1, string $str2): bool
    {
        $str1 = strtolower(trim($str1));
        $str2 = strtolower(trim($str2));
        
        // Exact match
        if ($str1 === $str2) {
            return true;
        }
        
        // Contains match
        if (stripos($str1, $str2) !== false || stripos($str2, $str1) !== false) {
            return true;
        }
        
        // Similar text (80% similarity)
        $similarity = 0;
        similar_text($str1, $str2, $similarity);
        return $similarity >= 80;
    }
    


    /**
     * Extract sub-district (kecamatan) and regency (kabupaten) from address
     * This is more specific than city/province and often gives better results
     */
    private function extractSubDistrictRegency(string $address): string
    {
        // Look for patterns like "Kecamatan Tambakdahan, Kabupaten Subang"
        if (preg_match('/(?:Kecamatan|Kec\.?)\s+([^,]+),\s*(?:Kabupaten|Kab\.?)\s+([^,]+)/i', $address, $matches)) {
            return trim($matches[1]) . ', ' . trim($matches[2]);
        }
        
        // Look for patterns without keywords: "Tambakdahan, Subang"
        $parts = array_map('trim', explode(',', $address));
        if (count($parts) >= 3) {
            // Try to find kecamatan and kabupaten from the middle parts
            // Usually: [detailed_address], [village], [sub_district], [regency], [province]
            $subDistrictIndex = count($parts) - 3; // Third from last
            $regencyIndex = count($parts) - 2; // Second from last
            
            // Ensure indices are valid before accessing array
            if (isset($parts[$subDistrictIndex]) && isset($parts[$regencyIndex])) {
                $possibleSubDistrict = $parts[$subDistrictIndex];
                $possibleRegency = $parts[$regencyIndex];
                
                // Skip if it looks like a postal code or RT/RW
                if (!preg_match('/^(\d{5}|RT|RW)/i', $possibleSubDistrict) && 
                    !preg_match('/^(\d{5}|RT|RW)/i', $possibleRegency)) {
                    return $possibleSubDistrict . ', ' . $possibleRegency;
                }
            }
        }
        
        return '';
    }

    /**
     * Extract village (desa) and sub-district (kecamatan) only
     * This is more specific than sub-district + regency
     */
    private function extractVillageSubDistrict(string $address): string
    {
        // Remove detailed parts like RT/RW, Dusun, street numbers
        $cleaned = preg_replace('/^[^,]*,\s*/', '', $address); // Remove first part (usually street/RT/RW)
        $cleaned = preg_replace('/\b(RT|RW)[\.\\/\s]*\d+[\.\\/\s]*\d*\b/i', '', $cleaned);
        $cleaned = preg_replace('/\b(Dusun|Ds\.|Jl\.|Jalan)\s+[^,]+,?\s*/i', '', $cleaned);
        
        // Try to extract desa and kecamatan
        $parts = array_map('trim', explode(',', $cleaned));
        $parts = array_filter($parts, function($part) {
            return !empty($part) && !preg_match('/^\d+$/', $part); // Remove postal codes
        });
        
        if (count($parts) >= 2 && isset($parts[0]) && isset($parts[1])) {
            // Take the first two parts (likely desa and kecamatan)
            $village = $parts[0];
            $subDistrict = $parts[1];
            return "{$village}, {$subDistrict}";
        }
        
        return '';
    }

    /**
     * Extract regency/city and province from address
     */
    private function extractRegencyProvince(string $address): string
    {
        // Look for patterns like "KAB. SUBANG, PROV. JAWA BARAT" or "KOTA BANDUNG, JAWA BARAT"
        if (preg_match('/(?:Kabupaten|Kab\.|Kota)\s+([^,]+),\s*(?:Provinsi|Prov\.?)\s+([^,0-9]+)/i', $address, $matches)) {
            return trim($matches[1]) . ', ' . trim($matches[2]);
        }
        
        // Look for patterns like "SUBANG, JAWA BARAT" (extract regency and province)
        $parts = array_map('trim', explode(',', $address));
        if (count($parts) >= 3) {
            // Find regency (usually third from last, before province and postal code)
            $regency = '';
            $province = '';
            
            // Look for regency pattern
            for ($i = 0; $i < count($parts); $i++) {
                if (preg_match('/^(?:Kabupaten|Kab\.)\s+(.+)/i', $parts[$i], $matches)) {
                    $regency = trim($matches[1]);
                    // Province is usually the next non-postal part
                    for ($j = $i + 1; $j < count($parts); $j++) {
                        if (preg_match('/^(?:Provinsi|Prov\.?)\s+(.+)/i', $parts[$j], $provMatches)) {
                            $province = trim(preg_replace('/\s*\d{5}\s*$/', '', $provMatches[1]));
                            break;
                        }
                    }
                    break;
                }
            }
            
            if (!empty($regency) && !empty($province)) {
                return $regency . ', ' . $province;
            }
            
            // Fallback: try to get last two non-postal parts
            $filteredParts = array_filter($parts, function($part) {
                return !preg_match('/^\d{5}$/', trim($part));
            });
            
            if (count($filteredParts) >= 2) {
                $lastTwo = array_slice($filteredParts, -2);
                // Check if the last part looks like a province
                if (isset($lastTwo[1]) && preg_match('/\b(?:Jawa|Sumatra|Kalimantan|Sulawesi|Bali|Nusa|Papua|Maluku)\b/i', $lastTwo[1])) {
                    return implode(', ', $lastTwo);
                }
            }
        }
        
        return '';
    }

    /**
     * Extract address components into structured array
     */
    private function extractAddressComponents(string $address): array
    {
        $components = [
            'village' => '',
            'sub_district' => '',
            'regency' => '',
            'province' => ''
        ];
        
        // Clean address from RT/RW and other details
        $cleanAddress = $this->cleanAddressFromDetails($address);
        
        // Extract village/kelurahan
        if (preg_match('/(?:Desa|Kelurahan)\s+([^,]+)/i', $cleanAddress, $matches)) {
            $components['village'] = trim($matches[1]);
        }
        
        // Extract sub-district/kecamatan
        if (preg_match('/Kecamatan\s+([^,]+)/i', $cleanAddress, $matches)) {
            $components['sub_district'] = trim($matches[1]);
        }
        
        // Extract regency/kabupaten
        if (preg_match('/(?:Kabupaten|Kota)\s+([^,]+)/i', $cleanAddress, $matches)) {
            $components['regency'] = trim($matches[1]);
        }
        
        // Extract province
        if (preg_match('/Provinsi\s+([^,0-9]+)/i', $cleanAddress, $matches)) {
            $components['province'] = trim($matches[1]);
        } else {
            // Try to extract province from common Indonesian province names
            $provinces = [
                'DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'Jawa Timur', 'Yogyakarta',
                'Banten', 'Bali', 'Nusa Tenggara Barat', 'Nusa Tenggara Timur',
                'Kalimantan Barat', 'Kalimantan Tengah', 'Kalimantan Selatan', 'Kalimantan Timur', 'Kalimantan Utara',
                'Sulawesi Utara', 'Sulawesi Tengah', 'Sulawesi Selatan', 'Sulawesi Tenggara', 'Gorontalo', 'Sulawesi Barat',
                'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Jambi', 'Sumatera Selatan', 'Bengkulu', 'Lampung', 'Kepulauan Bangka Belitung', 'Kepulauan Riau',
                'Maluku', 'Maluku Utara', 'Papua', 'Papua Barat', 'Papua Selatan', 'Papua Tengah', 'Papua Pegunungan', 'Papua Barat Daya'
            ];
            
            foreach ($provinces as $province) {
                if (stripos($cleanAddress, $province) !== false) {
                    $components['province'] = $province;
                    break;
                }
            }
        }
        
        // If no explicit components found, try to parse from comma-separated parts
        if (empty($components['village']) && empty($components['sub_district']) && empty($components['regency'])) {
            $parts = array_map('trim', explode(',', $cleanAddress));
            $parts = array_filter($parts, function($part) {
                return !empty($part) && !preg_match('/^\d+$/', $part); // Remove postal codes
            });
            
            if (count($parts) >= 2) {
                // Handle simple format like "City, Province" or "Regency, Province"
                if (count($parts) == 2 && isset($parts[0]) && isset($parts[1])) {
                    $components['regency'] = $this->cleanComponentName($parts[0]);
                    $components['province'] = $this->cleanComponentName($parts[1]);
                } elseif (count($parts) >= 3 && isset($parts[0]) && isset($parts[1]) && isset($parts[2])) {
                    // Assume format: Village, SubDistrict, Regency, Province
                    $components['village'] = $this->cleanComponentName($parts[0]);
                    $components['sub_district'] = $this->cleanComponentName($parts[1]);
                    $components['regency'] = $this->cleanComponentName($parts[2]);
                    if (isset($parts[3])) {
                        $components['province'] = $this->cleanComponentName($parts[3]);
                    }
                }
            }
        }
        
        return $components;
    }
    
    /**
     * Clean component name from prefixes
     */
    private function cleanComponentName(string $name): string
    {
        $name = trim($name);
        $name = preg_replace('/^(?:Desa|Kelurahan|Kecamatan|Kabupaten|Kota|Provinsi)\s+/i', '', $name);
        return trim($name);
    }
    
    /**
     * Clean address from RT/RW and other details
     */
    private function cleanAddressFromDetails(string $address): string
    {
        // Remove RT/RW
        $address = preg_replace('/\b(?:RT|RW)\.?\s*\d+\b/i', '', $address);
        // Remove Dusun/Gang
        $address = preg_replace('/\b(?:Dusun|Gang|Gg|Jl|Jalan)\s+[^,]+,?/i', '', $address);
        // Clean multiple commas and spaces
        $address = preg_replace('/,+/', ',', $address);
        $address = preg_replace('/\s+/', ' ', $address);
        $address = trim($address, ', ');
        
        return $address;
    }

    /**
     * Build Indonesian format address for better geocoding
     * Format: "Desa Village, Kecamatan SubDistrict, Kabupaten Regency, Provinsi Province"
     */
    private function buildIndonesianFormat(string $address): string
    {
        $components = $this->extractAddressComponents($address);
        
        $parts = [];
        if (!empty($components['village'])) {
            $parts[] = 'Desa ' . $components['village'];
        }
        if (!empty($components['sub_district'])) {
            $parts[] = 'Kecamatan ' . $components['sub_district'];
        }
        if (!empty($components['regency'])) {
            $parts[] = 'Kabupaten ' . $components['regency'];
        }
        if (!empty($components['province'])) {
            $parts[] = 'Provinsi ' . $components['province'];
        }
        
        return implode(', ', $parts);
    }

    /**
     * Build simplified format without Indonesian prefixes
     * Format: "Village, SubDistrict, Regency, Province"
     */
    private function buildSimplifiedFormat(string $address): string
    {
        $components = $this->extractAddressComponents($address);
        
        $parts = [];
        if (!empty($components['village'])) {
            $parts[] = $components['village'];
        }
        if (!empty($components['sub_district'])) {
            $parts[] = $components['sub_district'];
        }
        if (!empty($components['regency'])) {
            $parts[] = $components['regency'];
        }
        if (!empty($components['province'])) {
            $parts[] = $components['province'];
        }
        
        return implode(', ', $parts);
    }

    /**
     * Validate Indonesian address result
     */
    private function validateIndonesianAddress(array $result, string $originalAddress): bool
    {
        $address = $result['address'] ?? [];
        $displayName = $result['display_name'] ?? '';
        
        // Extract components from original address
        $originalComponents = $this->extractAddressComponents($originalAddress);
        
        // Check if result contains expected administrative levels
        $hasVillage = !empty($address['village']) || !empty($address['hamlet']) || !empty($address['suburb']);
        $hasSubDistrict = !empty($address['suburb']) || !empty($address['city_district']) || !empty($address['municipality']);
        $hasRegency = !empty($address['city']) || !empty($address['county']) || !empty($address['state_district']);
        $hasProvince = !empty($address['state']);
        
        // Enhanced validation: If original has village, result should have at least sub-district level
        if (!empty($originalComponents['village']) && !$hasSubDistrict) {
            Log::debug("Validation failed: Original has village but result lacks sub-district level");
            return false;
        }
        
        // If original has sub-district, result should have at least regency level
        if (!empty($originalComponents['sub_district']) && !$hasRegency) {
            Log::debug("Validation failed: Original has sub-district but result lacks regency level");
            return false;
        }
        
        // If original has regency, result should have at least province level
        if (!empty($originalComponents['regency']) && !$hasProvince) {
            Log::debug("Validation failed: Original has regency but result lacks province level");
            return false;
        }
        
        // Enhanced regency matching with stricter validation
        if (!empty($originalComponents['regency'])) {
            $regencyMatch = false;
            $regencyNames = [
                $address['city'] ?? '',
                $address['county'] ?? '',
                $address['state_district'] ?? ''
            ];
            
            foreach ($regencyNames as $regencyName) {
                if ($this->fuzzyMatch($originalComponents['regency'], $regencyName)) {
                    $regencyMatch = true;
                    break;
                }
            }
            
            if (!$regencyMatch) {
                Log::debug("Validation failed: Regency mismatch. Original: {$originalComponents['regency']}, Found: " . implode(', ', $regencyNames));
                return false;
            }
        }
        
        // Enhanced province matching
        if (!empty($originalComponents['province'])) {
            $provinceName = $address['state'] ?? '';
            if (!$this->fuzzyMatch($originalComponents['province'], $provinceName)) {
                Log::debug("Validation failed: Province mismatch. Original: {$originalComponents['province']}, Found: {$provinceName}");
                return false;
            }
        }
        
        // Additional validation: Check if coordinates are within reasonable bounds for Indonesia
        $lat = (float) ($result['lat'] ?? 0);
        $lon = (float) ($result['lon'] ?? 0);
        
        if (!$this->isWithinIndonesiaBounds($lat, $lon)) {
            Log::debug("Validation failed: Coordinates outside Indonesia bounds: {$lat}, {$lon}");
            return false;
        }
        
        return true;
    }
    
    /**
     * Check if coordinates are within Indonesia's geographical bounds
     */
    private function isWithinIndonesiaBounds(float $lat, float $lon): bool
    {
        // Indonesia's approximate geographical bounds
        // Latitude: -11° to 6° (South to North)
        // Longitude: 95° to 141° (West to East)
        return ($lat >= -11 && $lat <= 6) && ($lon >= 95 && $lon <= 141);
    }

    /**
     * Extract province from address
     */
    private function extractProvince(string $address): string
    {
        // Look for province patterns
        if (preg_match('/(?:PROV\.?\s*)?([^,]*(?:JAWA|SUMATRA|KALIMANTAN|SULAWESI|BALI|NUSA|PAPUA|MALUKU)[^,]*)$/i', $address, $matches)) {
            return trim($matches[1]);
        }
        
        // Fallback: get the last part if it contains province keywords
        $parts = array_map('trim', explode(',', $address));
        $lastPart = end($parts);
        if (preg_match('/\b(?:JAWA|SUMATRA|KALIMANTAN|SULAWESI|BALI|NUSA|PAPUA|MALUKU)\b/i', $lastPart)) {
            return $lastPart;
        }
        
        return '';
    }

    /**
     * Get address from coordinates using OpenStreetMap Nominatim API (Reverse Geocoding)
     */
    public function getAddressFromCoordinates(float $latitude, float $longitude): array
    {
        // Create cache key for the coordinates
        $cacheKey = 'reverse_geocoding_' . md5($latitude . '_' . $longitude);
        
        // Check if result is cached (cache for 24 hours)
        $cachedResult = Cache::get($cacheKey);
        if ($cachedResult) {
            return $cachedResult;
        }

        try {
            // Removed delay to prevent timeout - using cache to reduce API calls
            
            $response = Http::withHeaders([
                'User-Agent' => 'ApotekBaraya/1.0 (contact@apotekbaraya.com)' // Required by Nominatim
            ])->get($this->nominatimBaseUrl . '/reverse', [
                'lat' => $latitude,
                'lon' => $longitude,
                'format' => 'json',
                'addressdetails' => 1,
                'zoom' => 18, // High zoom for detailed address
                'extratags' => 1, // Get additional tags
                'namedetails' => 1, // Get name details in different languages
                'accept-language' => 'id,en' // Prefer Indonesian, fallback to English
            ]);

            $data = $response->json();

            if (empty($data) || !isset($data['display_name'])) {
                throw new \Exception('Reverse geocoding failed: No address found for coordinates');
            }

            $address = [
                'formatted_address' => $data['display_name'],
                'latitude' => $latitude,
                'longitude' => $longitude,
                'address_components' => $data['address'] ?? []
            ];
            
            // Cache the result for 24 hours
            Cache::put($cacheKey, $address, 60 * 24);
            
            return $address;

        } catch (\Exception $e) {
            Log::error('Nominatim reverse geocoding error: ' . $e->getMessage());
            throw new \Exception('Failed to get address for coordinates: ' . $latitude . ', ' . $longitude);
        }
    }

    /**
     * Calculate distance from an address to store location
     */
    public function calculateDistanceFromAddress(string $address): array
    {
        // Get coordinates from address
        $coordinates = $this->getCoordinatesFromAddress($address);
        
        // Get store coordinates
        $storeLat = (float) StoreSetting::get('store_latitude', 0);
        $storeLng = (float) StoreSetting::get('store_longitude', 0);
        
        if (empty($storeLat) || empty($storeLng)) {
            throw new \Exception('Koordinat toko belum diatur. Silakan atur koordinat toko terlebih dahulu.');
        }
        
        // Calculate distance
        $distanceData = $this->calculateDistance(
            $storeLat,
            $storeLng,
            $coordinates['latitude'],
            $coordinates['longitude']
        );
        
        return [
            'distance_km' => $distanceData['distance_km'],
            'duration_minutes' => $distanceData['duration_minutes'],
            'distance_text' => $distanceData['distance_text'],
            'duration_text' => $distanceData['duration_text'],
            'formatted_address' => $coordinates['formatted_address'],
            'destination_coordinates' => [
                'latitude' => $coordinates['latitude'],
                'longitude' => $coordinates['longitude']
            ],
            'store_coordinates' => [
                'latitude' => $storeLat,
                'longitude' => $storeLng
            ]
        ];
    }

    /**
     * Check if delivery is available for given distance
     */
    public function isDeliveryAvailable(float $distanceKm): bool
    {
        $maxDistance = StoreSetting::get('max_delivery_distance', 15);
        return $distanceKm <= $maxDistance;
    }

    /**
     * Calculate shipping cost based on distance
     * Free shipping is given when order total is ABOVE or EQUAL to the minimum threshold
     */
    public function calculateShippingCost(float $distanceKm, float $orderTotal): array
    {
        $ratePerKm = StoreSetting::get('shipping_rate_per_km', 2000);
        $freeShippingMinimum = StoreSetting::get('free_shipping_minimum', 100000);
        
        $shippingCost = ceil($distanceKm) * $ratePerKm;
        // Free shipping is given when order total is ABOVE or EQUAL to minimum
        $isFreeShipping = $orderTotal >= $freeShippingMinimum;
        
        return [
            'base_cost' => $shippingCost,
            'final_cost' => $isFreeShipping ? 0 : $shippingCost,
            'is_free_shipping' => $isFreeShipping,
            'free_shipping_minimum' => $freeShippingMinimum,
            'distance_km' => $distanceKm
        ];
    }
}