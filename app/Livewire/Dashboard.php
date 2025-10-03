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
