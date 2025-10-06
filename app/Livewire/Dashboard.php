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
    public int $perPage = 30; // initial batch size
    protected int $increment = 20; // load more size
    public int $seed = 1; // stable random seed per session (public for Livewire hydration)
    public int $promoSeed = 1; // stable random seed for promo row per session (public for Livewire hydration)
    
    /**
     * Filter products by category
     */
    public function filterByCategory($categoryId = null)
    {
        $this->selectedCategory = $categoryId;
        // Reset batch size when category changes
        $this->perPage = 30;
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
    /**
     * Base query for products with optional category filter and stable random order.
     */
    protected function getProductQuery()
    {
        $query = Product::with(['category', 'images'])->active()->available();

        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        // Stable random order per session using seeded RAND()
        return $query->orderByRaw('RAND(' . (int) $this->seed . ')');
    }

    /**
     * Get batched products according to current perPage.
     */
    public function getProducts()
    {
        return $this->getProductQuery()->take($this->perPage)->get();
    }

    /**
     * Get total products count for current filter, to determine hasMore.
     */
    public function getTotalProductsCount(): int
    {
        return (int) $this->getProductQuery()->count();
    }

    /**
     * Load more products in batches.
     */
    public function loadMore(): void
    {
        $total = $this->getTotalProductsCount();
        $this->perPage = min($this->perPage + $this->increment, $total);
    }

    /**
     * Promo products row (random, stable per session)
     */
    public function getPromoProducts()
    {
        return Product::with(['category', 'images'])
            ->active()->available()->onSale()
            ->orderByRaw('RAND(' . (int) $this->promoSeed . ')')
            ->take(10)
            ->get();
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
    
    /**
     * Open WhatsApp consultation
     */
    public function openConsultation()
    {
        // Get store WhatsApp number from settings
        $whatsappNumber = $this->getStoreWhatsAppNumber();
        
        if (!$whatsappNumber) {
            session()->flash('error', 'Nomor WhatsApp toko belum dikonfigurasi. Silakan hubungi admin.');
            return;
        }
        
        // Create consultation message
        $userName = auth()->user()->name;
        $message = "Halo, saya {$userName} ingin berkonsultasi mengenai obat dan produk kesehatan. Mohon bantuannya.";
        $encodedMessage = urlencode($message);
        
        // Create WhatsApp URL
        $whatsappUrl = "https://wa.me/{$whatsappNumber}?text={$encodedMessage}";
        
        // Redirect to WhatsApp
        $this->redirect($whatsappUrl);
    }
    
    /**
     * Get store WhatsApp number from settings
     */
    private function getStoreWhatsAppNumber()
    {
        // Get WhatsApp number from store settings
        $whatsappNumber = \App\Models\StoreSetting::get('store_whatsapp');
        
        if (!$whatsappNumber) {
            return null;
        }
        
        // Clean the number format (remove + and spaces)
        $cleanNumber = preg_replace('/[^0-9]/', '', $whatsappNumber);
        
        // Ensure it starts with 62 (Indonesia country code)
        if (substr($cleanNumber, 0, 2) !== '62') {
            // If starts with 0, replace with 62
            if (substr($cleanNumber, 0, 1) === '0') {
                $cleanNumber = '62' . substr($cleanNumber, 1);
            } else {
                // If doesn't start with 62 or 0, prepend 62
                $cleanNumber = '62' . $cleanNumber;
            }
        }
        
        return $cleanNumber;
    }
    
    /**
     * Initialize seeds for stable random ordering per session.
     */
    public function mount(): void
    {
        $this->seed = (int) (session('dashboard_random_seed') ?? random_int(1, 1000000));
        session(['dashboard_random_seed' => $this->seed]);

        $this->promoSeed = (int) (session('dashboard_promo_seed') ?? random_int(1, 1000000));
        session(['dashboard_promo_seed' => $this->promoSeed]);
    }

    public function render()
    {
        $products = $this->getProducts();
        $totalCount = $this->getTotalProductsCount();
        $hasMore = $this->perPage < $totalCount;

        return view('livewire.dashboard', [
            'categories' => $this->getCategories(),
            'products' => $products,
            'isAuthenticated' => $this->isAuthenticated(),
            'currentUser' => $this->getCurrentUser(),
            'promoProducts' => $this->getPromoProducts(),
            'hasMore' => $hasMore,
            'totalCount' => $totalCount,
            'displayedCount' => count($products),
        ]);
    }
}
