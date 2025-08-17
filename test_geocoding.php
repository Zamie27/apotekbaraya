<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\DistanceCalculatorService;

$service = new DistanceCalculatorService();

// Test addresses with original format as reported by user
$addresses = [
    'Desa Tanjungwangi, Kecamatan Cijambe, Kabupaten Subang, Provinsi Jawa Barat 41286',
    'Desa Gardumukti, Kecamatan Tambakdahan, Kabupaten Subang, Provinsi Jawa Barat 41253'
];

foreach ($addresses as $index => $address) {
    echo "\n=== Testing Address " . ($index + 1) . " ===\n";
    echo "Address: $address\n";

    try {
        // Test full geocoding process
        echo "\n--- Full Geocoding Test ---\n";
        $coordinates = $service->getCoordinatesFromAddress($address);
        if ($coordinates) {
            echo "Coordinates found: {$coordinates['latitude']}, {$coordinates['longitude']}\n";

            // Test reverse geocoding to verify accuracy
            echo "\n--- Reverse Geocoding Test ---\n";
            $reverseAddress = $service->getAddressFromCoordinates($coordinates['latitude'], $coordinates['longitude']);
            echo "Reverse geocoded address: $reverseAddress\n";
        } else {
            echo "No coordinates found\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";

        // Try with simpler format
        echo "\n--- Testing with simpler format ---\n";
        $simpleAddress = "Subang, Jawa Barat";
        echo "Simple address: $simpleAddress\n";

        try {
            $simpleCoordinates = $service->getCoordinatesFromAddress($simpleAddress);
            if ($simpleCoordinates) {
                echo "Simple coordinates found: {$simpleCoordinates['latitude']}, {$simpleCoordinates['longitude']}\n";
            }
        } catch (Exception $e2) {
            echo "Simple geocoding also failed: " . $e2->getMessage() . "\n";
        }
    }

    echo "\n" . str_repeat("=", 50) . "\n";
}

// Test if the problematic coordinates exist in database
echo "\n=== Checking Database for Problematic Coordinates ===\n";
echo "Looking for coordinates: -6.56898070, 107.75873970\n";

try {
    // Check user_addresses table
    $userAddresses = \DB::table('user_addresses')
        ->where('latitude', 'LIKE', '-6.56898%')
        ->orWhere('longitude', 'LIKE', '107.75873%')
        ->select('*')
        ->get();

    if ($userAddresses->count() > 0) {
        echo "Found " . $userAddresses->count() . " addresses with these coordinates:\n";
        foreach ($userAddresses as $addr) {
            echo "- Address: {$addr->detailed_address}\n";
            echo "  Lat: {$addr->latitude}, Lng: {$addr->longitude}\n";
            echo "  Village: {$addr->village}, Sub-district: {$addr->sub_district}\n";
            echo "  Regency: {$addr->regency}, Province: {$addr->province}\n";
            echo "  Postal Code: {$addr->postal_code}\n\n";
        }
    } else {
        echo "No addresses found with these coordinates in database.\n";
    }

    // Also check for any addresses with same coordinates but different precision
    echo "\n--- Checking for similar coordinates ---\n";
    $similarAddresses = \DB::table('user_addresses')
        ->whereBetween('latitude', [-6.57, -6.56])
        ->whereBetween('longitude', [107.75, 107.76])
        ->select('*')
        ->get();

    if ($similarAddresses->count() > 0) {
        echo "Found " . $similarAddresses->count() . " addresses with similar coordinates:\n";
        foreach ($similarAddresses as $addr) {
            echo "- {$addr->detailed_address} => Lat: {$addr->latitude}, Lng: {$addr->longitude}\n";
        }
    }
} catch (Exception $e) {
    echo "Database check failed: " . $e->getMessage() . "\n";
}
