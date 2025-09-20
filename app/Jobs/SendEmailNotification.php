<?php

namespace App\Jobs;

use App\Mail\UserNotificationMail;
use App\Models\EmailNotification;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The maximum number of seconds the job can run.
     */
    public int $timeout = 120;

    /**
     * User instance for email notification
     */
    public User $user;

    /**
     * Email notification type
     */
    public string $notificationType;

    /**
     * Additional data for email template
     */
    public array $additionalData;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param string $notificationType
     * @param array $additionalData
     */
    public function __construct(User $user, string $notificationType, array $additionalData = [])
    {
        $this->user = $user;
        $this->notificationType = $notificationType;
        $this->additionalData = $additionalData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Generate email content based on notification type
            $emailData = $this->generateEmailContent();
            
            // Get recipients based on notification type
            $recipients = $this->getRecipients();
            
            // Send emails to all recipients using Mailable
            foreach ($recipients as $recipientEmail) {
                $mail = new UserNotificationMail(
                    $this->notificationType,
                    $emailData['data'],
                    $emailData['subject']
                );
                
                Mail::to($recipientEmail)->send($mail);
                
                Log::info('Email sent successfully', [
                    'user_id' => $this->user->id,
                    'notification_type' => $this->notificationType,
                    'recipient' => $recipientEmail
                ]);
            }
            
            Log::info('Email notification job completed successfully', [
                'user_id' => $this->user->id,
                'notification_type' => $this->notificationType,
                'recipients_count' => count($recipients)
            ]);
            
        } catch (Exception $e) {
            Log::error('Email notification job failed', [
                'user_id' => $this->user->id,
                'notification_type' => $this->notificationType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    /**
     * Generate email content based on notification type.
     */
    private function generateEmailContent(): array
    {
        $data = array_merge([
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'role' => $this->user->role->name ?? 'Unknown',
            ]
        ], $this->additionalData);

        switch ($this->notificationType) {
            case 'user_created':
                return [
                    'subject' => 'Akun Baru Telah Dibuat - Apotek Baraya',
                    'message' => "Akun baru telah dibuat untuk {$this->user->name} ({$this->user->email}).",
                    'data' => array_merge($data, [
                        'new_user' => $data['user'],
                        'created_at' => now()->format('d/m/Y H:i:s')
                    ])
                ];

            case 'user_updated':
                return [
                    'subject' => 'Data Pengguna Telah Diperbarui - Apotek Baraya',
                    'message' => "Data pengguna {$this->user->name} ({$this->user->email}) telah diperbarui.",
                    'data' => array_merge($data, [
                        'updated_user' => $data['user'],
                        'updated_at' => now()->format('d/m/Y H:i:s')
                    ])
                ];

            case 'user_deleted':
                return [
                    'subject' => 'Pengguna Telah Dihapus - Apotek Baraya',
                    'message' => "Pengguna {$this->user->name} ({$this->user->email}) telah dihapus.",
                    'data' => array_merge($data, [
                        'deleted_user' => $data['user'],
                        'deleted_at' => now()->format('d/m/Y H:i:s')
                    ])
                ];

            case 'login_attempt':
                return [
                    'subject' => 'Percobaan Login Gagal - Apotek Baraya',
                    'message' => "Terdapat percobaan login gagal untuk akun {$this->user->email}.",
                    'data' => array_merge($data, [
                        'email' => $this->user->email,
                        'attempted_at' => now()->format('d/m/Y H:i:s')
                    ])
                ];

            default:
                return [
                    'subject' => 'Notifikasi Sistem - Apotek Baraya',
                    'message' => 'Anda menerima notifikasi dari sistem Apotek Baraya.',
                    'data' => $data
                ];
        }
    }

    /**
     * Get recipients based on notification type.
     */
    private function getRecipients(): array
    {
        switch ($this->notificationType) {
            case 'user_created':
            case 'user_deleted':
                // Send to admin emails only
                return User::whereHas('role', function($query) {
                    $query->where('name', 'admin');
                })->pluck('email')->toArray();

            case 'user_updated':
                // Send to admin emails and the user themselves
                $recipients = User::whereHas('role', function($query) {
                    $query->where('name', 'admin');
                })->pluck('email')->toArray();
                
                $recipients[] = $this->user->email;
                return array_unique($recipients);

            case 'login_attempt':
                // Send to the user and admins
                $recipients = User::whereHas('role', function($query) {
                    $query->where('name', 'admin');
                })->pluck('email')->toArray();
                
                $recipients[] = $this->user->email;
                return array_unique($recipients);

            default:
                return [$this->user->email];
        }
    }

    /**
     * Handle a job failure.
     *
     * @param Exception $exception
     * @return void
     */
    public function failed(Exception $exception): void
    {
        Log::error('Email notification job failed permanently', [
            'user_id' => $this->user->id,
            'notification_type' => $this->notificationType,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);

        // Create failed notification record
        EmailNotification::create([
            'user_id' => $this->user->id,
            'event_type' => $this->notificationType,
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
            'data' => json_encode($this->additionalData),
            'retry_count' => $this->attempts()
        ]);
    }
}
