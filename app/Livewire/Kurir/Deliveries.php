<?php

namespace App\Livewire\Kurir;

use App\Models\Delivery;
use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.kurir')]
class Deliveries extends Component
{
    use WithPagination;

    // Filter properties
    public $statusFilter = 'all';
    public $search = '';

    // Note: Modal properties and validation rules removed as actions are now handled in Detail Pengiriman page

    // Note: Validation methods and messages removed as they are no longer needed

    /**
     * Reset pagination when search or filter changes.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }



    // Note: showUpdateDelivery method removed as modal functionality is no longer needed

    // Note: Action methods (startDelivery, completeDelivery, confirmPickup, cancelDelivery) 
    // have been removed as actions are now handled in the Detail Pengiriman page only.
    // Status display is now read-only in this management page.

    // Note: updateDelivery, updateOrderStatus, and closeModal methods removed 
    // as all delivery actions are now handled in the Detail Pengiriman page

    /**
     * Render the component.
     */
    public function render()
    {
        $deliveries = Delivery::with([
                'order', 
                'order.user', 
                'order.items', 
                'order.items.product',
                'order.payment',
                'order.payment.paymentMethod'
            ])
            ->where('courier_id', Auth::id())
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->search, function ($query) {
                $query->whereHas('order', function ($q) {
                    $q->where('order_number', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', function ($userQuery) {
                          $userQuery->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.kurir.deliveries', compact('deliveries'));
    }
}
