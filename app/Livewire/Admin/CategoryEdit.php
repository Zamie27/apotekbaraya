<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Str;
use App\Models\Category;

#[Layout('components.layouts.admin')]
#[Title('Edit Kategori')]
class CategoryEdit extends Component
{
    public Category $category;

    public array $form = [
        'name' => '',
        'slug' => '',
        'is_active' => true,
        'sort_order' => null,
    ];

    public function mount(int $categoryId): void
    {
        $this->category = Category::findOrFail($categoryId);
        $this->form = [
            'name' => $this->category->name,
            'slug' => $this->category->slug,
            'is_active' => (bool) $this->category->is_active,
            'sort_order' => $this->category->sort_order,
        ];
    }

    protected function rules(): array
    {
        return [
            'form.name' => 'required|string|min:3',
            'form.slug' => 'nullable|string|alpha_dash|unique:categories,slug,' . $this->category->category_id . ',category_id',
            'form.is_active' => 'boolean',
            'form.sort_order' => 'nullable|integer|min:0',
        ];
    }

    public function save(): void
    {
        $data = $this->validate()['form'];

        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if (is_null($data['sort_order'])) {
            $data['sort_order'] = 0;
        }

        $this->category->name = $data['name'];
        $this->category->slug = $data['slug'];
        $this->category->is_active = (bool) $data['is_active'];
        $this->category->sort_order = (int) $data['sort_order'];
        $this->category->save();

        session()->flash('success', 'Kategori berhasil diperbarui.');
        redirect()->route('admin.categories');
    }

    public function render()
    {
        return view('livewire.admin.category-edit');
    }
}