<?php

namespace App\Livewire\Components;

use App\Models\Order;
use Livewire\Component;

class OrderStatusWorkflow extends Component
{
    public Order $order;
    public bool $showDetails = false;
    public string $size = 'normal'; // normal, compact

    /**
     * Mount the component with order data.
     */
    public function mount(Order $order, bool $showDetails = false, string $size = 'normal')
    {
        $this->order = $order;
        $this->showDetails = $showDetails;
        $this->size = $size;
    }

    /**
     * Get workflow steps for the order.
     */
    public function getWorkflowStepsProperty()
    {
        return $this->order->status_workflow;
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.components.order-status-workflow', [
            'workflowSteps' => $this->workflowSteps
        ]);
    }
}