<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Category;

#[Layout('components.layouts.admin')]
#[Title('Detail Kategori')]
class CategoryDetail extends Component
{
    public Category $category;

    public function mount($categoryId): void
    {
        $id = is_numeric($categoryId) ? (int) $categoryId : null;
        if (!$id) {
            abort(404);
        }

        $this->category = Category::with(['products' => function ($q) {
            $q->orderByDesc('product_id');
        }])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.admin.category-detail');
    }
}