<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserAddress;

class UserAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first customer user (assuming role_id 4 is customer)
        $customer = User::where('role_id', 4)->first();
        
        if (!$customer) {
            $this->command->info('No customer user found. Creating a test customer...');
            
            $customer = User::create([
                'name' => 'Test Customer',
                'email' => 'customer@test.com',
                'phone' => '081234567890',
                'password' => bcrypt('password'),
                'role_id' => 4,
                'email_verified_at' => now(),
            ]);
        }

        // Create sample addresses for the customer
        $addresses = [
            [
                'user_id' => $customer->user_id,
                'label' => 'rumah',
                'recipient_name' => $customer->name,
                'phone' => $customer->phone ?? '081234567890',
                'village' => 'Desa Sukamaju',
                'sub_district' => 'Subang',
                'regency' => 'Kabupaten Subang',
                'province' => 'Jawa Barat',
                'detailed_address' => 'Jl. Merdeka No. 123, RT 01/RW 02',
                'province_key' => 'jawa_barat',
                'regency_key' => 'subang',
                'sub_district_key' => 'subang',
                'village_key' => 'sukamaju',
                'district' => 'Subang',
                'city' => 'Subang',
                'postal_code' => '41211',
                'notes' => 'Rumah cat hijau, dekat warung Pak Budi',
                'is_default' => true,
            ],
            [
                'user_id' => $customer->user_id,
                'label' => 'kantor',
                'recipient_name' => $customer->name,
                'phone' => $customer->phone ?? '081234567890',
                'village' => 'Desa Kalijati',
                'sub_district' => 'Kalijati',
                'regency' => 'Kabupaten Subang',
                'province' => 'Jawa Barat',
                'detailed_address' => 'Jl. Raya Kalijati No. 456, Gedung Plaza Lt. 3',
                'province_key' => 'jawa_barat',
                'regency_key' => 'subang',
                'sub_district_key' => 'kalijati',
                'village_key' => 'kalijati',
                'district' => 'Kalijati',
                'city' => 'Subang',
                'postal_code' => '41281',
                'notes' => 'Gedung warna biru, masuk dari pintu samping',
                'is_default' => false,
            ],
            [
                'user_id' => $customer->user_id,
                'label' => 'kost',
                'recipient_name' => $customer->name,
                'phone' => $customer->phone ?? '081234567890',
                'village' => 'Desa Cijambe',
                'sub_district' => 'Cijambe',
                'regency' => 'Kabupaten Subang',
                'province' => 'Jawa Barat',
                'detailed_address' => 'Jl. Pendidikan No. 789, Kost Melati Kamar 12',
                'province_key' => 'jawa_barat',
                'regency_key' => 'subang',
                'sub_district_key' => 'cijambe',
                'village_key' => 'cijambe',
                'district' => 'Cijambe',
                'city' => 'Subang',
                'postal_code' => '41285',
                'notes' => 'Kost 3 lantai, kamar di lantai 2',
                'is_default' => false,
            ],
        ];

        foreach ($addresses as $addressData) {
            UserAddress::create($addressData);
        }

        $this->command->info('Sample addresses created successfully for customer: ' . $customer->email);
    }
}
