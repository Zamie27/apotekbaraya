<?php

namespace App\Livewire\Admin;

use App\Models\EmailNotification;
use App\Services\EmailNotificationService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.admin')]
#[Title('Pengaturan Notifikasi Email')]
class EmailNotificationSettings extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $typeFilter = '';
    public $dateFilter = '';
    
    // Statistics
    public $statistics = [];

    /**
     * Mount component
     */
    public function mount()
    {
        $this->loadStatistics();
    }

    /**
     * Load notification statistics
     */
    public function loadStatistics()
    {
        $emailService = app(EmailNotificationService::class);
        $this->statistics = $emailService->getStatistics();
    }

    /**
     * Process pending notifications manually
     */
    public function processPendingNotifications()
    {
        try {
            $emailService = app(EmailNotificationService::class);
            $emailService->processPendingNotifications(10); // Process 10 at a time
            
            $this->loadStatistics();
            session()->flash('success', 'Notifikasi pending berhasil diproses!');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat memproses notifikasi: ' . $e->getMessage());
        }
    }

    /**
     * Retry failed notification
     */
    public function retryNotification($notificationId)
    {
        try {
            $notification = EmailNotification::findOrFail($notificationId);
            
            if ($notification->status === 'failed') {
                $notification->update([
                    'status' => 'pending',
                    'error_message' => null,
                ]);
                
                session()->flash('success', 'Notifikasi berhasil dijadwalkan ulang untuk dikirim.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Delete notification
     */
    public function deleteNotification($notificationId)
    {
        try {
            $notification = EmailNotification::findOrFail($notificationId);
            $notification->delete();
            
            $this->loadStatistics();
            session()->flash('success', 'Notifikasi berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reset filters
     */
    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->typeFilter = '';
        $this->dateFilter = '';
        $this->resetPage();
    }

    /**
     * Updated search
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    /**
     * Updated status filter
     */
    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    /**
     * Updated type filter
     */
    public function updatedTypeFilter()
    {
        $this->resetPage();
    }

    /**
     * Updated date filter
     */
    public function updatedDateFilter()
    {
        $this->resetPage();
    }

    /**
     * Render component
     */
    public function render()
    {
        $query = EmailNotification::with('user')
            ->orderBy('created_at', 'desc');

        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('recipient_email', 'like', '%' . $this->search . '%')
                  ->orWhere('subject', 'like', '%' . $this->search . '%')
                  ->orWhere('type', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function($userQuery) {
                      $userQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Apply status filter
        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        // Apply type filter
        if (!empty($this->typeFilter)) {
            $query->where('type', $this->typeFilter);
        }

        // Apply date filter
        if (!empty($this->dateFilter)) {
            $query->whereDate('created_at', $this->dateFilter);
        }

        $notifications = $query->paginate(15);

        // Get unique types for filter
        $types = EmailNotification::distinct()
            ->pluck('type')
            ->filter()
            ->sort()
            ->values();

        return view('livewire.admin.email-notification-settings', [
            'notifications' => $notifications,
            'types' => $types,
        ]);
    }
}
