<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

echo "=== FIXING INCORRECT COORDINATES ===\n\n";

// Find addresses with problematic coordinates
$problematicCoords = [-6.56898070, 107.75873970];

$addresses = DB::table('user_addresses')
    ->where('latitude', $problematicCoords[0])
    ->where('longitude', $problematicCoords[1])
    ->select('*')
    ->get();

echo "Found " . $addresses->count() . " addresses with problematic coordinates:\n\n";

foreach ($addresses as $address) {
    echo "Address ID: {$address->address_id}\n";
    echo "Detailed Address: {$address->detailed_address}\n";
    echo "Village: {$address->village}\n";
    echo "Sub-district: {$address->sub_district}\n";
    echo "Regency: {$address->regency}\n";
    echo "Province: {$address->province}\n";
    echo "Postal Code: {$address->postal_code}\n";
    echo "Current Coordinates: {$address->latitude}, {$address->longitude}\n\n";
    
    // Try to get correct coordinates based on village and sub-district
    $searchQuery = "";
    $correctCoords = null;
    
    if ($address->village == 'Gardumukti' && $address->sub_district == 'Tambakdahan') {
        // We know the correct coordinates from our debug
        $correctCoords = [-6.3515677, 107.8139817];
        echo "Using known correct coordinates for Gardumukti: {$correctCoords[0]}, {$correctCoords[1]}\n";
    } elseif ($address->village == 'Tanjungwangi' && $address->sub_district == 'Cijambe') {
        // Try to find coordinates for Cijambe sub-district as fallback
        $searchQuery = "Cijambe, Subang, Jawa Barat";
        echo "Searching for coordinates of sub-district: {$searchQuery}\n";
        
        try {
            $response = Http::timeout(15)->withHeaders([
                'User-Agent' => 'ApotekBaraya/1.0 (fix@apotekbaraya.com)'
            ])->get('https://nominatim.openstreetmap.org/search', [
                'q' => $searchQuery,
                'format' => 'json',
                'limit' => 3,
                'addressdetails' => 1,
                'countrycodes' => 'id',
                'bounded' => 1,
                'viewbox' => '95.0,-11.0,141.0,6.0',
                'zoom' => 16,
                'accept-language' => 'id,en'
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (!empty($data)) {
                    // Look for the best result (preferably village or administrative)
                    foreach ($data as $result) {
                        if (isset($result['address']['county']) && 
                            stripos($result['address']['county'], 'Subang') !== false) {
                            $correctCoords = [(float)$result['lat'], (float)$result['lon']];
                            echo "Found coordinates for Cijambe: {$correctCoords[0]}, {$correctCoords[1]}\n";
                            break;
                        }
                    }
                }
            }
            
            sleep(1); // Respect rate limit
        } catch (Exception $e) {
            echo "Error searching for Cijambe coordinates: " . $e->getMessage() . "\n";
        }
    }
    
    if ($correctCoords) {
        echo "Updating coordinates from {$address->latitude}, {$address->longitude} to {$correctCoords[0]}, {$correctCoords[1]}\n";
        
        // Update the coordinates in database
        $updated = DB::table('user_addresses')
            ->where('address_id', $address->address_id)
            ->update([
                'latitude' => $correctCoords[0],
                'longitude' => $correctCoords[1],
                'updated_at' => now()
            ]);
            
        if ($updated) {
            echo "✓ Successfully updated coordinates for address ID {$address->address_id}\n";
        } else {
            echo "✗ Failed to update coordinates for address ID {$address->address_id}\n";
        }
    } else {
        echo "⚠ Could not find correct coordinates for this address\n";
    }
    
    echo "\n" . str_repeat("-", 60) . "\n\n";
}

echo "=== COORDINATE FIX COMPLETE ===\n";

// Verify the fixes
echo "\n=== VERIFICATION ===\n";

$updatedAddresses = DB::table('user_addresses')
    ->whereIn('address_id', $addresses->pluck('address_id'))
    ->get();

foreach ($updatedAddresses as $addr) {
    echo "Address ID {$addr->address_id}: {$addr->village}, {$addr->sub_district} => {$addr->latitude}, {$addr->longitude}\n";
}