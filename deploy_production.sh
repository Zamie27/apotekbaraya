#!/bin/bash

# Production Deployment Script for Apotek Baraya
# This script fixes payment method issues and deploys the application

echo "=== APOTEK BARAYA PRODUCTION DEPLOYMENT ==="
echo "Date: $(date)"
echo "Environment: Production"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_info() {
    echo -e "📋 $1"
}

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    print_error "artisan file not found. Please run this script from the Laravel project root."
    exit 1
fi

print_info "Starting production deployment..."

# Step 1: Update composer dependencies
echo ""
echo "Step 1: Updating Composer dependencies..."
if composer install --no-dev --optimize-autoloader; then
    print_success "Composer dependencies updated"
else
    print_error "Failed to update composer dependencies"
    exit 1
fi

# Step 2: Run migrations
echo ""
echo "Step 2: Running database migrations..."
if php artisan migrate --force; then
    print_success "Database migrations completed"
else
    print_error "Database migrations failed"
    exit 1
fi

# Step 3: Seed payment methods
echo ""
echo "Step 3: Seeding payment methods..."
if php artisan db:seed --class=PaymentMethodSeeder --force; then
    print_success "Payment methods seeded successfully"
else
    print_warning "Payment method seeding failed, trying alternative method..."
    
    # Alternative: Run our custom fix script
    if php fix_payment_methods_production.php; then
        print_success "Payment methods fixed using alternative method"
    else
        print_error "Failed to fix payment methods"
        exit 1
    fi
fi

# Step 4: Activate payment methods
echo ""
echo "Step 4: Activating payment methods..."
php artisan tinker --execute="
use App\Models\PaymentMethod;
\$updated = PaymentMethod::whereIn('code', ['bank_transfer', 'cod'])->update(['is_active' => true]);
echo \"Activated {\$updated} payment methods\n\";
"

if [ $? -eq 0 ]; then
    print_success "Payment methods activated"
else
    print_error "Failed to activate payment methods"
fi

# Step 5: Clear and cache configurations
echo ""
echo "Step 5: Clearing and caching configurations..."

php artisan config:clear
php artisan config:cache
print_success "Configuration cached"

php artisan route:clear
php artisan route:cache
print_success "Routes cached"

php artisan view:clear
php artisan view:cache
print_success "Views cached"

# Step 6: Set proper permissions (if on Linux/Unix)
if [[ "$OSTYPE" == "linux-gnu"* ]] || [[ "$OSTYPE" == "darwin"* ]]; then
    echo ""
    echo "Step 6: Setting proper permissions..."
    
    chmod -R 755 storage
    chmod -R 755 bootstrap/cache
    print_success "Permissions set"
fi

# Step 7: Verify payment methods
echo ""
echo "Step 7: Verifying payment methods..."
php artisan tinker --execute="
use App\Models\PaymentMethod;
\$count = PaymentMethod::count();
\$active = PaymentMethod::where('is_active', true)->count();
echo \"Total payment methods: {\$count}\n\";
echo \"Active payment methods: {\$active}\n\";

if (\$active > 0) {
    echo \"✅ Payment methods verification PASSED\n\";
} else {
    echo \"❌ Payment methods verification FAILED\n\";
    exit(1);
}
"

if [ $? -eq 0 ]; then
    print_success "Payment methods verification passed"
else
    print_error "Payment methods verification failed"
    exit 1
fi

# Step 8: Run diagnostic script
echo ""
echo "Step 8: Running diagnostic check..."
if php diagnose_payment_methods.php; then
    print_success "Diagnostic check completed"
else
    print_warning "Diagnostic check had issues, but deployment may still be successful"
fi

echo ""
print_success "=== DEPLOYMENT COMPLETED SUCCESSFULLY ==="
echo ""
print_info "Next steps:"
echo "1. Test the checkout flow on your production website"
echo "2. Monitor application logs for any errors"
echo "3. Verify that payment methods are working correctly"
echo ""
print_info "If you encounter any issues, check the logs:"
echo "- tail -f storage/logs/laravel.log"
echo "- Check your web server error logs"
echo ""
print_success "Your Apotek Baraya application is now ready for production!"