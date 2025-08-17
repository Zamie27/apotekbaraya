<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\DistanceCalculatorService;

echo "=== DEBUG GEOCODING ISSUE ===\n\n";

// Test addresses that are getting same coordinates
$testAddresses = [
    'Desa Tanjungwangi, Kecamatan Cijambe, Kabupaten Subang, Provinsi Jawa Barat 41286',
    'Desa Gardumukti, Kecamatan Tambakdahan, Kabupaten Subang, Provinsi Jawa Barat 41253'
];

$service = new DistanceCalculatorService();

foreach ($testAddresses as $index => $address) {
    echo "=== Testing Address " . ($index + 1) . " ===\n";
    echo "Address: {$address}\n\n";
    
    // Test direct Nominatim API calls with different strategies
    $strategies = [
        $address, // Original
        'Tanjungwangi, Cijambe, Subang, Jawa Barat', // Simplified
        'Gardumukti, Tambakdahan, Subang, Jawa Barat', // Simplified
        'Cijambe, Subang, Jawa Barat', // Sub-district level
        'Tambakdahan, Subang, Jawa Barat', // Sub-district level
        'Subang, Jawa Barat' // City level
    ];
    
    foreach ($strategies as $strategyIndex => $searchQuery) {
        if (($index == 0 && in_array($strategyIndex, [2, 4])) || 
            ($index == 1 && in_array($strategyIndex, [1, 3]))) {
            continue; // Skip irrelevant strategies
        }
        
        echo "--- Strategy " . ($strategyIndex + 1) . ": {$searchQuery} ---\n";
        
        try {
            $response = Http::timeout(15)->withHeaders([
                'User-Agent' => 'ApotekBaraya/1.0 (debug@apotekbaraya.com)'
            ])->get('https://nominatim.openstreetmap.org/search', [
                'q' => trim($searchQuery),
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
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (!empty($data)) {
                    echo "Found " . count($data) . " results:\n";
                    
                    foreach ($data as $resultIndex => $result) {
                        echo "  Result " . ($resultIndex + 1) . ":\n";
                        echo "    Coordinates: {$result['lat']}, {$result['lon']}\n";
                        echo "    Display Name: {$result['display_name']}\n";
                        echo "    Type: {$result['type']}\n";
                        echo "    Class: {$result['class']}\n";
                        
                        if (isset($result['address'])) {
                            echo "    Address Details:\n";
                            foreach ($result['address'] as $key => $value) {
                                echo "      {$key}: {$value}\n";
                            }
                        }
                        
                        echo "\n";
                    }
                } else {
                    echo "No results found.\n";
                }
            } else {
                echo "API request failed with status: {$response->status()}\n";
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
        sleep(1); // Respect rate limit
    }
    
    echo "\n" . str_repeat("=", 80) . "\n\n";
}

echo "=== TESTING REVERSE GEOCODING ===\n\n";

// Test reverse geocoding for the problematic coordinates
$problemCoords = [-6.56898070, 107.75873970];

echo "Reverse geocoding for coordinates: {$problemCoords[0]}, {$problemCoords[1]}\n\n";

try {
    $response = Http::timeout(15)->withHeaders([
        'User-Agent' => 'ApotekBaraya/1.0 (debug@apotekbaraya.com)'
    ])->get('https://nominatim.openstreetmap.org/reverse', [
        'lat' => $problemCoords[0],
        'lon' => $problemCoords[1],
        'format' => 'json',
        'addressdetails' => 1,
        'zoom' => 18,
        'accept-language' => 'id,en'
    ]);
    
    if ($response->successful()) {
        $data = $response->json();
        
        echo "Reverse geocoding result:\n";
        echo "Display Name: {$data['display_name']}\n";
        echo "Type: {$data['type']}\n";
        echo "Class: {$data['class']}\n";
        
        if (isset($data['address'])) {
            echo "Address Details:\n";
            foreach ($data['address'] as $key => $value) {
                echo "  {$key}: {$value}\n";
            }
        }
    } else {
        echo "Reverse geocoding failed with status: {$response->status()}\n";
    }
} catch (Exception $e) {
    echo "Reverse geocoding error: " . $e->getMessage() . "\n";
}

echo "\n=== DEBUG COMPLETE ===\n";