<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CancelExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cancel-expired 
                            {--dry-run : Show what would be cancelled without actually cancelling}
                            {--days=1 : Number of days after which orders expire}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel orders that have not been paid within the specified time period';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $expireDays = (int) $this->option('days');
        
        $this->info("Starting expired orders cancellation process...");
        $this->info("Expire after: {$expireDays} day(s)");
        
        if ($dryRun) {
            $this->warn("DRY RUN MODE - No actual changes will be made");
        }

        // Find orders that need to be cancelled
        $expiredOrders = $this->findExpiredOrders($expireDays);
        
        if ($expiredOrders->isEmpty()) {
            $this->info("No expired orders found.");
            return Command::SUCCESS;
        }

        $this->info("Found {$expiredOrders->count()} expired order(s):");
        
        // Display orders that will be cancelled
        $this->displayOrdersTable($expiredOrders);

        if (!$dryRun) {
            if ($this->confirm('Do you want to proceed with cancelling these orders?', true)) {
                $cancelledCount = $this->cancelOrders($expiredOrders);
                $this->info("Successfully cancelled {$cancelledCount} order(s).");
            } else {
                $this->info("Operation cancelled by user.");
                return Command::SUCCESS;
            }
        }

        return Command::SUCCESS;
    }

    /**
     * Find orders that have expired and need to be cancelled
     *
     * @param int $expireDays
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function findExpiredOrders(int $expireDays)
    {
        $expireDate = Carbon::now()->subDays($expireDays);
        
        return Order::with(['items.product', 'payment'])
            ->where('status', 'waiting_payment')
            ->where('created_at', '<=', $expireDate)
            ->whereDoesntHave('payment', function ($query) {
                $query->whereIn('status', ['settlement', 'capture', 'success']);
            })
            ->get();
    }

    /**
     * Display orders in a table format
     *
     * @param \Illuminate\Database\Eloquent\Collection $orders
     * @return void
     */
    private function displayOrdersTable($orders)
    {
        $tableData = [];
        
        foreach ($orders as $order) {
            $tableData[] = [
                'Order ID' => $order->order_number,
                'Customer' => $order->customer_name ?? 'N/A',
                'Total' => 'Rp ' . number_format($order->total_amount, 0, ',', '.'),
                'Created' => $order->created_at->format('Y-m-d H:i:s'),
                'Days Ago' => $order->created_at->diffInDays(Carbon::now()),
                'Status' => $order->status,
            ];
        }
        
        $this->table(
            ['Order ID', 'Customer', 'Total', 'Created', 'Days Ago', 'Status'],
            $tableData
        );
    }

    /**
     * Cancel the expired orders and restore stock
     *
     * @param \Illuminate\Database\Eloquent\Collection $orders
     * @return int
     */
    private function cancelOrders($orders)
    {
        $cancelledCount = 0;
        
        foreach ($orders as $order) {
            try {
                DB::beginTransaction();
                
                // Update order status to cancelled
                $order->update([
                    'status' => 'cancelled',
                    'cancelled_at' => Carbon::now(),
                    'cancellation_reason' => 'Pesanan Expired - Tidak dibayar dalam 1 hari',
                    'cancelled_by' => null // System cancellation
                ]);
                
                // Note: Stock restoration not needed as products table uses enum stock (available/out_of_stock)
                // instead of quantity-based stock management
                
                // Log the cancellation
                Log::info("Order {$order->order_number} automatically cancelled due to expiration", [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'total_amount' => $order->total_amount,
                    'created_at' => $order->created_at,
                    'cancelled_at' => Carbon::now(),
                ]);
                
                DB::commit();
                $cancelledCount++;
                
                $this->line("✓ Cancelled order: {$order->order_number}");
                
            } catch (\Exception $e) {
                DB::rollBack();
                
                Log::error("Failed to cancel expired order {$order->order_number}", [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                $this->error("✗ Failed to cancel order: {$order->order_number} - {$e->getMessage()}");
            }
        }
        
        return $cancelledCount;
    }


}