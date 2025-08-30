<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\GeocodingService;
use App\Services\AddressService;
use App\Services\DistanceCalculatorService;

echo "=== Testing Address Validation for Parung, Subang ===\n\n";

try {
    // Test data yang sama dengan yang digunakan user
    $testCases = [
        [
            'village' => 'Parung',
            'sub_district' => 'Subang',
            'regency' => 'Subang',
            'province' => 'Jawa Barat',
            'postal_code' => null
        ],
        [
            'village' => 'Parung',
            'sub_district' => 'Subang',
            'regency' => 'Subang', 
            'province' => 'Jawa Barat',
            'postal_code' => '41211' // Kode pos Subang
        ]
    ];
    
    $geocodingService = app(GeocodingService::class);
    $addressService = app(AddressService::class);
    $distanceService = app(DistanceCalculatorService::class);
    
    foreach ($testCases as $index => $testCase) {
        echo "Test Case " . ($index + 1) . ":\n";
        echo "Village: {$testCase['village']}\n";
        echo "Sub District: {$testCase['sub_district']}\n";
        echo "Postal Code: " . ($testCase['postal_code'] ?? 'null') . "\n\n";
        
        // Test 1: Address validation
        echo "1. Testing AddressService::isValidAddress()...\n";
        $isValid = $addressService->isValidAddress(
            $testCase['village'],
            $testCase['sub_district'],
            $testCase['postal_code']
        );
        echo "   Result: " . ($isValid ? '✅ Valid' : '❌ Invalid') . "\n\n";
        
        // Test 2: Direct distance lookup
        echo "2. Testing DistanceCalculatorService::getDirectDistance()...\n";
        $directDistance = $distanceService->getDirectDistance(
            $testCase['village'],
            $testCase['sub_district'],
            $testCase['postal_code']
        );
        echo "   Result: " . ($directDistance !== null ? "✅ Found: {$directDistance} km" : '❌ Not found') . "\n\n";
        
        // Test 3: Geocoding service
        echo "3. Testing GeocodingService::geocodeAddress()...\n";
        $coordinates = $geocodingService->geocodeAddress(
            $testCase['village'],
            $testCase['sub_district'],
            $testCase['regency'],
            $testCase['province'],
            $testCase['postal_code']
        );
        
        if ($coordinates !== null) {
            echo "   Result: ✅ Success\n";
            echo "   Coordinates: {$coordinates['lat']}, {$coordinates['lon']}\n";
            echo "   Source: {$coordinates['source']}\n";
            if (isset($coordinates['distance_km'])) {
                echo "   Distance: {$coordinates['distance_km']} km\n";
            }
        } else {
            echo "   Result: ❌ Failed (null returned)\n";
        }
        
        echo "\n" . str_repeat('-', 50) . "\n\n";
    }
    
    echo "=== Summary ===\n";
    echo "Jika semua test menunjukkan ✅, maka masalah sudah teratasi.\n";
    echo "Jika masih ada ❌, periksa struktur data di alamatsubang.json.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== End of Test ===\n";