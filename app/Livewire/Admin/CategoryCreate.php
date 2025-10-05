<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Str;
use App\Models\Category;

#[Layout('components.layouts.admin')]
#[Title('Tambah Kategori')]
class CategoryCreate extends Component
{
    public array $form = [
        'name' => '',
        'slug' => '',
        'is_active' => true,
        'sort_order' => null,
    ];

    protected function rules(): array
    {
        return [
            'form.name' => 'required|string|min:3',
            'form.slug' => 'nullable|string|alpha_dash|unique:categories,slug',
            'form.is_active' => 'boolean',
            'form.sort_order' => 'nullable|integer|min:0',
        ];
    }

    public function save(): void
    {
        $data = $this->validate()['form'];

        // Generate slug from name if empty
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Fallback sort_order to 0 if null
        if (is_null($data['sort_order'])) {
            $data['sort_order'] = 0;
        }

        $category = new Category();
        $category->name = $data['name'];
        $category->slug = $data['slug'];
        $category->is_active = (bool) $data['is_active'];
        $category->sort_order = (int) $data['sort_order'];
        $category->save();

        session()->flash('success', 'Kategori berhasil ditambahkan.');
        redirect()->route('admin.categories');
    }

    public function render()
    {
        return view('livewire.admin.category-create');
    }
}