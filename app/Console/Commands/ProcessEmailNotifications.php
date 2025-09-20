<?php

namespace App\Console\Commands;

use App\Services\EmailNotificationService;
use Illuminate\Console\Command;

class ProcessEmailNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:process-notifications {--limit=50 : Maximum number of notifications to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending email notifications and send them';

    /**
     * Execute the console command.
     */
    public function handle(EmailNotificationService $emailService)
    {
        $limit = (int) $this->option('limit');
        
        $this->info("Processing up to {$limit} pending email notifications...");
        
        $emailService->processPendingNotifications($limit);
        
        // Get statistics after processing
        $stats = $emailService->getStatistics();
        
        $this->info('Email notification processing completed!');
        $this->table(
            ['Status', 'Count'],
            [
                ['Total', $stats['total']],
                ['Pending', $stats['pending']],
                ['Sent', $stats['sent']],
                ['Failed', $stats['failed']],
                ['Sent Today', $stats['today_sent']],
                ['Sent This Week', $stats['this_week_sent']],
            ]
        );
        
        return Command::SUCCESS;
    }
}
