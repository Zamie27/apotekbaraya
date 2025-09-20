<?php

namespace App\Services;

use App\Models\EmailNotification;
use App\Models\User;
use App\Jobs\SendEmailNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailNotificationService
{
    /**
     * Create and queue an email notification.
     */
    public function createNotification(
        User $user,
        string $eventType,
        string $subject,
        string $message,
        array $data = [],
        string $recipientEmail = null
    ): EmailNotification {
        return EmailNotification::create([
            'user_id' => $user->id,
            'event_type' => $eventType,
            'recipient_email' => $recipientEmail ?? $user->email,
            'subject' => $subject,
            'message' => $message,
            'data' => $data,
            'status' => 'pending',
        ]);
    }

    /**
     * Send notification for user creation.
     */
    public function sendUserCreatedNotification(User $user, User $createdBy): void
    {
        $subject = 'Akun Baru Telah Dibuat - Apotek Baraya';
        $message = "Akun baru telah dibuat untuk {$user->name} ({$user->email}) oleh {$createdBy->name}.";
        
        $data = [
            'new_user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->name ?? 'Unknown',
            ],
            'created_by' => [
                'id' => $createdBy->id,
                'name' => $createdBy->name,
                'email' => $createdBy->email,
            ],
            'created_at' => now()->format('d/m/Y H:i:s'),
        ];

        // Send to admin emails
        $adminEmails = User::whereHas('role', function($query) {
            $query->where('name', 'admin');
        })->pluck('email')->toArray();

        foreach ($adminEmails as $adminEmail) {
            $this->createNotification($user, 'user_created', $subject, $message, $data, $adminEmail);
        }
    }

    /**
     * Send notification for user update.
     */
    public function sendUserUpdatedNotification(User $user, User $updatedBy, array $changes = []): void
    {
        $subject = 'Data Pengguna Telah Diperbarui - Apotek Baraya';
        $message = "Data pengguna {$user->name} ({$user->email}) telah diperbarui oleh {$updatedBy->name}.";
        
        $data = [
            'updated_user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->name ?? 'Unknown',
            ],
            'updated_by' => [
                'id' => $updatedBy->id,
                'name' => $updatedBy->name,
                'email' => $updatedBy->email,
            ],
            'changes' => $changes,
            'updated_at' => now()->format('d/m/Y H:i:s'),
        ];

        // Send to admin emails and the user themselves
        $recipients = User::whereHas('role', function($query) {
            $query->where('name', 'admin');
        })->pluck('email')->toArray();
        
        $recipients[] = $user->email;
        $recipients = array_unique($recipients);

        foreach ($recipients as $recipientEmail) {
            $this->createNotification($user, 'user_updated', $subject, $message, $data, $recipientEmail);
        }
    }

    /**
     * Send notification for user deletion.
     */
    public function sendUserDeletedNotification(User $user, User $deletedBy): void
    {
        $subject = 'Pengguna Telah Dihapus - Apotek Baraya';
        $message = "Pengguna {$user->name} ({$user->email}) telah dihapus oleh {$deletedBy->name}.";
        
        $data = [
            'deleted_user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->name ?? 'Unknown',
            ],
            'deleted_by' => [
                'id' => $deletedBy->id,
                'name' => $deletedBy->name,
                'email' => $deletedBy->email,
            ],
            'deleted_at' => now()->format('d/m/Y H:i:s'),
        ];

        // Send to admin emails
        $adminEmails = User::whereHas('role', function($query) {
            $query->where('name', 'admin');
        })->pluck('email')->toArray();

        foreach ($adminEmails as $adminEmail) {
            $this->createNotification($user, 'user_deleted', $subject, $message, $data, $adminEmail);
        }
    }

    /**
     * Send notification for failed login attempts.
     */
    public function sendLoginAttemptNotification(string $email, string $ipAddress): void
    {
        $user = User::where('email', $email)->first();
        if (!$user) return;

        $subject = 'Percobaan Login Gagal - Apotek Baraya';
        $message = "Terdapat percobaan login gagal untuk akun {$email} dari IP {$ipAddress}.";
        
        $data = [
            'email' => $email,
            'ip_address' => $ipAddress,
            'attempted_at' => now()->format('d/m/Y H:i:s'),
            'user_agent' => request()->userAgent(),
        ];

        $this->createNotification($user, 'login_attempt', $subject, $message, $data);
    }

    /**
     * Process pending notifications and send emails.
     */
    public function processPendingNotifications(int $limit = 50): void
    {
        $notifications = EmailNotification::pending()
            ->where('retry_count', '<', 3)
            ->orderBy('created_at')
            ->limit($limit)
            ->get();

        foreach ($notifications as $notification) {
            try {
                $this->sendEmail($notification);
                $notification->markAsSent();
                
                Log::info('Email notification sent successfully', [
                    'notification_id' => $notification->id,
                    'event_type' => $notification->event_type,
                    'recipient' => $notification->recipient_email,
                ]);
            } catch (\Exception $e) {
                $notification->markAsFailed($e->getMessage());
                
                Log::error('Failed to send email notification', [
                    'notification_id' => $notification->id,
                    'event_type' => $notification->event_type,
                    'recipient' => $notification->recipient_email,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Send email using Laravel Mail.
     */
    private function sendEmail(EmailNotification $notification): void
    {
        Mail::raw($notification->message, function ($message) use ($notification) {
            $message->to($notification->recipient_email)
                    ->subject($notification->subject)
                    ->from(config('mail.from.address'), config('mail.from.name'));
        });
    }

    /**
     * Get notification statistics.
     */
    public function getStatistics(): array
    {
        return [
            'total' => EmailNotification::count(),
            'pending' => EmailNotification::pending()->count(),
            'sent' => EmailNotification::sent()->count(),
            'failed' => EmailNotification::failed()->count(),
            'today_sent' => EmailNotification::sent()
                ->whereDate('sent_at', today())
                ->count(),
            'this_week_sent' => EmailNotification::sent()
                ->whereBetween('sent_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
        ];
    }

    /**
     * Dispatch user created notification to queue.
     */
    public function queueUserCreatedNotification(User $user, array $additionalData = []): void
    {
        SendEmailNotification::dispatch($user, 'user_created', $additionalData)->onQueue('emails');
        
        Log::info('User created notification dispatched to queue', [
            'user_id' => $user->id,
            'additional_data' => $additionalData
        ]);
    }

    /**
     * Dispatch user updated notification to queue.
     */
    public function queueUserUpdatedNotification(User $user, array $additionalData = []): void
    {
        SendEmailNotification::dispatch($user, 'user_updated', $additionalData)->onQueue('emails');
        
        Log::info('User updated notification dispatched to queue', [
            'user_id' => $user->id,
            'additional_data' => $additionalData
        ]);
    }

    /**
     * Dispatch user deleted notification to queue.
     */
    public function queueUserDeletedNotification(User $user, array $additionalData = []): void
    {
        SendEmailNotification::dispatch($user, 'user_deleted', $additionalData)->onQueue('emails');
        
        Log::info('User deleted notification dispatched to queue', [
            'user_id' => $user->id,
            'additional_data' => $additionalData
        ]);
    }

    /**
     * Dispatch login attempt notification to queue.
     */
    public function queueLoginAttemptNotification(User $user, array $additionalData = []): void
    {
        SendEmailNotification::dispatch($user, 'login_attempt', $additionalData)->onQueue('emails');
        
        Log::info('Login attempt notification dispatched to queue', [
            'user_id' => $user->id,
            'additional_data' => $additionalData
        ]);
    }
}