<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use App\Services\EmailNotificationService;
use App\Models\User;

// Bootstrap Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Email Queue System ===\n\n";

try {
    // Get a test user (first admin user)
    $user = User::whereHas('role', function($query) {
        $query->where('name', 'admin');
    })->first();

    if (!$user) {
        echo "❌ No admin user found. Please create an admin user first.\n";
        exit(1);
    }

    echo "✅ Found test user: {$user->name} ({$user->email})\n\n";

    // Initialize email service
    $emailService = new EmailNotificationService();

    // Test 1: Queue User Created Notification
    echo "🧪 Test 1: Queue User Created Notification\n";
    $emailService->queueUserCreatedNotification($user, [
        'created_by' => [
            'name' => 'System Test',
            'email' => 'system@apotekbaraya.com'
        ],
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Test Script'
    ]);
    echo "✅ User created notification queued successfully\n\n";

    // Test 2: Queue User Updated Notification
    echo "🧪 Test 2: Queue User Updated Notification\n";
    $emailService->queueUserUpdatedNotification($user, [
        'updated_by' => [
            'name' => 'System Test',
            'email' => 'system@apotekbaraya.com'
        ],
        'changes' => [
            'name' => ['old' => 'Old Name', 'new' => 'New Name'],
            'email' => ['old' => 'old@email.com', 'new' => 'new@email.com']
        ],
        'ip_address' => '127.0.0.1'
    ]);
    echo "✅ User updated notification queued successfully\n\n";

    // Test 3: Queue Login Attempt Notification
    echo "🧪 Test 3: Queue Login Attempt Notification\n";
    $emailService->queueLoginAttemptNotification($user, [
        'ip_address' => '192.168.1.100',
        'user_agent' => 'Mozilla/5.0 Test Browser',
        'login_time' => now()->format('d/m/Y H:i:s'),
        'status' => 'success'
    ]);
    echo "✅ Login attempt notification queued successfully\n\n";

    // Check queue status
    echo "📊 Queue Status:\n";
    $queueSize = \DB::table('jobs')->where('queue', 'emails')->count();
    echo "- Jobs in 'emails' queue: {$queueSize}\n";

    $failedJobs = \DB::table('failed_jobs')->count();
    echo "- Failed jobs: {$failedJobs}\n\n";

    echo "🎉 All tests completed successfully!\n";
    echo "💡 Check the queue worker terminal to see job processing.\n";
    echo "📧 Check your email for notifications (if mail is configured).\n\n";

    echo "📋 Next steps:\n";
    echo "1. Monitor queue worker output\n";
    echo "2. Check Laravel logs: storage/logs/laravel.log\n";
    echo "3. Verify email delivery\n";
    echo "4. Test failed job handling\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}