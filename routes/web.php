<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;

use App\Livewire\Dashboard;
use App\Livewire\Kategori;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\OrderManagement;
use App\Livewire\Admin\StoreSettings;
use App\Livewire\Apoteker\Dashboard as ApotekerDashboard;
use App\Livewire\Apoteker\Orders as ApotekerOrders;
use App\Livewire\Kurir\Dashboard as KurirDashboard;
use App\Livewire\Admin\Profile as AdminProfile;
use App\Livewire\Apoteker\Profile as ApotekerProfile;
use App\Livewire\Kurir\Profile as KurirProfile;
use App\Livewire\Profile;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\EmailVerification;
use App\Livewire\Checkout;
use Illuminate\Support\Facades\Route;
use App\Models\StoreSetting;

// Root route - redirect based on auth status and role
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        switch ($user->role->name) {
            case 'admin':
                return redirect('/admin/dashboard');
            case 'apoteker':
                return redirect('/apoteker/dashboard');
            case 'kurir':
                return redirect('/kurir/dashboard');
            case 'pelanggan':
                return redirect('/dashboard'); // Redirect pelanggan ke dashboard utama
            default:
                return redirect('/dashboard'); // Default juga ke dashboard utama
        }
    }

    // If guest, redirect to public homepage
    return redirect('/dashboard');
})->name('root');

// Public homepage for guests
Route::get('/dashboard', Dashboard::class)->name('dashboard')->middleware(['auth', 'verified']);

// Home route alias for dashboard (for backward compatibility)
Route::get('/home', function () {
    return redirect()->route('dashboard');
})->name('home');

// Public product description page (accessible by guests)
Route::get('/produk/{id?}', \App\Livewire\Deskripsi::class)->name('produk.deskripsi');

// Public search page (accessible by guests)
Route::get('/search', \App\Livewire\Search::class)->name('search');

// Public category page (accessible by guests)
Route::get('/kategori/{slug?}', Kategori::class)->name('kategori');

// Payment routes (accessible by Midtrans and authenticated users)
Route::post('/payment/notification', [\App\Http\Controllers\WebhookController::class, 'midtransNotification'])->name('payment.notification');
Route::get('/payment/finish', [PaymentController::class, 'finish'])->name('payment.finish');
Route::middleware(['auth', 'verified'])->get('/payment/status', [PaymentController::class, 'checkStatus'])->name('payment.status');
Route::middleware(['auth', 'verified'])->get('/payment', \App\Livewire\Payment::class)->name('payment.page');
Route::middleware(['auth', 'verified'])->get('/payment/snap', \App\Livewire\PaymentSnap::class)->name('payment.snap');



// Cart and Checkout pages (requires authentication and email verification)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/cart', \App\Livewire\Cart::class)->name('cart');
    Route::get('/checkout', Checkout::class)->name('checkout')->middleware('store.config');
});

// Guest only routes (redirect to dashboard if authenticated)
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

// Email verification routes
Route::get('/email/verify/{token}', [\App\Http\Controllers\EmailVerificationController::class, 'verify'])->name('email.verify');
Route::get('/email/verification/notice', [\App\Http\Controllers\EmailVerificationController::class, 'notice'])->name('email.verification.notice');
Route::post('/email/verification/resend', [\App\Http\Controllers\EmailVerificationController::class, 'resend'])->name('email.verification.resend');

// Email verification route (accessible by authenticated users who haven't verified email)
Route::get('/email/verification', EmailVerification::class)->name('email.verification')->middleware('auth');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Logout - ubah dari POST ke GET untuk kemudahan
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin routes - only admin can access
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', AdminDashboard::class)->name('admin.dashboard');
        Route::get('/admin/profile', AdminProfile::class)->name('admin.profile');
        Route::get('/admin/settings', StoreSettings::class)->name('admin.settings');
        Route::get('/admin/orders', OrderManagement::class)->name('admin.orders');
        Route::get('/admin/orders/{orderId}', \App\Livewire\Admin\OrderDetail::class)->name('admin.orders.detail');
        Route::get('/admin/refunds', \App\Livewire\Admin\RefundManagement::class)->name('admin.refunds');

        // Add more admin routes here
        // Route::get('/admin/users', AdminUsers::class);
        // Route::get('/admin/products', AdminProducts::class);
    });

    // Apoteker routes - only apoteker can access
    Route::middleware('role:apoteker')->group(function () {
        Route::get('/apoteker/dashboard', ApotekerDashboard::class)->name('apoteker.dashboard');
        Route::get('/apoteker/orders', ApotekerOrders::class)->name('apoteker.orders');
        Route::get('/apoteker/orders/{orderId}', \App\Livewire\Apoteker\OrderDetail::class)->name('apoteker.orders.detail');
        Route::get('/apoteker/profile', ApotekerProfile::class)->name('apoteker.profile');
        // Add more apoteker routes here
        // Route::get('/apoteker/prescriptions', ApotekerPrescriptions::class);
        // Route::get('/apoteker/inventory', ApotekerInventory::class);
    });

    // Kurir routes - only kurir can access
    Route::middleware('role:kurir')->group(function () {
        Route::get('/kurir/dashboard', KurirDashboard::class)->name('kurir.dashboard');
        Route::get('/kurir/profile', KurirProfile::class)->name('kurir.profile');
        Route::get('/kurir/deliveries', \App\Livewire\Kurir\Deliveries::class)->name('kurir.deliveries');
        Route::get('/kurir/deliveries/{deliveryId}', \App\Livewire\Kurir\DeliveryDetail::class)->name('kurir.deliveries.detail');
        // Add more kurir routes here
        // Route::get('/kurir/routes', KurirRoutes::class);
    });

    // Pelanggan routes - only pelanggan can access
    Route::middleware('role:pelanggan')->group(function () {
        // Dashboard pelanggan sekarang menggunakan dashboard utama di /dashboard
        Route::get('/profile', Profile::class)->name('profile');
        // Customer orders and cart routes
        Route::get('/orders', \App\Livewire\Pelanggan\Orders::class)->name('pelanggan.orders');
        Route::get('/orders/{orderId}', \App\Livewire\Pelanggan\OrderDetail::class)->name('pelanggan.orders.show');
        // Route::get('/cart', \App\Livewire\Pelanggan\Cart::class)->name('cart');
        // Add more pelanggan routes here
    });
});

// Webhook routes - no authentication required
Route::post('/webhook/midtrans', [\App\Http\Controllers\WebhookController::class, 'midtransNotification'])
    ->name('webhook.midtrans')
    ->withoutMiddleware(['web', 'auth']);
