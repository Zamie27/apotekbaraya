<?php
/**
 * Payment Methods Diagnostic Script for Production
 * 
 * This script helps diagnose payment method issues in production environment.
 * Run this script on your production server to check the status.
 * 
 * Usage: php diagnose_payment_methods.php
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== PAYMENT METHODS DIAGNOSTIC SCRIPT ===\n";
echo "Environment: " . config('app.env') . "\n";
echo "Database: " . config('database.default') . "\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // 1. Check if payment_methods table exists
    echo "1. Checking if payment_methods table exists...\n";
    if (Schema::hasTable('payment_methods')) {
        echo "   ✅ payment_methods table EXISTS\n";
        
        // Check table structure
        $columns = Schema::getColumnListing('payment_methods');
        echo "   📋 Table columns: " . implode(', ', $columns) . "\n";
        
        // 2. Check if table has data
        echo "\n2. Checking payment_methods data...\n";
        $count = PaymentMethod::count();
        echo "   📊 Total payment methods: {$count}\n";
        
        if ($count > 0) {
            // Show all payment methods
            $paymentMethods = PaymentMethod::select('payment_method_id', 'code', 'name', 'is_active', 'sort_order')
                ->orderBy('sort_order')
                ->get();
            
            echo "   📋 Payment Methods List:\n";
            echo "   " . str_pad('ID', 4) . " | " . str_pad('Code', 15) . " | " . str_pad('Name', 20) . " | " . str_pad('Active', 8) . " | Sort\n";
            echo "   " . str_repeat('-', 60) . "\n";
            
            foreach ($paymentMethods as $pm) {
                $active = $pm->is_active ? 'YES' : 'NO';
                echo "   " . str_pad($pm->payment_method_id, 4) . " | " . str_pad($pm->code, 15) . " | " . str_pad($pm->name, 20) . " | " . str_pad($active, 8) . " | {$pm->sort_order}\n";
            }
            
            // 3. Check active payment methods
            echo "\n3. Checking ACTIVE payment methods...\n";
            $activeCount = PaymentMethod::where('is_active', true)->count();
            echo "   ✅ Active payment methods: {$activeCount}\n";
            
            if ($activeCount === 0) {
                echo "   ❌ ERROR: No active payment methods found!\n";
                echo "   💡 SOLUTION: Activate at least one payment method\n";
            }
            
            // 4. Test CheckoutService logic
            echo "\n4. Testing CheckoutService payment method selection...\n";
            
            // Simulate the logic from CheckoutService
            $bankTransfer = PaymentMethod::where('code', 'bank_transfer')->where('is_active', true)->first();
            $cod = PaymentMethod::where('code', 'cod')->where('is_active', true)->first();
            $anyActive = PaymentMethod::where('is_active', true)->first();
            
            $selectedMethod = $bankTransfer ?? $cod ?? $anyActive;
            
            if ($selectedMethod) {
                echo "   ✅ Selected payment method: {$selectedMethod->name} ({$selectedMethod->code})\n";
            } else {
                echo "   ❌ ERROR: No payment method would be selected!\n";
                echo "   💡 SOLUTION: This is the cause of 'No payment method available' error\n";
            }
            
        } else {
            echo "   ❌ ERROR: payment_methods table is EMPTY!\n";
            echo "   💡 SOLUTION: Run PaymentMethodSeeder\n";
        }
        
    } else {
        echo "   ❌ ERROR: payment_methods table does NOT exist!\n";
        echo "   💡 SOLUTION: Run migrations first\n";
    }
    
    // 5. Check migrations status
    echo "\n5. Checking migrations status...\n";
    try {
        $migrations = DB::table('migrations')
            ->where('migration', 'like', '%payment%')
            ->pluck('migration')
            ->toArray();
        
        if (!empty($migrations)) {
            echo "   ✅ Payment-related migrations found:\n";
            foreach ($migrations as $migration) {
                echo "   - {$migration}\n";
            }
        } else {
            echo "   ⚠️  No payment-related migrations found in migrations table\n";
        }
    } catch (Exception $e) {
        echo "   ❌ ERROR checking migrations: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ FATAL ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== DIAGNOSTIC COMPLETE ===\n";

// Provide solutions
echo "\n🔧 RECOMMENDED SOLUTIONS:\n";
echo "1. If table doesn't exist: php artisan migrate\n";
echo "2. If table is empty: php artisan db:seed --class=PaymentMethodSeeder\n";
echo "3. If no active methods: UPDATE payment_methods SET is_active = 1 WHERE code IN ('bank_transfer', 'cod');\n";
echo "4. Check .env database configuration\n";
echo "5. Verify database connection\n";

echo "\n📝 PRODUCTION DEPLOYMENT CHECKLIST:\n";
echo "□ php artisan migrate --force\n";
echo "□ php artisan db:seed --class=PaymentMethodSeeder --force\n";
echo "□ php artisan config:cache\n";
echo "□ php artisan route:cache\n";
echo "□ php artisan view:cache\n";
echo "□ Verify .env database settings\n";
echo "□ Test checkout flow\n";