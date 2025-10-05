<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.admin')]
#[Title('Manajemen Produk')]
class ProductManagement extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public ?int $categoryId = null;
    public ?string $active = null; // '1' or '0'
    public $importFile;
    public array $importSummary = [];
    public bool $showImportModal = false;
    public bool $showImportIssuesModal = false;
    public int $perPage = 10;

    public function openImportModal(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->showImportModal = true;
    }

    public function closeImportModal(): void
    {
        $this->showImportModal = false;
        $this->importFile = null;
    }

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryId' => ['except' => null],
        'active' => ['except' => null],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryId(): void
    {
        $this->resetPage();
    }

    public function updatingActive(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function openImportIssuesModal(): void
    {
        if (!empty($this->importSummary['errors'])) {
            $this->showImportIssuesModal = true;
        }
    }

    public function closeImportIssuesModal(): void
    {
        $this->showImportIssuesModal = false;
    }

    /**
     * Import products from a CSV file uploaded via Livewire.
     * Expected headers:
     * name,slug,category_slug,price,discount_percentage,stock,requires_prescription,is_active,weight,kandungan,kemasan,produsen,deskripsi,komposisi,manfaat,dosis,efek_samping,lainnya
     */
    public function importCsv(): void
    {
        $this->validate([
            'importFile' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $created = 0;
        $skipped = 0;
        $errors = [];
        $updated = 0;

        try {
            $path = $this->importFile->getRealPath();
            if (!$path) {
                throw new \RuntimeException('File CSV tidak ditemukan.');
            }

            $handle = fopen($path, 'r');
            if ($handle === false) {
                throw new \RuntimeException('Gagal membuka file CSV.');
            }

            // Read header
            $headers = fgetcsv($handle, 0, ',');
            if (!$headers) {
                throw new \RuntimeException('Header CSV tidak valid atau kosong.');
            }

            // Normalize headers to lowercase
            $headers = array_map(function ($h) { return Str::of($h)->lower()->toString(); }, $headers);

            // Expected minimal headers
            $requiredHeaders = ['name', 'category_slug', 'price'];
            foreach ($requiredHeaders as $h) {
                if (!in_array($h, $headers, true)) {
                    throw new \InvalidArgumentException("Header wajib '{$h}' tidak ditemukan.");
                }
            }

            DB::beginTransaction();

            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                if (count($row) === 1 && trim($row[0]) === '') {
                    continue; // skip empty line
                }

                $data = array_combine($headers, $row);
                if ($data === false) {
                    $errors[] = 'Baris CSV tidak sesuai dengan header.';
                    continue;
                }

                // Basic sanitize and map
                $name = trim($data['name'] ?? '');
                $categorySlug = Str::of($data['category_slug'] ?? '')->lower()->toString();
                $price = (float) ($data['price'] ?? 0);
                $discountPct = isset($data['discount_percentage']) ? (float) $data['discount_percentage'] : 0.0;
                $stock = isset($data['stock']) ? (int) $data['stock'] : 0;
                $requiresPrescription = isset($data['requires_prescription']) ? filter_var($data['requires_prescription'], FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false : false;
                $isActive = isset($data['is_active']) ? filter_var($data['is_active'], FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? true : true;
                $weight = isset($data['weight']) ? (float) $data['weight'] : null;

                $specifications = [
                    'Kandungan' => $data['kandungan'] ?? null,
                    'Kemasan' => $data['kemasan'] ?? null,
                    'Produsen' => $data['produsen'] ?? null,
                    'Komposisi' => $data['komposisi'] ?? null,
                    'Manfaat' => $data['manfaat'] ?? null,
                    'Dosis' => $data['dosis'] ?? null,
                    'Efek Samping' => $data['efek_samping'] ?? null,
                    'Lainnya' => $data['lainnya'] ?? null,
                ];

                $description = $data['deskripsi'] ?? null;

                if ($name === '' || $categorySlug === '' || $price <= 0) {
                    $errors[] = "Baris dilewati: data tidak lengkap untuk produk '{$name}'.";
                    continue;
                }

                $category = Category::where('slug', $categorySlug)->first();
                if (!$category) {
                    $errors[] = "Kategori dengan slug '{$categorySlug}' tidak ditemukan untuk produk '{$name}'.";
                    continue;
                }

                $slug = Str::slug($data['slug'] ?? $name);

                // Compute discount price from percentage
                $discountPrice = 0;
                if ($discountPct > 0 && $discountPct < 100) {
                    $discountPrice = round($price * ($discountPct / 100));
                }

                // Find existing by slug
                $existing = Product::where('slug', $slug)->first();
                if ($existing) {
                    // Check identical fields to skip
                    $identical = (
                        $existing->name === $name &&
                        (int)$existing->category_id === (int)$category->category_id &&
                        (float)$existing->price === (float)$price &&
                        (int)$existing->stock === (int)$stock &&
                        (bool)$existing->requires_prescription === (bool)$requiresPrescription &&
                        (bool)$existing->is_active === (bool)$isActive &&
                        (float)($existing->weight ?? 0) === (float)($weight ?? 0) &&
                        (int)($existing->discount_price ?? 0) === (int)$discountPrice &&
                        (string)($existing->description ?? '') === (string)($description ?? '')
                    );

                    if ($identical) {
                        $skipped++;
                        continue; // duplicate, skip
                    }

                    // If not identical, do not auto-update; record discrepancy
                    $errors[] = "Produk dengan slug '{$slug}' sudah ada namun data berbeda. Lewati untuk review manual.";
                    continue;
                }

                // Ensure unique slug by appending suffix if needed
                $baseSlug = $slug;
                $idx = 1;
                while (Product::where('slug', $slug)->exists()) {
                    $slug = $baseSlug.'-'.$idx;
                    $idx++;
                }

                // Create product
                Product::create([
                    'name' => $name,
                    'slug' => $slug,
                    'description' => $description,
                    'price' => $price,
                    'discount_price' => $discountPrice,
                    'stock' => $stock,
                    'category_id' => $category->category_id,
                    'requires_prescription' => $requiresPrescription,
                    'is_active' => $isActive,
                    'weight' => $weight,
                    'specifications' => $specifications,
                ]);

                $created++;
            }

            fclose($handle);
            DB::commit();

            $this->importSummary = [
                'created' => $created,
                'skipped' => $skipped,
                'errors' => $errors,
                'updated' => $updated,
            ];
            session()->flash('success', "Impor selesai: {$created} dibuat, {$skipped} duplikat dilewati, ".count($errors)." isu untuk ditinjau.");
            $this->resetPage();
            $this->closeImportModal();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->addError('importFile', 'Gagal impor CSV: '.$e->getMessage());
        }
    }

    public function render()
    {
        $query = Product::query()->with('category');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('slug', 'like', "%{$this->search}%");
            });
        }

        if (!is_null($this->categoryId)) {
            $query->where('category_id', $this->categoryId);
        }

        if (!is_null($this->active)) {
            $query->where('is_active', (bool) ((int) $this->active));
        }

        $products = $query->orderByDesc('product_id')->paginate($this->perPage);
        $categories = Category::query()->orderBy('name')->get(['category_id', 'name']);

        return view('livewire.admin.product-management', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}