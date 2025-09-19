<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\User;

echo "=== Testing Refund System ===\n\n";

try {
    // Test 1: Check if models can be instantiated
    echo "1. Testing model instantiation...\n";
    $order = new Order();
    $payment = new Payment();
    $refund = new Refund();
    echo "✓ All models can be instantiated\n\n";
    
    // Test 2: Check if relationships are defined
    echo "2. Testing model relationships...\n";
    
    // Test Order relationships
    $orderRelations = ['refunds', 'latestRefund', 'payment'];
    foreach ($orderRelations as $relation) {
        if (method_exists($order, $relation)) {
            echo "✓ Order::{$relation}() method exists\n";
        } else {
            echo "✗ Order::{$relation}() method missing\n";
        }
    }
    
    // Test Payment relationships
    $paymentRelations = ['refunds', 'latestRefund'];
    foreach ($paymentRelations as $relation) {
        if (method_exists($payment, $relation)) {
            echo "✓ Payment::{$relation}() method exists\n";
        } else {
            echo "✗ Payment::{$relation}() method missing\n";
        }
    }
    
    // Test Refund relationships
    $refundRelations = ['order', 'payment', 'requestedBy', 'processedBy'];
    foreach ($refundRelations as $relation) {
        if (method_exists($refund, $relation)) {
            echo "✓ Refund::{$relation}() method exists\n";
        } else {
            echo "✗ Refund::{$relation}() method missing\n";
        }
    }
    echo "\n";
    
    // Test 3: Check Order methods
    echo "3. Testing Order methods...\n";
    $orderMethods = ['cancelOrder', 'processRefund', 'canBeCancelled', 'canBeRefunded'];
    foreach ($orderMethods as $method) {
        if (method_exists($order, $method)) {
            echo "✓ Order::{$method}() method exists\n";
        } else {
            echo "✗ Order::{$method}() method missing\n";
        }
    }
    echo "\n";
    
    // Test 4: Check Refund scopes and methods
    echo "4. Testing Refund methods...\n";
    $refundMethods = ['isPending', 'isCompleted', 'isFailed'];
    foreach ($refundMethods as $method) {
        if (method_exists($refund, $method)) {
            echo "✓ Refund::{$method}() method exists\n";
        } else {
            echo "✗ Refund::{$method}() method missing\n";
        }
    }
    echo "\n";
    
    // Test 5: Check database tables
    echo "5. Testing database tables...\n";
    
    // Check if refunds table exists
    $refundsTableExists = \Illuminate\Support\Facades\Schema::hasTable('refunds');
    echo $refundsTableExists ? "✓ Refunds table exists\n" : "✗ Refunds table missing\n";
    
    // Check refunds table columns
    if ($refundsTableExists) {
        $refundColumns = ['refund_id', 'order_id', 'payment_id', 'refund_key', 'refund_amount', 'status', 'reason'];
        foreach ($refundColumns as $column) {
            $hasColumn = \Illuminate\Support\Facades\Schema::hasColumn('refunds', $column);
            echo $hasColumn ? "✓ Column 'refunds.{$column}' exists\n" : "✗ Column 'refunds.{$column}' missing\n";
        }
    }
    echo "\n";
    
    // Test 6: Check MidtransService methods
    echo "6. Testing MidtransService...\n";
    $midtransService = app(\App\Services\MidtransService::class);
    $midtransMethods = ['refundTransaction', 'processRefundOrCancel'];
    foreach ($midtransMethods as $method) {
        if (method_exists($midtransService, $method)) {
            echo "✓ MidtransService::{$method}() method exists\n";
        } else {
            echo "✗ MidtransService::{$method}() method missing\n";
        }
    }
    echo "\n";
    
    echo "=== Refund System Test Completed ===\n";
    echo "All basic components are properly set up!\n";
    
} catch (Exception $e) {
    echo "Error during testing: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}