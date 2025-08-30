<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\UserAddress;
use App\Services\AddressService;

echo "=== Debugging Address Data ===\n";

// Get first address
$address = UserAddress::first();

if (!$address) {
    echo "No addresses found in database.\n";
    exit;
}

echo "Address ID: {$address->id}\n";
echo "User ID: {$address->user_id}\n";
echo "Address Detail: '{$address->address_detail}'\n";
echo "Village Key: '{$address->village_key}'\n";
echo "District Key: '{$address->district_key}'\n";
echo "Regency Key: '{$address->regency_key}'\n";
echo "Province Key: '{$address->province_key}'\n";
echo "Postal Code: '{$address->postal_code}'\n";
echo "Latitude: {$address->latitude}\n";
echo "Longitude: {$address->longitude}\n";
echo "\n";

// Test AddressService
$addressService = new AddressService();

echo "=== Testing AddressService ===\n";

if ($address->village_key) {
    $villageName = $addressService->getVillageName($address->village_key);
    echo "Village Name: '{$villageName}'\n";
}

if ($address->district_key) {
    $districtName = $addressService->getDistrictName($address->district_key);
    echo "District Name: '{$districtName}'\n";
}

if ($address->regency_key) {
    $regencyName = $addressService->getRegencyName($address->regency_key);
    echo "Regency Name: '{$regencyName}'\n";
}

if ($address->province_key) {
    $provinceName = $addressService->getProvinceName($address->province_key);
    echo "Province Name: '{$provinceName}'\n";
}

// Test address variations
echo "\n=== Testing Address Variations ===\n";
if ($address->village_key && $address->district_key && $address->regency_key && $address->province_key) {
    $variations = $addressService->getAddressVariations(
        $address->village_key,
        $address->district_key,
        $address->regency_key,
        $address->province_key,
        $address->postal_code,
        $address->address_detail
    );
    foreach ($variations as $i => $variation) {
        echo "Variation " . ($i + 1) . ": '{$variation}'\n";
    }
} else {
    echo "Cannot generate variations - missing required address keys\n";
    echo "Missing keys: ";
    $missing = [];
    if (!$address->village_key) $missing[] = 'village_key';
    if (!$address->district_key) $missing[] = 'district_key';
    if (!$address->regency_key) $missing[] = 'regency_key';
    if (!$address->province_key) $missing[] = 'province_key';
    echo implode(', ', $missing) . "\n";
}

echo "\nDone.\n";