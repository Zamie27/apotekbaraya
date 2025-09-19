<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

echo "Testing startDelivery function...\n";

// Find a delivery with ready_to_ship status
$delivery = Delivery::with('order')->where('delivery_status', 'ready_to_ship')->first();

if (!$delivery) {
    echo "No delivery with ready_to_ship status found. Creating one...\n";
    $delivery = Delivery::with('order')->first();
    $delivery->update(['delivery_status' => 'ready_to_ship']);
    $delivery->refresh();
}

echo "Found delivery ID: {$delivery->delivery_id}\n";
echo "Current delivery status: {$delivery->delivery_status}\n";
echo "Current order status: {$delivery->order->status}\n";

// Simulate the startDelivery logic
try {
    Log::info('StartDelivery called for delivery ID: ' . $delivery->delivery_id);
    Log::info('Current delivery status: ' . $delivery->delivery_status);
    Log::info('Current order status: ' . $delivery->order->status);
    
    // Check if delivery status is ready_to_ship
    if ($delivery->delivery_status !== 'ready_to_ship') {
        Log::warning('Delivery status is not ready_to_ship: ' . $delivery->delivery_status);
        echo "Error: Delivery status is not ready_to_ship\n";
        exit(1);
    }

    // Update delivery status to in_transit
    $deliveryUpdated = $delivery->update([
        'delivery_status' => 'in_transit'
    ]);
    Log::info('Delivery update result: ' . ($deliveryUpdated ? 'success' : 'failed'));
    echo "Delivery update: " . ($deliveryUpdated ? 'success' : 'failed') . "\n";

    // Update order status to shipped
    $orderUpdated = $delivery->order->update([
        'status' => 'shipped',
        'shipped_at' => now()
    ]);
    Log::info('Order update result: ' . ($orderUpdated ? 'success' : 'failed'));
    echo "Order update: " . ($orderUpdated ? 'success' : 'failed') . "\n";

    // Reload data
    $delivery->refresh();
    $delivery->load('order');
    
    Log::info('After reload - Delivery status: ' . $delivery->delivery_status);
    Log::info('After reload - Order status: ' . $delivery->order->status);
    
    echo "After update:\n";
    echo "Delivery status: {$delivery->delivery_status}\n";
    echo "Order status: {$delivery->order->status}\n";
    echo "Success!\n";

} catch (\Exception $e) {
    Log::error('Error starting delivery: ' . $e->getMessage());
    Log::error('Stack trace: ' . $e->getTraceAsString());
    echo "Error: " . $e->getMessage() . "\n";
}