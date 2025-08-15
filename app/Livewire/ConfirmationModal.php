<?php

namespace App\Livewire;

use Livewire\Component;

class ConfirmationModal extends Component
{
    public $show = false;
    public $title = '';
    public $message = '';
    public $confirmText = 'Ya, Lanjutkan';
    public $cancelText = 'Batal';
    public $confirmButtonClass = 'btn-error';
    public $actionMethod = '';
    public $actionParams = [];
    
    protected $listeners = [
        'show-confirmation' => 'showModal',
        'hide-confirmation' => 'hideModal'
    ];
    
    /**
     * Show confirmation modal
     * 
     * @param array $data Modal configuration data
     */
    public function showModal($data)
    {
        $this->title = $data['title'] ?? 'Konfirmasi';
        $this->message = $data['message'] ?? 'Apakah Anda yakin?';
        $this->confirmText = $data['confirmText'] ?? 'Ya, Lanjutkan';
        $this->cancelText = $data['cancelText'] ?? 'Batal';
        $this->confirmButtonClass = $data['confirmButtonClass'] ?? 'btn-error';
        $this->actionMethod = $data['actionMethod'] ?? '';
        $this->actionParams = $data['actionParams'] ?? [];
        $this->show = true;
    }
    
    /**
     * Hide confirmation modal
     */
    public function hideModal()
    {
        $this->show = false;
        $this->reset(['title', 'message', 'confirmText', 'cancelText', 'confirmButtonClass', 'actionMethod', 'actionParams']);
    }
    
    /**
     * Confirm action and execute the specified method
     */
    public function confirm()
    {
        if ($this->actionMethod) {
            // Dispatch event to parent component to execute the action
            $this->dispatch('execute-action', $this->actionMethod, $this->actionParams);
        }
        
        $this->hideModal();
    }
    
    /**
     * Cancel action and hide modal
     */
    public function cancel()
    {
        $this->hideModal();
    }
    
    public function render()
    {
        return view('livewire.confirmation-modal');
    }
}