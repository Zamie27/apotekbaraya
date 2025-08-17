<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING NOMINATIM API RESPONSE STRUCTURE ===\n\n";

// Test with a simple address that should work
$testAddress = "Subang, Jawa Barat";

echo "Testing address: {$testAddress}\n";
echo "Making request to Nominatim API...\n\n";

try {
    $response = Http::timeout(15)->withHeaders([
        'User-Agent' => 'ApotekBaraya/1.0 (contact@apotekbaraya.com)'
    ])->get('https://nominatim.openstreetmap.org/search', [
        'q' => $testAddress,
        'format' => 'json',
        'limit' => 5,
        'addressdetails' => 1,
        'countrycodes' => 'id',
        'bounded' => 1,
        'viewbox' => '95.0,-11.0,141.0,6.0',
        'zoom' => 18,
        'dedupe' => 0,
        'extratags' => 1,
        'namedetails' => 1,
        'accept-language' => 'id,en'
    ]);
    
    if (!$response->successful()) {
        echo "API request failed with status: {$response->status()}\n";
        exit(1);
    }
    
    $data = $response->json();
    
    echo "Response status: {$response->status()}\n";
    echo "Number of results: " . count($data) . "\n\n";
    
    if (empty($data)) {
        echo "No results found!\n";
        exit(1);
    }
    
    // Check structure of first result
    $firstResult = $data[0];
    
    echo "=== FIRST RESULT STRUCTURE ===\n";
    echo "Available keys: " . implode(', ', array_keys($firstResult)) . "\n\n";
    
    // Check for required keys
    $requiredKeys = ['lat', 'lon', 'display_name'];
    $missingKeys = [];
    
    foreach ($requiredKeys as $key) {
        if (!isset($firstResult[$key])) {
            $missingKeys[] = $key;
        } else {
            echo "✓ {$key}: {$firstResult[$key]}\n";
        }
    }
    
    if (!empty($missingKeys)) {
        echo "\n✗ Missing required keys: " . implode(', ', $missingKeys) . "\n";
    } else {
        echo "\n✓ All required keys are present!\n";
    }
    
    // Show full structure of first result
    echo "\n=== FULL FIRST RESULT ===\n";
    echo json_encode($firstResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== TEST COMPLETE ===\n";