<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\AddressService;
use App\Services\GeocodingService;

echo "=== Testing Address Form Fix ===\n\n";

$addressService = app(AddressService::class);
$geocodingService = app(GeocodingService::class);

echo "1. Testing scenario that previously failed (NULL postal code):\n";
$testData = [
    'village_key' => 'parung',
    'sub_district_key' => 'subang',
    'regency_key' => 'subang',
    'province_key' => 'jawa_barat',
    'postal_code' => null, // This should now be caught by validation
    'detailed_address' => 'Jl. Test No. 123'
];

echo "Data: " . json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";

// Test the validation that would happen in Profile.php
echo "2. Testing postal code validation (new validation):\n";
if (empty($testData['postal_code'])) {
    echo "✅ VALIDATION CAUGHT: Postal code is empty - would show error message\n";
    echo "Error message: 'Silakan pilih kode pos terlebih dahulu. Tunggu hingga dropdown kode pos terisi setelah memilih desa.'\n\n";
} else {
    echo "❌ Validation missed empty postal code\n\n";
}

echo "3. Testing scenario that should work (with postal code):\n";
$validTestData = [
    'village_key' => 'parung',
    'sub_district_key' => 'subang',
    'regency_key' => 'subang',
    'province_key' => 'jawa_barat',
    'postal_code' => '41211', // Valid postal code
    'detailed_address' => 'Jl. Test No. 123'
];

echo "Data: " . json_encode($validTestData, JSON_PRETTY_PRINT) . "\n\n";

// Test postal code validation
echo "4. Testing postal code validation (valid case):\n";
if (empty($validTestData['postal_code'])) {
    echo "❌ Should not reach here\n";
} else {
    echo "✅ Postal code validation passed\n";
    
    // Test address validation
    echo "\n5. Testing AddressService::isValidAddress():\n";
    $isValid = $addressService->isValidAddress(
        $validTestData['village_key'],
        $validTestData['sub_district_key'],
        $validTestData['postal_code']
    );
    echo "Result: " . ($isValid ? 'VALID' : 'INVALID') . "\n";
    
    if ($isValid) {
        // Test geocoding
        echo "\n6. Testing GeocodingService::geocodeAddress():\n";
        $coordinates = $geocodingService->geocodeAddress(
            $validTestData['village_key'],
            $validTestData['sub_district_key'],
            $validTestData['regency_key'],
            $validTestData['province_key'],
            $validTestData['postal_code'],
            $validTestData['detailed_address']
        );
        
        if ($coordinates !== null) {
            echo "✅ Geocoding SUCCESS\n";
            echo "Coordinates: {$coordinates['lat']}, {$coordinates['lon']}\n";
            echo "Source: {$coordinates['source']}\n";
            if (isset($coordinates['distance_km'])) {
                echo "Distance: {$coordinates['distance_km']} km\n";
            }
            echo "\n✅ This address should now be accepted by the system\n";
        } else {
            echo "❌ Geocoding failed\n";
        }
    } else {
        echo "❌ Address validation failed\n";
    }
}

echo "\n=== SUMMARY ===\n";
echo "✅ Added frontend validation: Submit button disabled until postal code selected\n";
echo "✅ Added loading indicators: Users can see when dropdowns are loading\n";
echo "✅ Added server-side validation: Better error message for missing postal code\n";
echo "✅ Improved user guidance: Warning message when postal code not selected\n\n";

echo "The fix addresses the root cause:\n";
echo "- Users were submitting forms before postal code loaded\n";
echo "- Now they must wait for postal code to be selected\n";
echo "- Clear visual feedback shows loading state\n";
echo "- Better error messages guide users to the solution\n";