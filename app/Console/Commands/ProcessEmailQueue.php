<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ProcessEmailQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:process-queue 
                            {--timeout=60 : The number of seconds the worker should run}
                            {--sleep=3 : Number of seconds to sleep when no job is available}
                            {--tries=3 : Number of times to attempt a job before logging it failed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process email notification queue jobs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting email queue worker...');
        
        $timeout = $this->option('timeout');
        $sleep = $this->option('sleep');
        $tries = $this->option('tries');

        Log::info('Email queue worker started', [
            'timeout' => $timeout,
            'sleep' => $sleep,
            'tries' => $tries
        ]);

        try {
            // Run the queue worker specifically for email jobs
            Artisan::call('queue:work', [
                '--queue' => 'emails,default',
                '--timeout' => $timeout,
                '--sleep' => $sleep,
                '--tries' => $tries,
                '--verbose' => true,
            ]);

            $this->info('Queue worker completed successfully.');
            
        } catch (\Exception $e) {
            $this->error('Queue worker failed: ' . $e->getMessage());
            Log::error('Email queue worker failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return 1;
        }

        return 0;
    }
}
