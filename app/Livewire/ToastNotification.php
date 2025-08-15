<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class ToastNotification extends Component
{
    public $notifications = [];
    
    protected $listeners = ['show-toast' => 'addNotification'];
    
    /**
     * Listen for toast notification events
     */
    #[On('show-toast')]
    public function showToast($type, $message, $duration = 5000)
    {
        $id = uniqid();
        
        $this->notifications[] = [
            'id' => $id,
            'type' => $type, // success, error, warning, info
            'message' => $message,
            'duration' => $duration
        ];
        
        // Auto remove notification after duration
        $this->dispatch('auto-remove-toast', id: $id, duration: $duration);
    }
    
    /**
     * Remove notification by ID
     */
    public function removeNotification($id)
    {
        $this->notifications = array_filter($this->notifications, function($notification) use ($id) {
            return $notification['id'] !== $id;
        });
    }
    
    /**
     * Clear all notifications
     */
    public function clearAll()
    {
        $this->notifications = [];
    }
    
    /**
     * Get alert CSS class based on notification type
     */
    public function getAlertClass($type)
    {
        switch($type) {
            case 'success': return 'alert-success';
            case 'error': return 'alert-error';
            case 'warning': return 'alert-warning';
            case 'info': return 'alert-info';
            default: return 'alert-info';
        }
    }
    
    public function render()
    {
        return view('livewire.toast-notification');
    }
}