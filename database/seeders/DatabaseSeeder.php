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
        // Create roles first
        $adminRole = Role::create(['name' => 'admin']);
        $apotekerRole = Role::create(['name' => 'apoteker']);
        $kurirRole = Role::create(['name' => 'kurir']);
        $pelangganRole = Role::create(['name' => 'pelanggan']);

        // Create test users for each role
        User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@apotekbaraya.com',
            'phone' => '081234567890',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->role_id,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Apoteker User',
            'username' => 'apoteker',
            'email' => 'apoteker@apotekbaraya.com',
            'phone' => '081234567891',
            'password' => Hash::make('password'),
            'role_id' => $apotekerRole->role_id,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Kurir User',
            'username' => 'kurir',
            'email' => 'kurir@apotekbaraya.com',
            'phone' => '081234567892',
            'password' => Hash::make('password'),
            'role_id' => $kurirRole->role_id,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Pelanggan User',
            'username' => 'pelanggan',
            'email' => 'pelanggan@apotekbaraya.com',
            'phone' => '081234567893',
            'password' => Hash::make('password'),
            'role_id' => $pelangganRole->role_id,
            'email_verified_at' => now(),
        ]);
    }
}
