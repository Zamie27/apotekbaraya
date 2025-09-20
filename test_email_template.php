<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Mail\UserNotificationMail;
use Illuminate\Support\Facades\Mail;

echo "=== Testing Email Template ===\n\n";

// Get test user
$user = User::where('email', 'admin@apotekbaraya.com')->first();

if (!$user) {
    echo "❌ Test user not found\n";
    exit(1);
}

echo "✅ Found test user: {$user->name} ({$user->email})\n\n";

// Test 1: Simple user created notification
echo "🧪 Test 1: User Created Template\n";
try {
    $data = [
        'user' => $user,
        'created_by' => [
            'name' => 'Admin User',
            'email' => 'admin@apotekbaraya.com'
        ]
    ];
    
    $mail = new UserNotificationMail('user_created', $data, 'User Created Notification');
    $rendered = $mail->render();
    
    echo "✅ User created template rendered successfully\n";
    echo "📧 Template length: " . strlen($rendered) . " characters\n\n";
    
} catch (Exception $e) {
    echo "❌ Error rendering user created template: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
}

// Test 2: Simple login attempt notification
echo "🧪 Test 2: Login Attempt Template\n";
try {
    $data = [
        'user' => $user,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Test Browser'
    ];
    
    $mail = new UserNotificationMail('login_attempt', $data, 'Login Attempt Notification');
    $rendered = $mail->render();
    
    echo "✅ Login attempt template rendered successfully\n";
    echo "📧 Template length: " . strlen($rendered) . " characters\n\n";
    
} catch (Exception $e) {
    echo "❌ Error rendering login attempt template: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
}

echo "🎉 Template testing completed!\n";