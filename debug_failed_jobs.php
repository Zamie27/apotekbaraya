<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Debug Failed Jobs ===\n\n";

$failedJobs = DB::table('failed_jobs')->orderBy('failed_at', 'desc')->take(3)->get();

foreach ($failedJobs as $job) {
    echo "Job ID: {$job->id}\n";
    echo "Queue: {$job->queue}\n";
    echo "Failed At: {$job->failed_at}\n";
    echo "Exception:\n";
    echo substr($job->exception, 0, 500) . "...\n";
    echo str_repeat("-", 50) . "\n\n";
}