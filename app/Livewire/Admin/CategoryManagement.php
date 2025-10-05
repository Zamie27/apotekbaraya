<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Category;

#[Layout('components.layouts.admin')]
#[Title('Manajemen Kategori')]
class CategoryManagement extends Component
{
    use WithPagination;

    public string $search = '';
    public ?string $active = null; // '1' or '0'
    public ?int $confirmDeleteId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'active' => ['except' => null],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingActive(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Category::query();

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('slug', 'like', "%{$this->search}%");
            });
        }

        if (!is_null($this->active)) {
            $query->where('is_active', (bool) ((int) $this->active));
        }

        $categories = $query->orderBy('sort_order')->orderBy('name')->paginate(10);

        return view('livewire.admin.category-management', [
            'categories' => $categories,
        ]);
    }

    /**
     * Delete category safely. Prevent delete if has products.
     */
    public function deleteCategory(?int $categoryId = null): void
    {
        $id = $categoryId ?? $this->confirmDeleteId;
        if (!$id) {
            // No id to delete; just close modal safely
            $this->confirmDeleteId = null;
            return;
        }

        $category = Category::findOrFail($id);

        if ($category->products()->exists()) {
            session()->flash('error', 'Tidak bisa menghapus: kategori memiliki produk terkait.');
            $this->confirmDeleteId = null;
            return;
        }

        $category->delete();
        session()->flash('success', 'Kategori berhasil dihapus.');
        $this->confirmDeleteId = null;
        $this->resetPage();
    }
}