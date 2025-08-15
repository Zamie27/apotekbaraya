<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Rule;

class NavbarSearch extends Component
{
    #[Rule('required|string|max:100|regex:/^[a-zA-Z0-9\s]+$/')]
    public $query = '';

    /**
     * Sanitize and validate search query
     */
    public function updatedQuery($value)
    {
        // Remove any non-alphanumeric characters except spaces
        $this->query = preg_replace('/[^a-zA-Z0-9\s]/', '', $value);
        
        // Trim whitespace and limit length
        $this->query = trim(substr($this->query, 0, 100));
    }

    /**
     * Perform search and redirect to search page
     */
    public function search()
    {
        // Validate before search
        $this->validate();
        
        if (!empty($this->query)) {
            // Additional sanitization before redirect
            $sanitizedQuery = preg_replace('/[^a-zA-Z0-9\s]/', '', $this->query);
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