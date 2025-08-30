<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Debug address form flow for Subang > Parung
echo "=== Debugging Address Form Flow for Subang > Parung ==="."\n\n";

$addressService = app(App\Services\AddressService::class);

// Simulate user selecting dropdowns step by step
echo "1. User selects Province: Jawa Barat\n";
$provinces = $addressService->getProvinces();
echo "Available provinces: ".count($provinces)."\n";
$provinceKey = 'jawa_barat';
echo "Selected province key: {$provinceKey}\n\n";

echo "2. User selects Regency: Subang\n";
$regencies = $addressService->getRegencies($provinceKey);
echo "Available regencies: ".count($regencies)."\n";
$regencyKey = 'subang';
echo "Selected regency key: {$regencyKey}\n\n";

echo "3. User selects Sub District: Subang\n";
$subDistricts = $addressService->getSubDistricts($provinceKey, $regencyKey);
echo "Available sub districts: ".count($subDistricts)."\n";

// Find Subang sub district
$subDistrictKey = null;
foreach ($subDistricts as $key => $name) {
    if (stripos($name, 'Subang') !== false && !stripos($name, 'X')) {
        $subDistrictKey = $key;
        echo "Found Subang sub district: {$key} => {$name}\n";
        break;
    }
}

if (!$subDistrictKey) {
    echo "ERROR: Subang sub district not found!\n";
    exit(1);
}
echo "Selected sub district key: {$subDistrictKey}\n\n";

echo "4. User selects Village: Parung\n";
$villages = $addressService->getVillages($provinceKey, $regencyKey, $subDistrictKey);
echo "Available villages: ".count($villages)."\n";

// Find Parung village
$villageKey = null;
foreach ($villages as $key => $name) {
    if (stripos($name, 'Parung') !== false) {
        $villageKey = $key;
        echo "Found Parung village: {$key} => {$name}\n";
        break;
    }
}

if (!$villageKey) {
    echo "ERROR: Parung village not found!\n";
    exit(1);
}
echo "Selected village key: {$villageKey}\n\n";

echo "5. System auto-fills Postal Code (updatePostalCodes method):\n";
$postalCodes = $addressService->getPostalCodes($provinceKey, $regencyKey, $subDistrictKey, $villageKey);
echo "Available postal codes: ".count($postalCodes)."\n";

if (count($postalCodes) === 1) {
    $selectedPostalCode = (string) array_values($postalCodes)[0];
    echo "AUTO-SELECTED postal code: {$selectedPostalCode}\n";
} else {
    echo "Multiple postal codes available, user must select manually:\n";
    foreach ($postalCodes as $key => $code) {
        echo "  {$key} => {$code}\n";
    }
    $selectedPostalCode = '';
}

echo "\n6. Final form state:\n";
echo "Province Key: {$provinceKey}\n";
echo "Regency Key: {$regencyKey}\n";
echo "Sub District Key: {$subDistrictKey}\n";
echo "Village Key: {$villageKey}\n";
echo "Postal Code: {$selectedPostalCode}\n";

echo "\n7. Testing address validation:\n";
if ($selectedPostalCode) {
    $isValid = $addressService->isValidAddress($villageKey, $subDistrictKey, $selectedPostalCode);
    echo "Address validation: ".($isValid ? 'VALID' : 'INVALID')."\n";
    
    if ($isValid) {
        echo "\n8. Testing geocoding:\n";
        $geocodingService = app(App\Services\GeocodingService::class);
        $coordinates = $geocodingService->geocodeAddress(
            $villageKey,
            $subDistrictKey,
            $regencyKey,
            $provinceKey,
            $selectedPostalCode
        );
        
        if ($coordinates) {
            echo "Geocoding SUCCESS:\n";
            echo "  Coordinates: {$coordinates['lat']}, {$coordinates['lon']}\n";
            echo "  Source: {$coordinates['source']}\n";
            if (isset($coordinates['distance_km'])) {
                echo "  Distance: {$coordinates['distance_km']} km\n";
            }
            echo "\n✅ Address should be ACCEPTED by the system\n";
        } else {
            echo "Geocoding FAILED: Address not found\n";
            echo "\n❌ This is why user gets 'Alamat di luar jangkauan' error\n";
        }
    } else {
        echo "\n❌ Address validation failed - this is why user gets error\n";
    }
} else {
    echo "No postal code selected - user must select manually\n";
    echo "\n⚠️ If user doesn't select postal code, validation will fail\n";
}

echo "\n=== Debug Complete ===\n";