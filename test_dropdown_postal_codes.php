<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test dropdown postal codes for Subang area
echo "=== Testing Dropdown Postal Codes for Subang ===\n\n";

$addressService = app(App\Services\AddressService::class);

// Test 1: Get sub districts for Subang
echo "1. Testing getSubDistricts for Subang:\n";
$subDistricts = $addressService->getSubDistricts('jawa_barat', 'subang');
echo "Found " . count($subDistricts) . " sub districts\n";

// Find Subang sub district key
$subangKey = null;
foreach ($subDistricts as $key => $name) {
    if (stripos($name, 'Subang') !== false && !stripos($name, 'X')) {
        $subangKey = $key;
        echo "Found Subang key: {$key} => {$name}\n";
        break;
    }
}

if (!$subangKey) {
    echo "ERROR: Subang sub district not found!\n";
    exit(1);
}

echo "\n2. Testing getVillages for Subang sub district:\n";
$villages = $addressService->getVillages('jawa_barat', 'subang', $subangKey);
echo "Found " . count($villages) . " villages\n";

// Find Parung village key
$parungKey = null;
foreach ($villages as $key => $name) {
    if (stripos($name, 'Parung') !== false) {
        $parungKey = $key;
        echo "Found Parung key: {$key} => {$name}\n";
        break;
    }
}

if (!$parungKey) {
    echo "ERROR: Parung village not found!\n";
    exit(1);
}

echo "\n3. Testing getPostalCodes for Parung village:\n";
$postalCodes = $addressService->getPostalCodes('jawa_barat', 'subang', $subangKey, $parungKey);
echo "Found " . count($postalCodes) . " postal codes for Parung:\n";
foreach ($postalCodes as $key => $code) {
    echo "  {$key} => {$code}\n";
}

// Test 4: Validate address with postal code
echo "\n4. Testing address validation:\n";
if (!empty($postalCodes)) {
    $firstPostalCode = array_values($postalCodes)[0];
    echo "Testing with postal code: {$firstPostalCode}\n";
    
    $isValid = $addressService->isValidAddress($parungKey, $subangKey, $firstPostalCode);
    echo "Address validation result: " . ($isValid ? 'VALID' : 'INVALID') . "\n";
    
    // Test geocoding
    echo "\n5. Testing geocoding with postal code:\n";
    $geocodingService = app(App\Services\GeocodingService::class);
    $coordinates = $geocodingService->geocodeAddress(
        $parungKey,
        $subangKey,
        'subang',
        'jawa_barat',
        $firstPostalCode
    );
    
    if ($coordinates) {
        echo "Geocoding SUCCESS:\n";
        echo "  Latitude: {$coordinates['lat']}\n";
        echo "  Longitude: {$coordinates['lon']}\n";
        echo "  Source: {$coordinates['source']}\n";
        if (isset($coordinates['distance_km'])) {
            echo "  Distance: {$coordinates['distance_km']} km\n";
        }
    } else {
        echo "Geocoding FAILED: Address not found\n";
    }
} else {
    echo "ERROR: No postal codes found for Parung!\n";
}

echo "\n=== Test Complete ===\n";