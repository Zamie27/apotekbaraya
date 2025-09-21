<?php
/**
 * Production Payment Methods Fix Script
 * 
 * This script fixes payment method issues in production environment.
 * It ensures payment_methods table exists, has data, and has active methods.
 * 
 * Usage: php fix_payment_methods_production.php
 * 
 * IMPORTANT: Run this script on your production server!
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

echo "=== PRODUCTION PAYMENT METHODS FIX SCRIPT ===\n";
echo "Environment: " . config('app.env') . "\n";
echo "Database: " . config('database.default') . "\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

$errors = [];
$fixes = [];

try {
    // Step 1: Check and run migrations if needed
    echo "Step 1: Checking migrations...\n";
    
    if (!Schema::hasTable('payment_methods')) {
        echo "   ❌ payment_methods table missing. Running migrations...\n";
        
        try {
            Artisan::call('migrate', ['--force' => true]);
            echo "   ✅ Migrations completed successfully\n";
            $fixes[] = "Ran database migrations";
        } catch (Exception $e) {
            $error = "Failed to run migrations: " . $e->getMessage();
            echo "   ❌ {$error}\n";
            $errors[] = $error;
        }
    } else {
        echo "   ✅ payment_methods table exists\n";
    }
    
    // Step 2: Check and seed payment methods if needed
    echo "\nStep 2: Checking payment methods data...\n";
    
    $count = PaymentMethod::count();
    echo "   📊 Current payment methods count: {$count}\n";
    
    if ($count === 0) {
        echo "   ❌ No payment methods found. Running seeder...\n";
        
        try {
            Artisan::call('db:seed', [
                '--class' => 'PaymentMethodSeeder',
                '--force' => true
            ]);
            echo "   ✅ PaymentMethodSeeder completed successfully\n";
            $fixes[] = "Ran PaymentMethodSeeder";
            
            // Recount after seeding
            $count = PaymentMethod::count();
            echo "   📊 New payment methods count: {$count}\n";
            
        } catch (Exception $e) {
            $error = "Failed to run PaymentMethodSeeder: " . $e->getMessage();
            echo "   ❌ {$error}\n";
            $errors[] = $error;
            
            // Try manual insertion as fallback
            echo "   🔄 Attempting manual payment method insertion...\n";
            $this->insertPaymentMethodsManually();
        }
    } else {
        echo "   ✅ Payment methods data exists\n";
    }
    
    // Step 3: Ensure at least one payment method is active
    echo "\nStep 3: Checking active payment methods...\n";
    
    $activeCount = PaymentMethod::where('is_active', true)->count();
    echo "   📊 Active payment methods count: {$activeCount}\n";
    
    if ($activeCount === 0) {
        echo "   ❌ No active payment methods! Activating default methods...\n";
        
        try {
            // Activate bank_transfer and cod
            $updated = PaymentMethod::whereIn('code', ['bank_transfer', 'cod'])
                ->update(['is_active' => true]);
            
            echo "   ✅ Activated {$updated} payment methods (bank_transfer, cod)\n";
            $fixes[] = "Activated default payment methods";
            
        } catch (Exception $e) {
            $error = "Failed to activate payment methods: " . $e->getMessage();
            echo "   ❌ {$error}\n";
            $errors[] = $error;
        }
    } else {
        echo "   ✅ Active payment methods found\n";
    }
    
    // Step 4: Verify the fix
    echo "\nStep 4: Verifying the fix...\n";
    
    // Test the same logic as CheckoutService
    $bankTransfer = PaymentMethod::where('code', 'bank_transfer')->where('is_active', true)->first();
    $cod = PaymentMethod::where('code', 'cod')->where('is_active', true)->first();
    $anyActive = PaymentMethod::where('is_active', true)->first();
    
    $selectedMethod = $bankTransfer ?? $cod ?? $anyActive;
    
    if ($selectedMethod) {
        echo "   ✅ SUCCESS: Payment method selection works!\n";
        echo "   📋 Selected method: {$selectedMethod->name} ({$selectedMethod->code})\n";
        $fixes[] = "Payment method selection verified";
    } else {
        $error = "CRITICAL: Still no payment method available after fixes!";
        echo "   ❌ {$error}\n";
        $errors[] = $error;
    }
    
    // Step 5: Clear caches
    echo "\nStep 5: Clearing caches...\n";
    
    try {
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');
        echo "   ✅ Caches cleared and rebuilt\n";
        $fixes[] = "Cleared and rebuilt caches";
    } catch (Exception $e) {
        $error = "Failed to clear caches: " . $e->getMessage();
        echo "   ⚠️  {$error}\n";
        $errors[] = $error;
    }
    
} catch (Exception $e) {
    $error = "FATAL ERROR: " . $e->getMessage();
    echo "❌ {$error}\n";
    $errors[] = $error;
}

// Summary
echo "\n=== FIX SUMMARY ===\n";

if (!empty($fixes)) {
    echo "✅ FIXES APPLIED:\n";
    foreach ($fixes as $fix) {
        echo "   - {$fix}\n";
    }
}

if (!empty($errors)) {
    echo "\n❌ ERRORS ENCOUNTERED:\n";
    foreach ($errors as $error) {
        echo "   - {$error}\n";
    }
}

if (empty($errors)) {
    echo "\n🎉 SUCCESS: All payment method issues have been fixed!\n";
    echo "Your checkout should now work properly in production.\n";
} else {
    echo "\n⚠️  Some issues remain. Please check the errors above.\n";
}

echo "\n📝 NEXT STEPS:\n";
echo "1. Test the checkout flow on your production site\n";
echo "2. Monitor error logs for any remaining issues\n";
echo "3. Consider setting up automated deployment scripts\n";

/**
 * Manual payment method insertion as fallback
 */
function insertPaymentMethodsManually() {
    try {
        $paymentMethods = [
            [
                'code' => 'bank_transfer',
                'name' => 'Transfer Bank',
                'description' => 'Pembayaran melalui transfer bank',
                'configuration' => json_encode([
                    'bank_name' => 'Bank BCA',
                    'account_number' => '1234567890',
                    'account_name' => 'Apotek Baraya'
                ]),
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'cod',
                'name' => 'Bayar di Tempat (COD)',
                'description' => 'Pembayaran saat barang diterima',
                'configuration' => json_encode([]),
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'e_wallet',
                'name' => 'E-Wallet',
                'description' => 'Pembayaran melalui dompet digital',
                'configuration' => json_encode([
                    'supported_wallets' => ['OVO', 'GoPay', 'DANA']
                ]),
                'is_active' => false,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];
        
        foreach ($paymentMethods as $method) {
            PaymentMethod::updateOrCreate(
                ['code' => $method['code']],
                $method
            );
        }
        
        echo "   ✅ Manual payment method insertion completed\n";
        return true;
        
    } catch (Exception $e) {
        echo "   ❌ Manual insertion failed: " . $e->getMessage() . "\n";
        return false;
    }
}