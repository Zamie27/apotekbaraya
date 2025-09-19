<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, create roles if they don't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $apotekerRole = Role::firstOrCreate(['name' => 'apoteker']);
        $kurirRole = Role::firstOrCreate(['name' => 'kurir']);
        $pelangganRole = Role::firstOrCreate(['name' => 'pelanggan']);

        // Create Admin User
        User::create([
            'name' => 'Admin Apotek Baraya',
            'username' => 'admin',
            'email' => 'admin@apotekbaraya.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->role_id,
            'phone' => '081234567890',
            'status' => 'active',
        ]);

        // Create Apoteker User
        User::create([
            'name' => 'Dr. Sarah Apoteker',
            'username' => 'apoteker1',
            'email' => 'apoteker@apotekbaraya.com',
            'password' => Hash::make('password'),
            'role_id' => $apotekerRole->role_id,
            'phone' => '081234567891',
            'status' => 'active',
        ]);

        // Create Courier User
        User::create([
            'name' => 'Budi Kurir',
            'username' => 'kurir1',
            'email' => 'kurir@apotekbaraya.com',
            'password' => Hash::make('password'),
            'role_id' => $kurirRole->role_id,
            'phone' => '081234567892',
            'status' => 'active',
        ]);

        // Create Customer Users
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => "Customer {$i}",
                'username' => "customer{$i}",
                'email' => "customer{$i}@example.com",
                'password' => Hash::make('password'),
                'role_id' => $pelangganRole->role_id,
                'phone' => '08123456789' . $i,
                'status' => 'active',
            ]);
        }

        $this->command->info('Users seeded successfully!');
    }
}
