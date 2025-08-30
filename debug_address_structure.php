<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\AddressService;
use App\Models\UserAddress;

echo "=== Debugging Address Structure ===\n";

// Load address data directly
$jsonPath = base_path('alamatsubang.json');
$addressData = json_decode(file_get_contents($jsonPath), true);

echo "\n=== Address Data Structure ===\n";
foreach ($addressData as $key => $data) {
    echo "Key: '{$key}'\n";
    echo "Formatted Key: '" . strtolower(str_replace(' ', '_', $key)) . "'\n";
    if (isset($data['desa']) && is_array($data['desa'])) {
        echo "Villages: " . count($data['desa']) . " villages\n";
        $villages = array_slice($data['desa'], 0, 3); // Show first 3 villages
        foreach ($villages as $village) {
            echo "  - Village: '{$village}' -> Formatted: '" . strtolower(str_replace(' ', '_', $village)) . "'\n";
        }
        if (count($data['desa']) > 3) {
            echo "  ... and " . (count($data['desa']) - 3) . " more villages\n";
        }
    }
    echo "\n";
}

// Get test address from database
echo "\n=== Test Address from Database ===\n";
$testAddress = UserAddress::latest()->first();
if ($testAddress) {
    echo "Address ID: {$testAddress->id}\n";
    echo "Village Key: '{$testAddress->village_key}'\n";
    echo "Sub District Key: '{$testAddress->sub_district_key}'\n";
    echo "Regency Key: '{$testAddress->regency_key}'\n";
    echo "Province Key: '{$testAddress->province_key}'\n";
    
    // Try to find matching keys in address data
    echo "\n=== Trying to Match Keys ===\n";
    
    $found = false;
    foreach ($addressData as $key => $data) {
        $formattedKey = strtolower(str_replace(' ', '_', $key));
        if ($formattedKey === $testAddress->sub_district_key) {
            echo "Found matching sub-district: '{$key}' for key '{$testAddress->sub_district_key}'\n";
            $found = true;
            
            if (isset($data['desa']) && is_array($data['desa'])) {
                foreach ($data['desa'] as $village) {
                    $villageFormatted = strtolower(str_replace(' ', '_', $village));
                    if ($villageFormatted === $testAddress->village_key) {
                        echo "Found matching village: '{$village}' for key '{$testAddress->village_key}'\n";
                        break;
                    }
                }
            }
            break;
        }
    }
    
    if (!$found) {
        echo "No matching sub-district found for key: '{$testAddress->sub_district_key}'\n";
        echo "Available sub-district keys:\n";
        foreach ($addressData as $key => $data) {
            echo "  - '{$key}' -> formatted: '" . strtolower(str_replace(' ', '_', $key)) . "'\n";
        }
    }
} else {
    echo "No test address found in database.\n";
}

echo "\n=== Debug Complete ===\n";