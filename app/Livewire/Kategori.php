<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\WithPagination;

#[Layout('components.layouts.user')]
class Kategori extends Component
{
    use WithPagination;

    public $categoryId;
    public $category;

    public $perPage = 10;

    #[Validate('nullable|string|max:100|regex:/^[a-zA-Z0-9\s]*$/')]
    public $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'page' => ['except' => 1],
        'perPage' => ['except' => 10]
    ];

    public function mount($slug = null)
    {
        if ($slug) {
            // Handle special "promo" category
            if ($slug === 'promo') {
                $this->category = (object) [
                    'category_id' => 'promo',
                    'name' => 'Promo',
                    'slug' => 'promo',
                    'description' => 'Produk dengan harga diskon khusus'
                ];
                $this->categoryId = 'promo';
            } else {
                // Try to find by slug first, fallback to ID for backward compatibility
                $this->category = Category::where('is_active', true)
                    ->where(function($query) use ($slug) {
                        $query->where('slug', $slug)
                              ->orWhere('category_id', $slug);
                    })
                    ->firstOrFail();
                $this->categoryId = $this->category->category_id;
            }
        } else {
            $this->category = null;
        }
    }

    public function updatedSearch()
    {
        // Laravel validation will handle input security
        $this->resetPage();
    }



    /**
     * Clear search results and reset all search-related properties
     */
    public function clearSearch()
    {
        $this->search = '';
        $this->resetPage();
        
        // Force re-render to ensure UI updates
        $this->dispatch('search-cleared');
    }

    /**
     * Update items per page and reset pagination
     */
    public function updatedPerPage()
    {
        $this->resetPage();
    }

    /**
     * Set items per page
     */
    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;
        $this->resetPage();
    }



    public function getProductsProperty()
    {
        $query = Product::with(['category'])
            ->where('is_active', true);

        // Filter berdasarkan kategori jika ada
        if ($this->categoryId) {
            if ($this->categoryId === 'promo') {
                // Filter produk yang sedang diskon (memiliki discount_price)
                $query->onSale();
            } else {
                $query->where('category_id', $this->categoryId);
            }
        }

        // Filter berdasarkan pencarian
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Default ordering by name, but for promo category, order by discount percentage desc
        if ($this->categoryId === 'promo') {
            $query->orderByRaw('((price - discount_price) / price) DESC');
        } else {
            $query->orderBy('name', 'asc');
        }

        return $query->paginate($this->perPage);
    }

    public function getCategoriesProperty()
    {
        return Category::where('is_active', true)
            ->withCount('products')
            ->orderBy('name')
            ->get();
    }

    public function isAuthenticated()
    {
        return auth()->check();
    }

    public function getCurrentUser()
    {
        return auth()->user();
    }



    public function render()
    {
        return view('livewire.kategori', [
            'products' => $this->products,
            'categories' => $this->categories,
            'isAuthenticated' => $this->isAuthenticated(),
            'currentUser' => $this->getCurrentUser(),
        ]);
    }
}
