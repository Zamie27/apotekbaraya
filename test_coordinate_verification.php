<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\DistanceCalculatorService;
use Illuminate\Support\Facades\Log;

echo "=== Testing Coordinate Verification System ===\n\n";

$distanceService = new DistanceCalculatorService();

// Test addresses with known issues
$testAddresses = [
    'Subang, Jawa Barat', // Should work and be verified
    'Jakarta, DKI Jakarta', // Should work and be verified
    'Bandung, Jawa Barat', // Should work and be verified
    'Desa Tanjungwangi, Kecamatan Cijambe, Kabupaten Subang, Provinsi Jawa Barat', // Should fail gracefully
];

foreach ($testAddresses as $index => $address) {
    echo "=== Test " . ($index + 1) . " ===\n";
    echo "Address: {$address}\n\n";
    
    try {
        $coordinates = $distanceService->getCoordinatesFromAddress($address);
        
        echo "✅ Geocoding successful!\n";
        echo "Coordinates: {$coordinates['latitude']}, {$coordinates['longitude']}\n";
        echo "Formatted Address: {$coordinates['formatted_address']}\n";
        echo "Search Strategy: {$coordinates['search_strategy']}\n";
        echo "Confidence Score: {$coordinates['confidence_score']}\n";
        echo "Verified: " . ($coordinates['verified'] ? 'Yes' : 'No') . "\n";
        
        
        // Test reverse geocoding for verification
        echo "\n--- Reverse Geocoding Test ---\n";
        try {
            $reverseAddress = $distanceService->getAddressFromCoordinates(
                $coordinates['latitude'], 
                $coordinates['longitude']
            );
            echo "Reverse geocoded address: {$reverseAddress['formatted_address']}\n";
        } catch (Exception $e) {
            echo "❌ Reverse geocoding failed: {$e->getMessage()}\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Geocoding failed: {$e->getMessage()}\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n\n";
}

// Test coordinate verification with known coordinates
echo "=== Testing Coordinate Verification Function ===\n\n";

$testCoordinates = [
    // Jakarta coordinates
    ['lat' => -6.2088, 'lng' => 106.8456, 'address' => 'Jakarta, DKI Jakarta'],
    // Bandung coordinates  
    ['lat' => -6.9175, 'lng' => 107.6191, 'address' => 'Bandung, Jawa Barat'],
    // Wrong coordinates for Jakarta (using Bandung coordinates)
    ['lat' => -6.9175, 'lng' => 107.6191, 'address' => 'Jakarta, DKI Jakarta'],
];



foreach ($testCoordinates as $index => $test) {
    echo "Test " . ($index + 1) . ": {$test['address']}\n";
    echo "Coordinates: {$test['lat']}, {$test['lng']}\n";
    
    try {
        // Use reflection to access private method for testing
        $reflection = new ReflectionClass($distanceService);
        $method = $reflection->getMethod('verifyCoordinatesAccuracy');
        $method->setAccessible(true);
        
        $isVerified = $method->invoke(
            $distanceService, 
            $test['lat'], 
            $test['lng'], 
            $test['address']
        );
        
        echo "Verification result: " . ($isVerified ? '✅ Verified' : '❌ Not verified') . "\n";
        
    } catch (Exception $e) {
        echo "❌ Verification test failed: {$e->getMessage()}\n";
    }
    
    echo "\n";
}

echo "=== Test Complete ===\n";