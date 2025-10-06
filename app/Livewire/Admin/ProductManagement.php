<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\OrderItem;
use App\Models\UserActivityLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
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
    public ?int $confirmDeleteId = null;

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

    // (diubah: method ini dipindah ke bawah dengan logika yang juga memeriksa updates)

    public function closeImportIssuesModal(): void
    {
        $this->showImportIssuesModal = false;
    }

    /**
     * Import products from a CSV file uploaded via Livewire.
     * Expected headers (updated):
     * name,slug,category_slug,price,discount_percentage,stock,requires_prescription,is_active,unit,kemasan,produsen,deskripsi,komposisi,manfaat,dosis,efek_samping,lainnya
     * Notes:
     * - 'unit' required and must be one of: pcs,box,botol,strip,tube,sachet
     * - 'kandungan' deprecated; if present and 'komposisi' empty, it will be used as 'komposisi'.
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
        $updates = [];

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
            $requiredHeaders = ['name', 'category_slug', 'price', 'unit'];
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
                $unit = isset($data['unit']) ? Str::of($data['unit'])->lower()->toString() : null;
                $allowedUnits = ['pcs','box','botol','strip','tube','sachet'];
                if (!$unit || !in_array($unit, $allowedUnits, true)) {
                    $errors[] = "Unit tidak valid untuk produk '{$name}'. Gunakan salah satu: " . implode(',', $allowedUnits);
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
                    $errors[] = "Baris dilewati: data tidak lengkap untuk produk '{$name}'.";
                    continue;
                }

                $category = Category::where('slug', $categorySlug)->first();
                if (!$category) {
                    $errors[] = "Kategori dengan slug '{$categorySlug}' tidak ditemukan untuk produk '{$name}'.";
                    continue;
                }

                $slug = Str::slug($data['slug'] ?? $name);

                // Compute final discount price from percentage (final sale price)
                $computedDiscountPrice = null;
                if (is_numeric($discountPct) && $discountPct > 0 && $discountPct < 100) {
                    $computedDiscountPrice = round($price - ($price * ($discountPct / 100)), 2);
                }

                // Find existing by slug
                $existing = Product::where('slug', $slug)->first();
                if ($existing) {
                    // Compare specifications by keys
                    $existingSpecs = is_array($existing->specifications) ? $existing->specifications : [];
                    $specKeys = ['Kemasan','Produsen','Komposisi','Manfaat','Dosis','Efek Samping','Lainnya'];
                    $sameSpecs = true;
                    foreach ($specKeys as $k) {
                        $existingVal = $existingSpecs[$k] ?? null;
                        $incomingVal = $specifications[$k] ?? null;
                        if ((string)($existingVal ?? '') !== (string)($incomingVal ?? '')) {
                            $sameSpecs = false;
                            break;
                        }
                    }

                    // Check identical fields to skip
                    $identical = (
                        $existing->name === $name &&
                        (int)$existing->category_id === (int)$category->category_id &&
                        (float)$existing->price === (float)$price &&
                        (int)$existing->stock === (int)$stock &&
                        (bool)$existing->requires_prescription === (bool)$requiresPrescription &&
                        (bool)$existing->is_active === (bool)$isActive &&
                        (string)($existing->unit ?? '') === (string)$unit &&
                        (
                            $computedDiscountPrice === null
                                ? ($existing->discount_price === null || (float)$existing->discount_price === (float)$existing->price)
                                : (float)($existing->discount_price ?? 0) === (float)$computedDiscountPrice
                        ) &&
                        (string)($existing->description ?? '') === (string)($description ?? '') &&
                        $sameSpecs
                    );

                    if ($identical) {
                        $skipped++;
                        continue; // duplicate, skip
                    }

                    // Perform update on differing fields
                    $changes = [];
                    if ($existing->name !== $name) { $changes[] = 'name'; }
                    if ((int)$existing->category_id !== (int)$category->category_id) { $changes[] = 'category_id'; }
                    if ((float)$existing->price !== (float)$price) { $changes[] = 'price'; }
                    if ((int)$existing->stock !== (int)$stock) { $changes[] = 'stock'; }
                    if ((bool)$existing->requires_prescription !== (bool)$requiresPrescription) { $changes[] = 'requires_prescription'; }
                    if ((bool)$existing->is_active !== (bool)$isActive) { $changes[] = 'is_active'; }
                    if ((string)($existing->unit ?? '') !== (string)$unit) { $changes[] = 'unit'; }
                    $existingDiscountFinal = $existing->discount_price ? (float)$existing->discount_price : null;
                    if ($computedDiscountPrice !== $existingDiscountFinal) { $changes[] = 'discount_price'; }
                    if ((string)($existing->description ?? '') !== (string)($description ?? '')) { $changes[] = 'description'; }
                    if (!$sameSpecs) { $changes[] = 'specifications'; }

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
                    $updates[] = "Produk '{$existing->name}' (slug: {$slug}) diupdate: " . (empty($changes) ? 'perubahan terdeteksi' : implode(', ', $changes));
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

            fclose($handle);
            DB::commit();

            $this->importSummary = [
                'created' => $created,
                'skipped' => $skipped,
                'errors' => $errors,
                'updated' => $updated,
                'updates' => $updates,
            ];
            session()->flash('success', "Impor selesai: {$created} dibuat, {$updated} diupdate, {$skipped} duplikat dilewati, ".count($errors)." isu untuk ditinjau.");
            $this->resetPage();
            $this->closeImportModal();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->addError('importFile', 'Gagal impor CSV: '.$e->getMessage());
        }
    }

    public function openImportIssuesModal(): void
    {
        if (!empty($this->importSummary['errors']) || !empty($this->importSummary['updates'])) {
            $this->showImportIssuesModal = true;
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

    /**
     * Delete a product safely with validations and cleanup.
     */
    public function deleteProduct($productId): void
    {
        $id = is_numeric($productId) ? (int) $productId : null;
        if (!$id) {
            session()->flash('error', 'ID produk tidak valid.');
            $this->confirmDeleteId = null;
            return;
        }

        $product = Product::with('images')->find($id);
        if (!$product) {
            session()->flash('error', 'Produk tidak ditemukan.');
            $this->confirmDeleteId = null;
            return;
        }

        // Prevent deletion if product is referenced by any orders
        $hasOrderItems = OrderItem::where('product_id', $id)->exists();
        if ($hasOrderItems) {
            session()->flash('error', 'Produk tidak dapat dihapus karena terkait dengan pesanan.');
            $this->confirmDeleteId = null;
            return;
        }

        DB::beginTransaction();
        try {
            // Log activity before actual deletion
            UserActivityLog::logActivity(
                'product_delete',
                'Menghapus produk: ' . $product->name . ' (ID: ' . $product->product_id . ')',
                null,
                [
                    'product' => $product->toArray(),
                    'images' => $product->images->toArray(),
                ],
                null
            );

            // Delete image files and records
            foreach ($product->images as $image) {
                if ($image->image_path) {
                    Storage::disk('public')->delete($image->image_path);
                    // Attempt to delete thumbnail if exists
                    $thumbnailPath = str_replace('products/', 'products/thumbnails/', $image->image_path);
                    Storage::disk('public')->delete($thumbnailPath);
                }
            }
            ProductImage::where('product_id', $id)->delete();

            // Delete product record
            $product->delete();

            DB::commit();
            session()->flash('success', 'Produk berhasil dihapus.');
        } catch (\Throwable $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menghapus produk: ' . $e->getMessage());
        } finally {
            $this->confirmDeleteId = null;
            // Reset pagination ke halaman pertama untuk menghindari state tersangkut
            $this->resetPage();
        }
    }
}