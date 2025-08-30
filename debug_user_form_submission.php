<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\AddressService;
use App\Services\GeocodingService;

echo "=== Debug User Form Submission Issue ===\n\n";

// Test scenario: User fills form but doesn't wait for postal code to load
echo "1. Testing scenario: User selects village but postal code is empty\n";

$addressService = app(AddressService::class);
$geocodingService = app(GeocodingService::class);

// Simulate user selection
$provinceKey = 'jawa_barat';
$regencyKey = 'subang';
$subDistrictKey = 'subang';
$villageKey = 'parung';
$postalCode = null; // User didn't select postal code

echo "Province: {$provinceKey}\n";
echo "Regency: {$regencyKey}\n";
echo "Sub District: {$subDistrictKey}\n";
echo "Village: {$villageKey}\n";
echo "Postal Code: " . ($postalCode ?? 'NULL') . "\n\n";

// Test address validation (this is what fails)
echo "2. Testing AddressService::isValidAddress():\n";
$isValid = $addressService->isValidAddress($villageKey, $subDistrictKey, $postalCode);
echo "Result: " . ($isValid ? 'VALID' : 'INVALID') . "\n";

if (!$isValid) {
    echo "❌ This is why validation fails!\n\n";
    
    echo "3. Testing with correct postal code:\n";
    $correctPostalCode = '41211';
    echo "Postal Code: {$correctPostalCode}\n";
    $isValidWithCode = $addressService->isValidAddress($villageKey, $subDistrictKey, $correctPostalCode);
    echo "Result: " . ($isValidWithCode ? 'VALID' : 'INVALID') . "\n";
    
    if ($isValidWithCode) {
        echo "✅ Validation passes with postal code!\n\n";
    }
}

// Test geocoding (this is what returns null)
echo "4. Testing GeocodingService::geocodeAddress() with NULL postal code:\n";
$coordinates = $geocodingService->geocodeAddress(
    $villageKey,
    $subDistrictKey,
    $regencyKey,
    $provinceKey,
    null, // NULL postal code
    'Jl. Test No. 123'
);

if ($coordinates === null) {
    echo "Result: NULL (this triggers 'Alamat di luar jangkauan' error)\n";
    echo "❌ This is the exact problem user experiences!\n\n";
} else {
    echo "Result: SUCCESS\n";
    echo "Coordinates: {$coordinates['lat']}, {$coordinates['lon']}\n";
    echo "Source: {$coordinates['source']}\n\n";
}

// Test geocoding with correct postal code
echo "5. Testing GeocodingService::geocodeAddress() with correct postal code:\n";
$coordinatesWithCode = $geocodingService->geocodeAddress(
    $villageKey,
    $subDistrictKey,
    $regencyKey,
    $provinceKey,
    '41211', // Correct postal code
    'Jl. Test No. 123'
);

if ($coordinatesWithCode === null) {
    echo "Result: NULL\n";
    echo "❌ Still failing even with postal code!\n";
} else {
    echo "Result: SUCCESS\n";
    echo "Coordinates: {$coordinatesWithCode['lat']}, {$coordinatesWithCode['lon']}\n";
    echo "Source: {$coordinatesWithCode['source']}\n";
    echo "✅ This should work!\n\n";
}

echo "=== CONCLUSION ===\n";
echo "The issue is that users are submitting the form before postal code dropdown loads.\n";
echo "When postal code is NULL, AddressService::isValidAddress() returns false.\n";
echo "This causes GeocodingService::geocodeAddress() to return null.\n";
echo "Which triggers the 'Alamat di luar jangkauan' error in Profile.php line 595.\n\n";

echo "SOLUTION: Prevent form submission until postal code is selected.\n";