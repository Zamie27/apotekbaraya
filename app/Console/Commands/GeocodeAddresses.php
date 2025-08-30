<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserAddress;
use App\Services\GeocodingService;
use App\Services\AddressService;

class GeocodeAddresses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'addresses:geocode 
                            {--force : Force geocoding even if coordinates already exist}
                            {--limit=50 : Limit number of addresses to process}
                            {--batch=10 : Number of addresses to process in each batch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Geocode user addresses to get latitude and longitude coordinates';

    protected $geocodingService;
    protected $addressService;

    /**
     * Create a new command instance.
     */
    public function __construct(GeocodingService $geocodingService, AddressService $addressService)
    {
        parent::__construct();
        $this->geocodingService = $geocodingService;
        $this->addressService = $addressService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $force = $this->option('force');
        $limit = (int) $this->option('limit');
        $batchSize = (int) $this->option('batch');
        
        $this->info('Starting address geocoding process...');
        $this->info("Force mode: " . ($force ? 'ON' : 'OFF'));
        $this->info("Limit: {$limit} addresses");
        $this->info("Batch size: {$batchSize} addresses");
        $this->newLine();
        
        // Build query for addresses to geocode
        $query = UserAddress::query();
        
        if (!$force) {
            // Only process addresses without coordinates
            $query->where(function($q) {
                $q->whereNull('latitude')
                  ->orWhereNull('longitude')
                  ->orWhere('latitude', 0)
                  ->orWhere('longitude', 0);
            });
        }
        
        $totalAddresses = $query->count();
        $this->info("Found {$totalAddresses} addresses to process.");
        
        if ($totalAddresses === 0) {
            $this->info('No addresses need geocoding.');
            return Command::SUCCESS;
        }
        
        // Apply limit
        $addressesToProcess = min($totalAddresses, $limit);
        $this->info("Processing {$addressesToProcess} addresses...");
        $this->newLine();
        
        $addresses = $query->limit($limit)->get();
        $progressBar = $this->output->createProgressBar($addressesToProcess);
        $progressBar->start();
        
        $successCount = 0;
        $failureCount = 0;
        $skippedCount = 0;
        $processedCount = 0;
        
        // Process addresses in batches
        $addressChunks = $addresses->chunk($batchSize);
        
        foreach ($addressChunks as $chunk) {
            $batchAddresses = [];
            
            // Prepare batch data
            foreach ($chunk as $address) {
                $fullAddress = $this->buildFullAddressFromModel($address);
                
                if (empty($fullAddress)) {
                    $this->newLine();
                    $this->warn("Skipping address ID {$address->address_id}: insufficient address data");
                    $skippedCount++;
                    $progressBar->advance();
                    continue;
                }
                
                $batchAddresses[] = [
                    'id' => $address->address_id,
                    'address' => $fullAddress,
                    'model' => $address
                ];
            }
            
            if (empty($batchAddresses)) {
                continue;
            }
            
            // Process each address individually for better geocoding accuracy
            foreach ($batchAddresses as $index => $addressData) {
                $address = $addressData['model'];
                
                // Use geocodeAddress method directly for better accuracy with address variations
                $result = $this->geocodingService->geocodeAddress(
                    $address->village_key ?? '',
                    $address->sub_district_key ?? '',
                    $address->regency_key ?? '',
                    $address->province_key ?? '',
                    $address->postal_code,
                    $address->detailed_address ?? $address->address
                );
            
                // Update database with result
                if ($result && isset($result['lat'], $result['lon'])) {
                    try {
                        $address->update([
                            'latitude' => $result['lat'],
                            'longitude' => $result['lon']
                        ]);
                        $successCount++;
                    } catch (\Exception $e) {
                        $this->newLine();
                        $this->error("Failed to update address ID {$address->address_id}: {$e->getMessage()}");
                        $failureCount++;
                    }
                } else {
                    $failureCount++;
                }
                
                $progressBar->advance();
                $processedCount++;
                
                // Add delay between requests to respect API limits
                if ($index < count($batchAddresses) - 1) {
                    sleep(2);
                }
            }
            
            // Add delay between batches to respect API limits
            if ($processedCount < $addressesToProcess) {
                sleep(2); // 2 second delay between batches
            }
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        // Display results
        $this->info('Geocoding process completed!');
        $this->table(
            ['Status', 'Count'],
            [
                ['Successful', $successCount],
                ['Failed', $failureCount],
                ['Skipped', $skippedCount],
                ['Total Processed', $processedCount]
            ]
        );
        
        if ($failureCount > 0) {
            $this->warn("Some addresses failed to geocode. Check logs for details.");
        }
        
        return Command::SUCCESS;
    }
    
    /**
     * Build full address string from UserAddress model
     *
     * @param UserAddress $address
     * @return string
     */
    private function buildFullAddressFromModel(UserAddress $address): string
    {
        // Try to use new structured fields first
        if ($address->province_key && $address->regency_key && 
            $address->sub_district_key && $address->village_key) {
            
            return $this->addressService->buildFullAddress(
                $address->province_key,
                $address->regency_key,
                $address->sub_district_key,
                $address->village_key,
                $address->postal_code,
                $address->detailed_address ?? $address->address
            );
        }
        
        // Fallback to legacy fields
        $parts = array_filter([
            $address->detailed_address ?? $address->address,
            $address->village,
            $address->sub_district ?? $address->district,
            $address->regency ?? $address->city,
            $address->province,
            $address->postal_code
        ]);
        
        return implode(', ', $parts);
    }
}