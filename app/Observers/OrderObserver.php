<?php

namespace App\Observers;

use App\Models\Order;
use App\Mail\OrderNotificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * Observer for Order model to handle automatic email notifications
 * 
 * This observer listens to order status changes and sends appropriate
 * email notifications to customers automatically
 */
class OrderObserver
{
    /**
     * Handle the Order "updated" event.
     * 
     * This method is triggered whenever an order is updated
     * and checks if the status has changed to send appropriate notifications
     *
     * @param Order $order
     * @return void
     */
    public function updated(Order $order): void
    {
        // Check if status was changed
        if ($order->isDirty('status')) {
            $oldStatus = $order->getOriginal('status');
            $newStatus = $order->status;
            
            Log::info("Order status changed", [
                'order_id' => $order->order_id,
                'order_number' => $order->order_number,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'customer_email' => $order->user->email ?? 'N/A'
            ]);
            
            // Send email notification for specific status changes
            $this->sendStatusNotification($order, $newStatus);
        }
        
        // Check for other important field changes
        $this->checkOtherFieldChanges($order);
    }

    /**
     * Send email notification based on order status
     *
     * @param Order $order
     * @param string $status
     * @return void
     */
    private function sendStatusNotification(Order $order, string $status): void
    {
        // Only send notifications if customer email exists
        if (!$order->user || !$order->user->email) {
            Log::warning("Cannot send order notification: customer email not found", [
                'order_id' => $order->order_id,
                'order_number' => $order->order_number
            ]);
            return;
        }

        // Map status to notification type
        $notificationType = $this->mapStatusToNotificationType($status);
        
        if (!$notificationType) {
            return; // No notification needed for this status
        }

        try {
            // Send email notification
            Mail::to($order->user->email)
                ->send(new OrderNotificationMail($order, $notificationType));
            
            Log::info("Order notification email sent", [
                'order_id' => $order->order_id,
                'order_number' => $order->order_number,
                'notification_type' => $notificationType,
                'customer_email' => $order->user->email
            ]);
            
        } catch (\Exception $e) {
            Log::error("Failed to send order notification email", [
                'order_id' => $order->order_id,
                'order_number' => $order->order_number,
                'notification_type' => $notificationType,
                'customer_email' => $order->user->email,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Map order status to notification type
     *
     * @param string $status
     * @return string|null
     */
    private function mapStatusToNotificationType(string $status): ?string
    {
        return match ($status) {
            // Payment confirmed - order is paid
            'waiting_confirmation' => 'paid',
            
            // Order confirmed by admin/apoteker
            'confirmed' => 'confirmed',
            
            // Order is ready for delivery
            'ready_to_ship' => 'ready_to_ship',
            
            // Order is ready for pickup
            'ready_for_pickup' => 'ready_for_pickup',
            
            // Order is being shipped
            'shipped' => 'shipped',
            
            // Order has been picked up
            'picked_up' => 'picked_up',
            
            // Order has been delivered
            'delivered' => 'delivered',
            
            // Order is completed
            'completed' => 'completed',
            
            // Order is cancelled
            'cancelled' => 'cancelled',
            
            // No notification for these statuses
            'waiting_payment', 'processing' => null,
            
            // Default case
            default => null,
        };
    }

    /**
     * Check for other important field changes that might need notifications
     *
     * @param Order $order
     * @return void
     */
    private function checkOtherFieldChanges(Order $order): void
    {
        // Check if payment was confirmed
        if ($order->isDirty('confirmed_at') && $order->confirmed_at) {
            Log::info("Order payment confirmed", [
                'order_id' => $order->order_id,
                'order_number' => $order->order_number,
                'confirmed_at' => $order->confirmed_at
            ]);
        }
        
        // Check if order was cancelled
        if ($order->isDirty('cancelled_at') && $order->cancelled_at) {
            Log::info("Order cancelled", [
                'order_id' => $order->order_id,
                'order_number' => $order->order_number,
                'cancelled_at' => $order->cancelled_at,
                'cancellation_reason' => $order->cancellation_reason ?? 'No reason provided'
            ]);
        }
        
        // Check if order was completed
        if ($order->isDirty('completed_at') && $order->completed_at) {
            Log::info("Order completed", [
                'order_id' => $order->order_id,
                'order_number' => $order->order_number,
                'completed_at' => $order->completed_at
            ]);
        }
        
        // Check if ready to ship timestamp was set
        if ($order->isDirty('ready_to_ship_at') && $order->ready_to_ship_at) {
            Log::info("Order ready to ship", [
                'order_id' => $order->order_id,
                'order_number' => $order->order_number,
                'ready_to_ship_at' => $order->ready_to_ship_at
            ]);
        }
    }

    /**
     * Handle the Order "created" event.
     *
     * @param Order $order
     * @return void
     */
    public function created(Order $order): void
    {
        Log::info("New order created", [
            'order_id' => $order->order_id,
            'order_number' => $order->order_number,
            'customer_email' => $order->user->email ?? 'N/A',
            'total_price' => $order->total_price,
            'status' => $order->status
        ]);
    }

    /**
     * Handle the Order "deleted" event.
     *
     * @param Order $order
     * @return void
     */
    public function deleted(Order $order): void
    {
        Log::info("Order deleted", [
            'order_id' => $order->order_id,
            'order_number' => $order->order_number
        ]);
    }
}