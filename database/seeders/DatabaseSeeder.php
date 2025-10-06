<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles first (use firstOrCreate to avoid duplicates)
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $apotekerRole = Role::firstOrCreate(['name' => 'apoteker']);
        $kurirRole = Role::firstOrCreate(['name' => 'kurir']);
        $pelangganRole = Role::firstOrCreate(['name' => 'pelanggan']);

        // Create test users for each role (use firstOrCreate to avoid duplicates)
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin User',
                'email' => 'admin@apotekbaraya.com',
                'phone' => '081234567890',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->role_id,
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['username' => 'apoteker'],
            [
                'name' => 'Apoteker User',
                'email' => 'apoteker@apotekbaraya.com',
                'phone' => '081234567891',
                'password' => Hash::make('password'),
                'role_id' => $apotekerRole->role_id,
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['username' => 'kurir'],
            [
                'name' => 'Kurir User',
                'email' => 'kurir@apotekbaraya.com',
                'phone' => '081234567892',
                'password' => Hash::make('password'),
                'role_id' => $kurirRole->role_id,
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['username' => 'pelanggan'],
            [
                'name' => 'Pelanggan User',
                'email' => 'pelanggan@apotekbaraya.com',
                'phone' => '081234567893',
                'password' => Hash::make('password'),
                'role_id' => $pelangganRole->role_id,
                'email_verified_at' => now(),
            ]
        );

        // Seed categories, products, and store settings
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            AdditionalProductSeeder::class,
            StoreSettingsSeeder::class,
            CsvProductSeeder::class,
        ]);
    }
}
