<?php

namespace App\Console\Commands;

use App\Mail\PrescriptionOrderCreated;
use App\Models\Order;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Test command for prescription email notification
 * 
 * This command creates a test email to verify that the prescription order
 * notification system is working correctly
 */
class TestPrescriptionEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:prescription-email {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test prescription order email notification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'test@example.com';
        
        $this->info('Testing prescription order email notification...');
        
        try {
            // Get a sample user (or create a test one)
            $user = User::whereHas('role', function($query) {
                $query->where('name', 'pelanggan');
            })->first();
            
            if (!$user) {
                $this->error('No customer user found. Please create a customer user first.');
                return 1;
            }

            // Get a sample order (or create a test one)
            $order = Order::with('orderItems.product')->first();
            if (!$order) {
                $this->error('No order found. Please create an order first.');
                return 1;
            }

            // Get a sample prescription (or create a test one)
            $prescription = Prescription::first();
            if (!$prescription) {
                $this->error('No prescription found. Please create a prescription first.');
                return 1;
            }

            // Send test email
            Mail::to($email)->send(new PrescriptionOrderCreated($order, $prescription));
            
            $this->info("Test email sent successfully to: {$email}");
            $this->info("Order ID: {$order->order_id}");
            $this->info("Order Number: {$order->order_number}");
            $this->info("Prescription ID: {$prescription->prescription_id}");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Failed to send test email: ' . $e->getMessage());
            return 1;
        }
    }
}