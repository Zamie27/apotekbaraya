<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Rule;

class NavbarSearch extends Component
{
    #[Rule('required|string|max:100|regex:/^[a-zA-Z0-9\s]+$/')]
    public $query = '';

    // Laravel validation will handle input security
    // Removed preg_replace sanitization to rely on Laravel's built-in security

    /**
     * Perform search and redirect to search page
     */
    public function search()
    {
        // Validate before search
        $this->validate();
        
        if (!empty($this->query)) {
            // Additional sanitization before redirect
            $sanitizedQuery = $this->query;
            $sanitizedQuery = trim($sanitizedQuery);
            
            if (!empty($sanitizedQuery)) {
                return redirect()->to('/search?q=' . urlencode($sanitizedQuery));
            }
        }
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.navbar-search');
    }
}