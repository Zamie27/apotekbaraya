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
#[Title('Edit Produk')]
class ProductEdit extends Component
{
    use WithFileUploads;
    public Product $product;
    public array $form = [];
    public $image = null;
    public ?string $specifications_json = null;

    public function mount(int $productId): void
    {
        $this->product = Product::with('category')->findOrFail($productId);
        $spec = $this->product->specifications ?? [];
        $specForm = [
            'kandungan' => $spec['Kandungan'] ?? ($spec['kandungan'] ?? null),
            'kemasan' => $spec['Kemasan'] ?? ($spec['kemasan'] ?? null),
            'produsen' => $spec['Produsen'] ?? ($spec['produsen'] ?? null),
            'komposisi' => $spec['Komposisi'] ?? ($spec['komposisi'] ?? null),
            'manfaat' => $spec['Manfaat'] ?? ($spec['manfaat'] ?? null),
            'dosis' => $spec['Dosis'] ?? ($spec['dosis'] ?? null),
            'efek_samping' => $spec['Efek Samping'] ?? ($spec['efek_samping'] ?? null),
            'lainnya' => $spec['Lainnya'] ?? ($spec['lainnya'] ?? null),
        ];
        $this->form = [
            'name' => $this->product->name,
            'slug' => $this->product->slug,
            'description' => $this->product->description,
            'price' => $this->product->price,
            'stock' => $this->product->stock,
            'category_id' => $this->product->category_id,
            'requires_prescription' => (bool) $this->product->requires_prescription,
            'is_active' => (bool) $this->product->is_active,
            'discount_percentage' => $this->product->discount_percentage,
            'weight' => $this->product->weight,
            // Use lowercase keys for form binding; will normalize on save
            'specifications' => $specForm,
        ];
    }

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
            'form.weight' => 'nullable|numeric|min:0',
            'form.specifications' => 'nullable|array',
            'specifications_json' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    public function save(): void
    {
        // Normalize structured specifications keys or parse JSON if provided via textarea
        if (is_array($this->form['specifications'])) {
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

        $this->product->update($data);

        // Handle image upload: store to product_images and set as primary
        if ($this->image) {
            $path = $this->image->store('products', 'public');

            // If there is an existing primary image, demote it
            $existingPrimary = $this->product->primaryImage;
            if ($existingPrimary) {
                $existingPrimary->is_primary = false;
                $existingPrimary->save();
            }

            // Create new ProductImage as primary
            ProductImage::create([
                'product_id' => $this->product->product_id,
                'image_path' => $path,
                'is_primary' => true,
                'sort_order' => 0,
            ]);
        }

        session()->flash('success', 'Produk berhasil diperbarui.');
        $this->redirectRoute('admin.products.edit', ['productId' => $this->product->product_id]);
    }

    public function render()
    {
        $categories = Category::query()->orderBy('name')->get(['category_id', 'name']);
        return view('livewire.admin.product-edit', [
            'categories' => $categories,
        ]);
    }
}