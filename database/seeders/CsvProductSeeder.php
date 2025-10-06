<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Category;

class CsvProductSeeder extends Seeder
{
    /**
     * Path CSV default; dapat dioverride via env PRODUCTS_CSV_PATH.
     */
    private string $defaultCsvPath = 'd:\\Coding\\Herd\\apotekbaraya\\products_20251006_062504.csv';

    /**
     * Jalankan seeder: baca CSV, kemudian create/update produk berdasarkan slug.
     */
    public function run(): void
    {
        $path = env('PRODUCTS_CSV_PATH', $this->defaultCsvPath);

        if (!is_file($path)) {
            $this->command?->warn("File CSV tidak ditemukan di: {$path}. Seeder CSV dilewati.");
            return;
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            $this->command?->error('Gagal membuka file CSV.');
            return;
        }

        $headers = fgetcsv($handle, 0, ',');
        if (!$headers) {
            $this->command?->error('Header CSV tidak valid atau kosong.');
            fclose($handle);
            return;
        }

        // Normalisasi header ke lowercase
        $headers = array_map(function ($h) { return Str::of($h)->lower()->toString(); }, $headers);

        // Header minimal yang dibutuhkan
        $requiredHeaders = ['name', 'category_slug', 'price', 'unit'];
        foreach ($requiredHeaders as $h) {
            if (!in_array($h, $headers, true)) {
                $this->command?->error("Header wajib '{$h}' tidak ditemukan.");
                fclose($handle);
                return;
            }
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = 0;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                if (count($row) === 1 && trim((string)$row[0]) === '') {
                    continue; // skip empty line
                }

                $data = array_combine($headers, $row);
                if ($data === false) {
                    $errors++;
                    continue;
                }

                $name = trim((string)($data['name'] ?? ''));
                $categorySlug = Str::of($data['category_slug'] ?? '')->lower()->toString();
                $price = (float) ($data['price'] ?? 0);
                $discountPct = isset($data['discount_percentage']) ? (float) $data['discount_percentage'] : 0.0;
                $stock = isset($data['stock']) ? (int) $data['stock'] : 0;
                $requiresPrescription = isset($data['requires_prescription']) ? filter_var($data['requires_prescription'], FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false : false;
                $isActive = isset($data['is_active']) ? filter_var($data['is_active'], FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? true : true;
                $unitRaw = isset($data['unit']) ? Str::of($data['unit'])->lower()->toString() : null;

                // Normalisasi unit agar sesuai dengan skema
                $allowedUnits = ['pcs','box','botol','strip','tube','sachet'];
                $unitMap = [
                    'pack' => 'box',
                    'roll' => 'pcs',
                ];
                $unit = $unitRaw;
                if (!$unit || !in_array($unit, $allowedUnits, true)) {
                    $unit = $unitMap[$unitRaw] ?? null;
                }
                if (!$unit || !in_array($unit, $allowedUnits, true)) {
                    $errors++;
                    $this->command?->warn("Unit tidak valid untuk produk '{$name}'.");
                    continue;
                }

                $komposisiVal = $data['komposisi'] ?? null;
                if (!$komposisiVal && isset($data['kandungan'])) {
                    $komposisiVal = $data['kandungan'];
                }

                $specifications = [
                    'Kemasan' => $data['kemasan'] ?? null,
                    'Produsen' => $data['produsen'] ?? null,
                    'Komposisi' => $komposisiVal,
                    'Manfaat' => $data['manfaat'] ?? null,
                    'Dosis' => $data['dosis'] ?? null,
                    'Efek Samping' => $data['efek_samping'] ?? null,
                    'Lainnya' => $data['lainnya'] ?? null,
                ];

                $description = $data['deskripsi'] ?? null;

                if ($name === '' || $categorySlug === '' || $price <= 0) {
                    $errors++;
                    $this->command?->warn("Baris dilewati: data tidak lengkap untuk produk '{$name}'.");
                    continue;
                }

                $category = Category::where('slug', $categorySlug)->first();
                if (!$category) {
                    $errors++;
                    $this->command?->warn("Kategori dengan slug '{$categorySlug}' tidak ditemukan untuk produk '{$name}'.");
                    continue;
                }

                $slug = Str::slug($data['slug'] ?? $name);

                // Hitung discount_price dari persentase (harga setelah diskon)
                $computedDiscountPrice = null;
                if (is_numeric($discountPct) && $discountPct > 0 && $discountPct < 100) {
                    $computedDiscountPrice = round($price - ($price * ($discountPct / 100)), 2);
                }

                $existing = Product::where('slug', $slug)->first();
                if ($existing) {
                    $existing->update([
                        'name' => $name,
                        'category_id' => $category->category_id,
                        'price' => $price,
                        'stock' => $stock,
                        'requires_prescription' => $requiresPrescription,
                        'is_active' => $isActive,
                        'unit' => $unit,
                        'discount_price' => $computedDiscountPrice,
                        'description' => $description,
                        'specifications' => $specifications,
                    ]);
                    $updated++;
                } else {
                    Product::create([
                        'name' => $name,
                        'slug' => $slug,
                        'description' => $description,
                        'price' => $price,
                        'discount_price' => $computedDiscountPrice,
                        'stock' => $stock,
                        'category_id' => $category->category_id,
                        'requires_prescription' => $requiresPrescription,
                        'is_active' => $isActive,
                        'unit' => $unit,
                        'specifications' => $specifications,
                    ]);
                    $created++;
                }
            }

            fclose($handle);
            DB::commit();
            $this->command?->info("Impor CSV selesai: dibuat {$created}, diupdate {$updated}, dilewati {$skipped}, error {$errors}.");
        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);
            $this->command?->error('Gagal impor CSV: ' . $e->getMessage());
        }
    }
}