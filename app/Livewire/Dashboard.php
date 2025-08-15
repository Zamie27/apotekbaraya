<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.user')]
class Dashboard extends Component
{
    public $selectedCategory = null;
    
    /**
     * Filter products by category
     */
    public function filterByCategory($categoryId = null)
    {
        $this->selectedCategory = $categoryId;
    }
    

    
    /**
     * Get all active categories
     */
    public function getCategories()
    {
        return Category::active()->ordered()->get();
    }
    
    /**
     * Get products based on selected category
     */
    public function getProducts()
    {
        $query = Product::with(['category', 'images'])->active()->available();
        
        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }
        
        return $query->latest()->take(20)->get();
    }
    
    /**
     * Check if user is authenticated
     */
    public function isAuthenticated()
    {
        return auth()->check();
    }
    
    /**
     * Get current user if authenticated
     */
    public function getCurrentUser()
    {
        return auth()->user();
    }
    
    public function render()
    {
        return view('livewire.dashboard', [
            'categories' => $this->getCategories(),
            'products' => $this->getProducts(),
            'isAuthenticated' => $this->isAuthenticated(),
            'currentUser' => $this->getCurrentUser()
        ]);
    }
}
