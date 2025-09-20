<?php

namespace App\Services;

use App\Jobs\SendEmailNotification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class EmailNotificationQueueService
{
    /**
     * Dispatch user created notification to queue.
     *
     * @param User $user
     * @param array $additionalData
     * @return void
     */
    public function dispatchUserCreatedNotification(User $user, array $additionalData = []): void
    {
        try {
            SendEmailNotification::dispatch($user, 'user_created', $additionalData);
            
            Log::info('User created notification dispatched to queue', [
                'user_id' => $user->id,
                'user_email' => $user->email
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to dispatch user created notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Dispatch user updated notification to queue.
     *
     * @param User $user
     * @param array $additionalData
     * @return void
     */
    public function dispatchUserUpdatedNotification(User $user, array $additionalData = []): void
    {
        try {
            SendEmailNotification::dispatch($user, 'user_updated', $additionalData);
            
            Log::info('User updated notification dispatched to queue', [
                'user_id' => $user->id,
                'user_email' => $user->email
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to dispatch user updated notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Dispatch password reset notification to queue.
     *
     * @param User $user
     * @param array $additionalData
     * @return void
     */
    public function dispatchPasswordResetNotification(User $user, array $additionalData = []): void
    {
        try {
            SendEmailNotification::dispatch($user, 'password_reset', $additionalData);
            
            Log::info('Password reset notification dispatched to queue', [
                'user_id' => $user->id,
                'user_email' => $user->email
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to dispatch password reset notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Dispatch order status notification to queue.
     *
     * @param User $user
     * @param array $additionalData
     * @return void
     */
    public function dispatchOrderStatusNotification(User $user, array $additionalData = []): void
    {
        try {
            SendEmailNotification::dispatch($user, 'order_status_updated', $additionalData);
            
            Log::info('Order status notification dispatched to queue', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'order_id' => $additionalData['order_id'] ?? 'unknown'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to dispatch order status notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Dispatch bulk notifications to queue.
     *
     * @param array $users
     * @param string $notificationType
     * @param array $additionalData
     * @return void
     */
    public function dispatchBulkNotifications(array $users, string $notificationType, array $additionalData = []): void
    {
        foreach ($users as $user) {
            if (!$user instanceof User) {
                continue;
            }

            try {
                SendEmailNotification::dispatch($user, $notificationType, $additionalData);
            } catch (\Exception $e) {
                Log::error('Failed to dispatch bulk notification', [
                    'user_id' => $user->id,
                    'notification_type' => $notificationType,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info('Bulk notifications dispatched to queue', [
            'user_count' => count($users),
            'notification_type' => $notificationType
        ]);
    }

    /**
     * Get queue statistics.
     *
     * @return array
     */
    public function getQueueStatistics(): array
    {
        // This would require additional queue monitoring setup
        // For now, return basic info
        return [
            'queue_connection' => config('queue.default'),
            'jobs_table' => config('queue.connections.database.table', 'jobs'),
            'status' => 'active'
        ];
    }
}