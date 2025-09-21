<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Prescription;
use App\Models\User;

class PrescriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get pelanggan users
        $pelangganUsers = User::whereHas('role', function ($query) {
            $query->where('name', 'pelanggan');
        })->get();

        if ($pelangganUsers->isEmpty()) {
            $this->command->info('No pelanggan users found. Please run UserSeeder first.');
            return;
        }

        // Get apoteker user for confirmed_by field
        $apotekerUser = User::whereHas('role', function ($query) {
            $query->where('name', 'apoteker');
        })->first();

        if (!$apotekerUser) {
            $this->command->info('No apoteker user found. Please run UserSeeder first.');
            return;
        }

        // Sample prescription data
        $prescriptions = [
            [
                'user_id' => $pelangganUsers->first()->user_id,
                'prescription_number' => 'RX-' . date('Ymd') . '-001',
                'doctor_name' => 'Dr. Ahmad Wijaya, Sp.PD',
                'patient_name' => 'Budi Santoso',
                'file' => 'prescriptions/sample-prescription-1.jpg',
                'prescription_image' => 'prescriptions/sample-prescription-1.jpg',
                'notes' => 'Pasien mengalami hipertensi ringan, mohon diberikan obat sesuai resep dokter.',
                'status' => 'pending',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'user_id' => $pelangganUsers->first()->user_id,
                'prescription_number' => 'RX-' . date('Ymd') . '-002',
                'doctor_name' => 'Dr. Siti Nurhaliza, Sp.A',
                'patient_name' => 'Anak Budi',
                'file' => 'prescriptions/sample-prescription-2.jpg',
                'prescription_image' => 'prescriptions/sample-prescription-2.jpg',
                'notes' => 'Anak demam tinggi, butuh antibiotik sesuai resep.',
                'status' => 'confirmed',
                'confirmed_at' => now()->subDay(),
                'confirmed_by' => $apotekerUser->user_id,
                'confirmation_notes' => 'Resep sudah dikonfirmasi, obat tersedia.',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDay(),
            ],
            [
                'user_id' => $pelangganUsers->count() > 1 ? $pelangganUsers->skip(1)->first()->user_id : $pelangganUsers->first()->user_id,
                'prescription_number' => 'RX-' . date('Ymd') . '-003',
                'doctor_name' => 'Dr. Bambang Sutrisno, Sp.JP',
                'patient_name' => 'Ibu Sari',
                'file' => 'prescriptions/sample-prescription-3.jpg',
                'prescription_image' => 'prescriptions/sample-prescription-3.jpg',
                'notes' => 'Pasien jantung, mohon perhatian khusus untuk dosis obat.',
                'status' => 'rejected',
                'confirmed_at' => now()->subHours(6),
                'confirmed_by' => $apotekerUser->user_id,
                'confirmation_notes' => 'Resep tidak jelas, mohon upload ulang dengan kualitas yang lebih baik.',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subHours(6),
            ],
            [
                'user_id' => $pelangganUsers->first()->user_id,
                'prescription_number' => 'RX-' . date('Ymd') . '-004',
                'doctor_name' => 'Dr. Maya Sari, Sp.OG',
                'patient_name' => 'Ibu Dewi',
                'file' => 'prescriptions/sample-prescription-4.jpg',
                'prescription_image' => 'prescriptions/sample-prescription-4.jpg',
                'notes' => 'Ibu hamil 7 bulan, vitamin dan suplemen kehamilan.',
                'status' => 'processed',
                'confirmed_at' => now()->subHours(2),
                'confirmed_by' => $apotekerUser->user_id,
                'confirmation_notes' => 'Pesanan sudah diproses dan siap untuk diambil.',
                'order_id' => null, // Will be set if order exists
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(2),
            ],
        ];

        foreach ($prescriptions as $prescription) {
            Prescription::create($prescription);
        }

        $this->command->info('Prescription seeder completed successfully!');
        $this->command->info('Created ' . count($prescriptions) . ' sample prescriptions.');
    }
}
