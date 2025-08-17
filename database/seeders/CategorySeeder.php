<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Obat Keras',
                'slug' => 'obat-keras',
                'description' => 'Obat yang hanya dapat diperoleh dengan resep dokter dan pengawasan apoteker',
                'image' => null,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Obat Bebas Terbatas',
                'slug' => 'obat-bebas-terbatas',
                'description' => 'Obat yang dapat dibeli tanpa resep dokter namun dengan batasan tertentu',
                'image' => null,
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Obat Bebas',
                'slug' => 'obat-bebas',
                'description' => 'Obat yang dapat dibeli bebas tanpa resep dokter',
                'image' => null,
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jamu & Herbal',
                'slug' => 'jamu-herbal',
                'description' => 'Produk jamu tradisional dan obat herbal alami',
                'image' => null,
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kebutuhan Bayi & Ibu',
                'slug' => 'kebutuhan-bayi-ibu',
                'description' => 'Produk kesehatan dan perawatan untuk bayi dan ibu',
                'image' => null,
                'is_active' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Perawatan Wajah',
                'slug' => 'perawatan-wajah',
                'description' => 'Produk perawatan dan kecantikan wajah',
                'image' => null,
                'is_active' => true,
                'sort_order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Perawatan Tubuh',
                'slug' => 'perawatan-tubuh',
                'description' => 'Produk perawatan dan kebersihan tubuh',
                'image' => null,
                'is_active' => true,
                'sort_order' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kosmetik',
                'slug' => 'kosmetik',
                'description' => 'Produk kosmetik dan makeup',
                'image' => null,
                'is_active' => true,
                'sort_order' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Suplemen & Vitamin',
                'slug' => 'suplemen-vitamin',
                'description' => 'Suplemen makanan dan vitamin untuk kesehatan',
                'image' => null,
                'is_active' => true,
                'sort_order' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Alat Kesehatan',
                'slug' => 'alat-kesehatan',
                'description' => 'Peralatan medis dan alat kesehatan',
                'image' => null,
                'is_active' => true,
                'sort_order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Perban & Pertolongan Pertama',
                'slug' => 'perban-pertolongan-pertama',
                'description' => 'Produk perban dan peralatan pertolongan pertama',
                'image' => null,
                'is_active' => true,
                'sort_order' => 11,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert categories with duplicate check
        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['slug' => $category['slug']], // Check by slug
                $category // Insert/update with this data
            );
        }
    }
}