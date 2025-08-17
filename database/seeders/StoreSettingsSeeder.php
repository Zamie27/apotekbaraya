<?php

namespace Database\Seeders;

use App\Models\StoreSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultSettings = [
            [
                'key' => 'store_name',
                'value' => 'Apotek Baraya',
                'type' => 'string'
            ],
            [
                'key' => 'store_address',
                'value' => 'Jl. Raya Apotek No. 123, Subang, Jawa Barat 41211',
                'type' => 'string'
            ],
            [
                'key' => 'store_latitude',
                'value' => '-6.318318',
                'type' => 'string'
            ],
            [
                'key' => 'store_longitude',
                'value' => '107.694088',
                'type' => 'string'
            ],
            [
                'key' => 'shipping_rate_per_km',
                'value' => '2000',
                'type' => 'number'
            ],
            [
                'key' => 'max_delivery_distance',
                'value' => '15',
                'type' => 'number'
            ],
            [
                'key' => 'free_shipping_minimum',
                'value' => '100000',
                'type' => 'number'
            ],
            // Google Maps API key removed - now using OpenStreetMap Nominatim
            // [
            //     'key' => 'google_maps_api_key',
            //     'value' => '',
            //     'type' => 'string'
            // ]
        ];

        foreach ($defaultSettings as $setting) {
            StoreSetting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type']
                ]
            );
        }

        $this->command->info('Store settings seeded successfully!');
    }
}