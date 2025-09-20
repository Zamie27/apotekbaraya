<?php

namespace App\Livewire\Admin;

use App\Models\UserActivityLog;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

#[Layout('components.layouts.admin')]
#[Title('Laporan Aktivitas Pengguna')]
class UserActivityReports extends Component
{
    use WithPagination;

    // Filter properties
    public $dateFrom = '';
    public $dateTo = '';
    public $userId = '';
    public $action = '';
    public $search = '';
    
    // Pagination
    public $perPage = 10;

    /**
     * Initialize component with default date range (last 30 days)
     */
    public function mount()
    {
        $this->dateTo = Carbon::now()->format('Y-m-d');
        $this->dateFrom = Carbon::now()->subDays(30)->format('Y-m-d');
    }

    /**
     * Reset pagination when filters change
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingUserId()
    {
        $this->resetPage();
    }

    public function updatingAction()
    {
        $this->resetPage();
    }

    /**
     * Reset all filters to default
     */
    public function resetFilters()
    {
        $this->dateFrom = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
        $this->userId = '';
        $this->action = '';
        $this->search = '';
        $this->resetPage();
    }

    /**
     * Export activity logs to CSV
     */
    public function exportToCsv()
    {
        $activities = $this->getFilteredActivities(false);
        
        $filename = 'user_activity_report_' . Carbon::now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($activities) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'ID',
                'User',
                'Email', 
                'Action',
                'Description',
                'IP Address',
                'User Agent',
                'Created At'
            ]);

            // CSV Data
            foreach ($activities as $activity) {
                fputcsv($file, [
                    $activity->id,
                    $activity->user ? $activity->user->name : 'Unknown User',
                    $activity->user ? $activity->user->email : 'N/A',
                    $activity->action,
                    $activity->description,
                    $activity->ip_address,
                    $activity->user_agent,
                    $activity->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Get filtered activities based on current filters
     */
    private function getFilteredActivities($paginate = true)
    {
        $query = UserActivityLog::with('user')
            ->when($this->dateFrom, function($q) {
                $q->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function($q) {
                $q->whereDate('created_at', '<=', $this->dateTo);
            })
            ->when($this->userId, function($q) {
                $q->where('user_id', $this->userId);
            })
            ->when($this->action, function($q) {
                $q->where('action', $this->action);
            })
            ->when($this->search, function($q) {
                $q->where(function($subQuery) {
                    $subQuery->where('description', 'like', '%' . $this->search . '%')
                             ->orWhere('ip_address', 'like', '%' . $this->search . '%')
                             ->orWhereHas('user', function($userQuery) {
                                 $userQuery->where('name', 'like', '%' . $this->search . '%')
                                          ->orWhere('email', 'like', '%' . $this->search . '%');
                             });
                });
            })
            ->orderBy('created_at', 'desc');

        return $paginate ? $query->paginate($this->perPage) : $query->get();
    }

    /**
     * Get users property for filter dropdown
     */
    public function getUsersProperty()
    {
        return User::select('user_id', 'name', 'email')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get unique actions for filter dropdown
     */
    public function getActionsProperty()
    {
        return UserActivityLog::select('action')
                              ->distinct()
                              ->orderBy('action')
                              ->pluck('action');
    }

    /**
     * Render the component
     */
    public function render()
    {
        $activities = $this->getFilteredActivities();
        
        return view('livewire.admin.user-activity-reports', [
            'activities' => $activities,
            'users' => $this->users,
            'actions' => $this->actions,
        ]);
    }
}
