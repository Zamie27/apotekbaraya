<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Delivery;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users and products
        $users = User::whereHas('role', function($query) {
            $query->where('name', 'pelanggan');
        })->take(5)->get();
        $products = Product::take(10)->get();
        
        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('No users or products found. Please seed users and products first.');
            return;
        }

        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        $paymentStatuses = ['pending', 'paid', 'failed'];
        
        // Create 20 sample orders
        for ($i = 1; $i <= 20; $i++) {
            $user = $users->random();
            $status = $statuses[array_rand($statuses)];
            $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];
            
            // Create order
            $subtotal = rand(50000, 500000);
            $deliveryFee = rand(10000, 25000);
            $discountAmount = rand(0, 50000);
            $totalPrice = $subtotal + $deliveryFee - $discountAmount;
            
            $order = Order::create([
                'user_id' => $user->user_id,
                'order_number' => 'ORD-' . date('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'status' => $status,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'discount_amount' => $discountAmount,
                'total_price' => $totalPrice,
                'notes' => 'Sample order #' . $i,
                'shipping_address' => [
                    'name' => $user->name,
                    'phone' => '08' . rand(1000000000, 9999999999),
                    'address' => 'Jl. Sample Address No. ' . rand(1, 100),
                    'city' => 'Jakarta',
                    'postal_code' => rand(10000, 99999),
                    'province' => 'DKI Jakarta'
                ],
                'created_at' => now()->subDays(rand(0, 30)),
                'updated_at' => now()->subDays(rand(0, 5)),
            ]);
            
            // Create order items
            $itemCount = rand(1, 4);
            $selectedProducts = $products->random($itemCount);
            
            foreach ($selectedProducts as $product) {
                $qty = rand(1, 3);
                OrderItem::create([
                    'order_id' => $order->order_id,
                    'product_id' => $product->product_id,
                    'qty' => $qty,
                    'price' => $product->price,
                ]);
            }
            
            // Create payment
            Payment::create([
                'order_id' => $order->order_id,
                'payment_method_id' => rand(1, 2), // Assuming payment methods exist
                'amount' => $order->total_price,
                'status' => $paymentStatus,
                'payment_proof' => $paymentStatus === 'paid' ? 'proof_' . $order->order_id . '.jpg' : null,
                'paid_at' => $paymentStatus === 'paid' ? $order->created_at->addHours(rand(1, 24)) : null,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ]);
            
            // Create delivery if order is shipped or delivered
            if (in_array($status, ['shipped', 'delivered'])) {
                $courier = User::whereHas('role', function($query) {
                    $query->where('name', 'kurir');
                })->inRandomOrder()->first();
                
                Delivery::create([
                    'order_id' => $order->order_id,
                    'courier_id' => $courier?->user_id,
                    'delivery_address' => $order->shipping_address,
                    'delivery_fee' => $order->delivery_fee,
                    'status' => $status === 'delivered' ? 'delivered' : 'in_transit',
                    'delivered_at' => $status === 'delivered' ? $order->created_at->addDays(rand(4, 7)) : null,
                    'delivery_notes' => 'Sample delivery for order #' . $i,
                    'created_at' => $order->created_at,
                    'updated_at' => $order->updated_at,
                ]);
            }
        }
        
        $this->command->info('Created 20 sample orders with related data.');
    }
}
