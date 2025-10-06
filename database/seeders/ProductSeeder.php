<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Obat Keras (category_id: 1)
            [
                'name' => 'Amoxicillin 500mg',
                'slug' => 'amoxicillin-500mg',
                'description' => 'Antibiotik untuk mengobati infeksi bakteri. Harus dikonsumsi sesuai resep dokter.',
                'short_description' => 'Antibiotik untuk infeksi bakteri',
                'price' => 25000.00,
                'discount_price' => null,
                'stock' => 'available',
                'sku' => 'AMX-500-001',
                'category_id' => 1,
                'requires_prescription' => true,
                'is_active' => true,
                'unit' => 'strip',
                'specifications' => json_encode([
                    'kandungan' => 'Amoxicillin 500mg',
                    'kemasan' => '10 kapsul per strip',
                    'produsen' => 'Kimia Farma'
                ]),
                'weight' => 0.05,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Captopril 25mg',
                'slug' => 'captopril-25mg',
                'description' => 'Obat untuk menurunkan tekanan darah tinggi (hipertensi). Harus dikonsumsi dengan resep dokter.',
                'short_description' => 'Obat hipertensi',
                'price' => 15000.00,
                'discount_price' => null,
                'stock' => 'available',
                'sku' => 'CAP-25-001',
                'category_id' => 1,
                'requires_prescription' => true,
                'is_active' => true,
                'unit' => 'strip',
                'specifications' => json_encode([
                    'kandungan' => 'Captopril 25mg',
                    'kemasan' => '10 tablet per strip',
                    'produsen' => 'Dexa Medica'
                ]),
                'weight' => 0.03,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Obat Bebas Terbatas (category_id: 2)
            [
                'name' => 'Paracetamol 500mg',
                'slug' => 'paracetamol-500mg',
                'description' => 'Obat penurun demam dan pereda nyeri. Dapat dibeli tanpa resep dengan batasan dosis.',
                'short_description' => 'Penurun demam dan pereda nyeri',
                'price' => 8000.00,
                'discount_price' => 7000.00,
                'stock' => 'available',
                'sku' => 'PCM-500-001',
                'category_id' => 2,
                'requires_prescription' => false,
                'is_active' => true,
                'unit' => 'strip',
                'specifications' => json_encode([
                    'kandungan' => 'Paracetamol 500mg',
                    'kemasan' => '10 tablet per strip',
                    'produsen' => 'Sanbe Farma'
                ]),
                'weight' => 0.04,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ibuprofen 400mg',
                'slug' => 'ibuprofen-400mg',
                'description' => 'Anti-inflamasi non-steroid untuk meredakan nyeri dan peradangan.',
                'short_description' => 'Pereda nyeri dan anti-inflamasi',
                'price' => 12000.00,
                'discount_price' => null,
                'stock' => 'available',
                'sku' => 'IBU-400-001',
                'category_id' => 2,
                'requires_prescription' => false,
                'is_active' => true,
                'unit' => 'strip',
                'specifications' => json_encode([
                    'kandungan' => 'Ibuprofen 400mg',
                    'kemasan' => '10 tablet per strip',
                    'produsen' => 'Tempo Scan Pacific'
                ]),
                'weight' => 0.05,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Obat Bebas (category_id: 3)
            [
                'name' => 'Antangin JRG',
                'slug' => 'antangin-jrg',
                'description' => 'Obat herbal untuk mengatasi masuk angin, mual, dan perut kembung.',
                'short_description' => 'Obat masuk angin herbal',
                'price' => 3500.00,
                'discount_price' => null,
                'stock' => 'available',
                'sku' => 'ANT-JRG-001',
                'category_id' => 3,
                'requires_prescription' => false,
                'is_active' => true,
                'unit' => 'sachet',
                'specifications' => json_encode([
                    'kandungan' => 'Jahe, Royal Jelly, Ginseng',
                    'kemasan' => '1 sachet 15ml',
                    'produsen' => 'Deltomed'
                ]),
                'weight' => 0.02,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tolak Angin',
                'slug' => 'tolak-angin',
                'description' => 'Obat tradisional untuk mencegah dan mengatasi masuk angin.',
                'short_description' => 'Obat tradisional masuk angin',
                'price' => 2500.00,
                'discount_price' => null,
                'stock' => 'available',
                'sku' => 'TOL-ANG-001',
                'category_id' => 3,
                'requires_prescription' => false,
                'is_active' => true,
                'unit' => 'sachet',
                'specifications' => json_encode([
                    'kandungan' => 'Ekstrak herbal tradisional',
                    'kemasan' => '1 sachet 15ml',
                    'produsen' => 'Sido Muncul'
                ]),
                'weight' => 0.02,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Jamu & Herbal (category_id: 4)
            [
                'name' => 'Jamu Kunyit Asam',
                'slug' => 'jamu-kunyit-asam',
                'description' => 'Jamu tradisional untuk kesehatan pencernaan dan detoksifikasi tubuh.',
                'short_description' => 'Jamu tradisional kunyit asam',
                'price' => 5000.00,
                'discount_price' => null,
                'stock' => 'available',
                'sku' => 'JAM-KUN-001',
                'category_id' => 4,
                'requires_prescription' => false,
                'is_active' => true,
                'unit' => 'sachet',
                'specifications' => json_encode([
                    'kandungan' => 'Kunyit, Asam Jawa, Gula Aren',
                    'kemasan' => '1 sachet 20g',
                    'produsen' => 'Nyonya Meneer'
                ]),
                'weight' => 0.025,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Kebutuhan Bayi & Ibu (category_id: 5)
            [
                'name' => 'Susu Formula Bayi 0-6 Bulan',
                'slug' => 'susu-formula-bayi-0-6-bulan',
                'description' => 'Susu formula untuk bayi usia 0-6 bulan dengan nutrisi lengkap.',
                'short_description' => 'Susu formula bayi 0-6 bulan',
                'price' => 85000.00,
                'discount_price' => 80000.00,
                'stock' => 'available',
                'sku' => 'SUS-BAY-001',
                'category_id' => 5,
                'requires_prescription' => false,
                'is_active' => true,
                'unit' => 'box',
                'specifications' => json_encode([
                    'kandungan' => 'Protein, DHA, ARA, Prebiotik',
                    'kemasan' => '400g per box',
                    'produsen' => 'Nestle'
                ]),
                'weight' => 0.45,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Minyak Telon Bayi',
                'slug' => 'minyak-telon-bayi',
                'description' => 'Minyak telon untuk menghangatkan tubuh bayi dan mencegah masuk angin.',
                'short_description' => 'Minyak telon bayi',
                'price' => 15000.00,
                'discount_price' => null,
                'stock' => 'available',
                'sku' => 'MIN-TEL-001',
                'category_id' => 5,
                'requires_prescription' => false,
                'is_active' => true,
                'unit' => 'botol',
                'specifications' => json_encode([
                    'kandungan' => 'Minyak Kelapa, Minyak Kayu Putih, Minyak Adas',
                    'kemasan' => '60ml per botol',
                    'produsen' => 'Cap Lang'
                ]),
                'weight' => 0.08,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Suplemen & Vitamin (category_id: 9)
            [
                'name' => 'Vitamin C 1000mg',
                'slug' => 'vitamin-c-1000mg',
                'description' => 'Suplemen vitamin C untuk meningkatkan daya tahan tubuh.',
                'short_description' => 'Suplemen vitamin C',
                'price' => 45000.00,
                'discount_price' => 40000.00,
                'stock' => 'available',
                'sku' => 'VIT-C-001',
                'category_id' => 9,
                'requires_prescription' => false,
                'is_active' => true,
                'unit' => 'botol',
                'specifications' => json_encode([
                    'kandungan' => 'Vitamin C 1000mg',
                    'kemasan' => '30 tablet per botol',
                    'produsen' => 'Blackmores'
                ]),
                'weight' => 0.12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Multivitamin Dewasa',
                'slug' => 'multivitamin-dewasa',
                'description' => 'Suplemen multivitamin lengkap untuk dewasa.',
                'short_description' => 'Multivitamin dewasa',
                'price' => 65000.00,
                'discount_price' => null,
                'stock' => 'available',
                'sku' => 'MUL-VIT-001',
                'category_id' => 9,
                'requires_prescription' => false,
                'is_active' => true,
                'unit' => 'botol',
                'specifications' => json_encode([
                    'kandungan' => 'Vitamin A, B, C, D, E, Mineral',
                    'kemasan' => '30 kapsul per botol',
                    'produsen' => 'Nature Plus'
                ]),
                'weight' => 0.15,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Alat Kesehatan (category_id: 10)
            [
                'name' => 'Termometer Digital',
                'slug' => 'termometer-digital',
                'description' => 'Termometer digital untuk mengukur suhu tubuh dengan akurat.',
                'short_description' => 'Termometer digital',
                'price' => 35000.00,
                'discount_price' => null,
                'stock' => 'available',
                'sku' => 'TER-DIG-001',
                'category_id' => 10,
                'requires_prescription' => false,
                'is_active' => true,
                'unit' => 'pcs',
                'specifications' => json_encode([
                    'fitur' => 'LCD Display, Waterproof, Auto Shut-off',
                    'akurasi' => '±0.1°C',
                    'produsen' => 'Omron'
                ]),
                'weight' => 0.05,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tensimeter Digital',
                'slug' => 'tensimeter-digital',
                'description' => 'Alat pengukur tekanan darah digital untuk monitoring kesehatan.',
                'short_description' => 'Tensimeter digital',
                'price' => 250000.00,
                'discount_price' => 230000.00,
                'stock' => 'available',
                'sku' => 'TEN-DIG-001',
                'category_id' => 10,
                'requires_prescription' => false,
                'is_active' => true,
                'unit' => 'pcs',
                'specifications' => json_encode([
                    'fitur' => 'Memory Storage, Large Display, Irregular Heartbeat Detection',
                    'ukuran_manset' => '22-42 cm',
                    'produsen' => 'Omron'
                ]),
                'weight' => 0.8,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Perban & Pertolongan Pertama (category_id: 11)
            [
                'name' => 'Perban Elastis 10cm',
                'slug' => 'perban-elastis-10cm',
                'description' => 'Perban elastis untuk membalut luka atau cedera.',
                'short_description' => 'Perban elastis 10cm',
                'price' => 12000.00,
                'discount_price' => null,
                'stock' => 'available',
                'sku' => 'PER-ELA-001',
                'category_id' => 11,
                'requires_prescription' => false,
                'is_active' => true,
                'unit' => 'pcs',
                'specifications' => json_encode([
                    'ukuran' => '10cm x 4.5m',
                    'bahan' => 'Cotton Elastic',
                    'produsen' => 'Onemed'
                ]),
                'weight' => 0.1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Plester Luka Waterproof',
                'slug' => 'plester-luka-waterproof',
                'description' => 'Plester luka tahan air untuk melindungi luka dari air dan kotoran.',
                'short_description' => 'Plester luka waterproof',
                'price' => 8000.00,
                'discount_price' => null,
                'stock' => 'available',
                'sku' => 'PLE-WAT-001',
                'category_id' => 11,
                'requires_prescription' => false,
                'is_active' => true,
                'unit' => 'box',
                'specifications' => json_encode([
                    'ukuran' => 'Assorted sizes',
                    'isi' => '20 pieces per box',
                    'produsen' => 'Hansaplast'
                ]),
                'weight' => 0.05,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        // Normalize legacy fields to match current schema
        foreach ($products as &$product) {
            // Remove legacy 'weight' field (column no longer exists)
            if (array_key_exists('weight', $product)) {
                unset($product['weight']);
            }

            // Convert legacy stock string to integer quantity
            if (!is_int($product['stock'])) {
                $product['stock'] = 100; // default stock quantity
            }
        }
        unset($product);

        // Insert products with duplicate check
        foreach ($products as $productData) {
            DB::table('products')->updateOrInsert(
                ['sku' => $productData['sku']], // Check by SKU
                $productData // Insert/update with this data
            );
        }
    }
}