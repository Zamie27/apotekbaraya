<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

/**
 * Search Component
 * 
 * Handles product search functionality with real-time results using Livewire
 */
#[Layout('components.layouts.user')]
class Search extends Component
{
    #[Validate('nullable|string|max:100|regex:/^[a-zA-Z0-9\s]+$/')]
    public $query = '';
    public $results = [];
    public $showResults = false;
    public $isSearching = false;

    /**
     * Initialize component with query parameter from URL
     */
    public function mount()
    {
        $this->query = request('q', '');
        
        if (!empty($this->query)) {
            $this->searchProducts();
            $this->showResults = true;
        }
    }

    /**
     * Handle search query updates
     */
    public function updatedQuery($value)
    {
        // Laravel validation will handle input security
        $this->isSearching = true;
        
        if (strlen($this->query) >= 2) {
            $this->searchProducts();
            $this->showResults = true;
        } else {
            $this->results = [];
            $this->showResults = false;
        }
        
        $this->isSearching = false;
    }

    /**
     * Search products based on name, description, or category
     */
    public function searchProducts()
    {
        if (empty($this->query)) {
            $this->results = [];
            return;
        }

        $this->results = Product::with(['category', 'images'])
            ->active()
            ->available()
            ->where(function($query) {
                $query->where('name', 'LIKE', '%' . $this->query . '%')
                      ->orWhere('description', 'LIKE', '%' . $this->query . '%')
                      ->orWhereHas('category', function($q) {
                          $q->where('name', 'LIKE', '%' . $this->query . '%');
                      });
            })
            ->orderByRaw("CASE 
                WHEN name LIKE '" . $this->query . "%' THEN 1
                WHEN name LIKE '%" . $this->query . "%' THEN 2
                ELSE 3
            END")
            ->orderBy('name')
            ->limit(20)
            ->get();
    }

    /**
     * Clear search results and reset all search-related properties
     */
    public function clearSearch()
    {
        $this->query = '';
        $this->results = [];
        $this->showResults = false;
        $this->isSearching = false;
        
        // Force re-render to ensure UI updates
        $this->dispatch('search-cleared');
    }

    /**
     * Navigate to product detail page
     */
    public function viewProduct($productId)
    {
        return redirect()->route('produk.deskripsi', ['id' => $productId]);
    }



    /**
     * Render the search component
     */
    public function render()
    {
        // Get popular products when no search is active
        $popularProducts = [];
        if (empty($this->query)) {
            $popularProducts = Product::with(['category', 'images'])
                ->active()
                ->available()
                ->latest()
                ->limit(10)
                ->get();
        }

        return view('livewire.search', [
            'popularProducts' => $popularProducts
        ]);
    }
}