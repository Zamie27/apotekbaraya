<?php

namespace App\Services;

use App\Models\Order;
use App\Mail\OrderNotificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * Service class for handling order email notifications
 * 
 * This service provides methods to manually send order notifications
 * and manage email notification logic
 */
class OrderNotificationService
{
    /**
     * Send order notification email
     *
     * @param Order $order
     * @param string $notificationType
     * @param array $customData
     * @return bool
     */
    public function sendNotification(Order $order, string $notificationType, array $customData = []): bool
    {
        try {
            // Validate customer email
            if (!$order->user || !$order->user->email) {
                Log::warning("Cannot send order notification: customer email not found", [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number
                ]);
                return false;
            }

            // Send email
            Mail::to($order->user->email)
                ->send(new OrderNotificationMail($order, $notificationType, $customData));
            
            Log::info("Order notification email sent manually", [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'notification_type' => $notificationType,
                'customer_email' => $order->user->email
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error("Failed to send order notification email", [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'notification_type' => $notificationType,
                'customer_email' => $order->user->email ?? 'N/A',
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Send payment confirmation notification
     *
     * @param Order $order
     * @return bool
     */
    public function sendPaymentConfirmation(Order $order): bool
    {
        return $this->sendNotification($order, 'paid');
    }

    /**
     * Send order confirmation notification
     *
     * @param Order $order
     * @return bool
     */
    public function sendOrderConfirmation(Order $order): bool
    {
        return $this->sendNotification($order, 'confirmed');
    }

    /**
     * Send processing notification
     *
     * @param Order $order
     * @return bool
     */
    public function sendProcessingNotification(Order $order): bool
    {
        return $this->sendNotification($order, 'processing');
    }

    /**
     * Send ready to ship notification
     *
     * @param Order $order
     * @return bool
     */
    public function sendReadyToShipNotification(Order $order): bool
    {
        return $this->sendNotification($order, 'ready_to_ship');
    }

    /**
     * Send ready for pickup notification
     *
     * @param Order $order
     * @return bool
     */
    public function sendReadyForPickupNotification(Order $order): bool
    {
        return $this->sendNotification($order, 'ready_for_pickup');
    }

    /**
     * Send shipped notification
     *
     * @param Order $order
     * @return bool
     */
    public function sendShippedNotification(Order $order): bool
    {
        return $this->sendNotification($order, 'shipped');
    }

    /**
     * Send picked up notification
     *
     * @param Order $order
     * @return bool
     */
    public function sendPickedUpNotification(Order $order): bool
    {
        return $this->sendNotification($order, 'picked_up');
    }

    /**
     * Send delivered notification
     *
     * @param Order $order
     * @return bool
     */
    public function sendDeliveredNotification(Order $order): bool
    {
        return $this->sendNotification($order, 'delivered');
    }

    /**
     * Send completed notification
     *
     * @param Order $order
     * @return bool
     */
    public function sendCompletedNotification(Order $order): bool
    {
        return $this->sendNotification($order, 'completed');
    }

    /**
     * Send cancelled notification
     *
     * @param Order $order
     * @param string|null $reason
     * @return bool
     */
    public function sendCancelledNotification(Order $order, ?string $reason = null): bool
    {
        $customData = [];
        
        if ($reason) {
            $customData['additionalInfo'] = "Alasan pembatalan: {$reason}";
        }
        
        return $this->sendNotification($order, 'cancelled', $customData);
    }

    /**
     * Send custom notification
     *
     * @param Order $order
     * @param string $title
     * @param string $message
     * @param string|null $actionUrl
     * @param string|null $actionText
     * @param string|null $additionalInfo
     * @return bool
     */
    public function sendCustomNotification(
        Order $order,
        string $title,
        string $message,
        ?string $actionUrl = null,
        ?string $actionText = null,
        ?string $additionalInfo = null
    ): bool {
        $customData = [
            'title' => $title,
            'message' => $message,
            'statusLabel' => $order->status_label,
            'actionUrl' => $actionUrl,
            'actionText' => $actionText,
            'additionalInfo' => $additionalInfo,
        ];
        
        return $this->sendNotification($order, 'custom', $customData);
    }

    /**
     * Get available notification types
     *
     * @return array
     */
    public function getAvailableNotificationTypes(): array
    {
        return [
            'paid' => 'Pembayaran Diterima',
            'confirmed' => 'Pesanan Dikonfirmasi',
            'processing' => 'Sedang Diproses',
            'ready_to_ship' => 'Siap Dikirim',
            'ready_for_pickup' => 'Siap Diambil',
            'shipped' => 'Sedang Dikirim',
            'picked_up' => 'Telah Diambil',
            'delivered' => 'Telah Dikirim',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];
    }

    /**
     * Check if notification should be sent for status change
     *
     * @param string $oldStatus
     * @param string $newStatus
     * @return bool
     */
    public function shouldSendNotification(string $oldStatus, string $newStatus): bool
    {
        // Define status changes that should trigger notifications
        $notifiableChanges = [
            'waiting_payment' => ['waiting_confirmation'],
            'waiting_confirmation' => ['confirmed'],
            'confirmed' => ['processing', 'ready_to_ship', 'ready_for_pickup'],
            'processing' => ['ready_to_ship', 'ready_for_pickup'],
            'ready_to_ship' => ['shipped'],
            'ready_for_pickup' => ['picked_up'],
            'shipped' => ['delivered'],
            'delivered' => ['completed'],
            'picked_up' => ['completed'],
        ];

        // Check if this status change should trigger a notification
        return isset($notifiableChanges[$oldStatus]) && 
               in_array($newStatus, $notifiableChanges[$oldStatus]);
    }
}