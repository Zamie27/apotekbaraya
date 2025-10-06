<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;

#[Layout('components.layouts.admin')]
#[Title('Tambah Produk')]
class ProductCreate extends Component
{
    use WithFileUploads;

    public array $form = [
        'name' => '',
        'slug' => '',
        'description' => '',
        'price' => null,
        'stock' => null,
        'category_id' => null,
        'requires_prescription' => false,
        'is_active' => true,
        'discount_percentage' => 0,
        'unit' => 'pcs',
        // Structured specifications bound to individual inputs
        'specifications' => [],
    ];

    public $image = null;
    // Optional JSON specifications input for advanced users
    public ?string $specifications_json = null;

    protected function rules(): array
    {
        return [
            'form.name' => 'required|string|min:3',
            'form.slug' => 'nullable|string|alpha_dash',
            'form.description' => 'nullable|string',
            'form.price' => 'required|numeric|min:0',
            'form.stock' => 'required|integer|min:0',
            'form.category_id' => 'required|exists:categories,category_id',
            'form.requires_prescription' => 'boolean',
            'form.is_active' => 'boolean',
            'form.discount_percentage' => 'nullable|numeric|min:0|max:100',
            'form.unit' => 'required|string|in:pcs,box,botol,strip,tube,sachet',
            'form.specifications' => 'nullable|array',
            'specifications_json' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    public function save(): void
    {
        // Normalize structured specifications keys or parse JSON if provided via textarea
        if (is_array($this->form['specifications'])) {
            // Accept both label-case and csv header-case keys, prefer csv header-case if present
            $spec = $this->form['specifications'];
            $normalized = [
                'Kandungan' => $spec['kandungan'] ?? ($spec['Kandungan'] ?? null),
                'Kemasan' => $spec['kemasan'] ?? ($spec['Kemasan'] ?? null),
                'Produsen' => $spec['produsen'] ?? ($spec['Produsen'] ?? null),
                'Komposisi' => $spec['komposisi'] ?? ($spec['Komposisi'] ?? null),
                'Manfaat' => $spec['manfaat'] ?? ($spec['Manfaat'] ?? null),
                'Dosis' => $spec['dosis'] ?? ($spec['Dosis'] ?? null),
                'Efek Samping' => $spec['efek_samping'] ?? ($spec['Efek Samping'] ?? null),
                'Lainnya' => $spec['lainnya'] ?? ($spec['Lainnya'] ?? null),
            ];
            $this->form['specifications'] = $normalized;
        } elseif (is_string($this->specifications_json) && $this->specifications_json !== '') {
            $decoded = json_decode($this->specifications_json, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                $this->addError('specifications_json', 'Spesifikasi harus berupa JSON valid.');
                return;
            }
            $this->form['specifications'] = $decoded;
        } else {
            $this->form['specifications'] = [];
        }

        $this->validate();

        $data = $this->form;

        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = str(\Str::slug($data['name']))->limit(60);
        }

        // Auto set requires_prescription for kategori "Obat Keras" dkk berdasarkan slug
        if (!empty($data['category_id'])) {
            $cat = Category::find($data['category_id']);
            if ($cat) {
                $slug = \Illuminate\Support\Str::of($cat->slug)->lower()->toString();
                $hardMedSlugs = ['obat-keras', 'obat-resep'];
                if (in_array($slug, $hardMedSlugs, true)) {
                    $data['requires_prescription'] = true;
                }
            }
        }

        // Compute discount_price from discount_percentage if provided
        if (isset($data['discount_percentage']) && is_numeric($data['discount_percentage'])) {
            $pct = (float) $data['discount_percentage'];
            if ($pct > 0 && $pct < 100 && isset($data['price'])) {
                $data['discount_price'] = round(((float)$data['price']) - (((float)$data['price']) * ($pct / 100)), 2);
            } else {
                $data['discount_price'] = null;
            }
            // Do not persist discount_percentage
            unset($data['discount_percentage']);
        }

        $product = Product::create($data);

        // Handle image upload: store to product_images and set as primary
        if ($this->image) {
            $path = $this->image->store('products', 'public');
            ProductImage::create([
                'product_id' => $product->product_id,
                'image_path' => $path,
                'is_primary' => true,
                'sort_order' => 0,
            ]);
        }

        session()->flash('success', 'Produk berhasil dibuat.');
        $this->redirectRoute('admin.products.edit', ['productId' => $product->product_id]);
    }

    // Auto-enable requires_prescription when selecting categories like "Obat Keras" or "Obat Resep"
    public function updatedFormCategoryId($value): void
    {
        if (!empty($value)) {
            $cat = Category::find($value);
            if ($cat) {
                $slug = \Illuminate\Support\Str::of($cat->slug)->lower()->toString();
                $hardMedSlugs = ['obat-keras', 'obat-resep'];
                if (in_array($slug, $hardMedSlugs, true)) {
                    $this->form['requires_prescription'] = true;
                }
            }
        }
    }

    public function render()
    {
        $categories = Category::query()->orderBy('name')->get(['category_id', 'name']);
        return view('livewire.admin.product-create', [
            'categories' => $categories,
        ]);
    }
}