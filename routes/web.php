<?php

use App\Http\Controllers\AuthController;
use App\Livewire\Dashboard;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Apoteker\Dashboard as ApotekerDashboard;
use App\Livewire\Kurir\Dashboard as KurirDashboard;
use App\Livewire\Pelanggan\Dashboard as PelangganDashboard;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use Illuminate\Support\Facades\Route;

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
                return redirect('/pelanggan/dashboard');
            default:
                return redirect('/pelanggan/dashboard');
        }
    }

    // If guest, redirect to public homepage
    return redirect('/dashboard');
})->name('root');

// Public homepage for guests
Route::get('/dashboard', Dashboard::class)->name('home');

// Public routes that everyone can access
Route::get('/about', function () {
    return view('pages.about');  // You can create this later
});

Route::get('/contact', function () {
    return view('pages.contact');  // You can create this later
});

// Guest only routes (redirect to dashboard if authenticated)
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Logout - ubah dari POST ke GET untuk kemudahan
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin routes - only admin can access
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', AdminDashboard::class);
        // Add more admin routes here
        // Route::get('/admin/users', AdminUsers::class);
        // Route::get('/admin/products', AdminProducts::class);
    });

    // Apoteker routes - only apoteker can access
    Route::middleware('role:apoteker')->group(function () {
        Route::get('/apoteker/dashboard', ApotekerDashboard::class);
        // Add more apoteker routes here
        // Route::get('/apoteker/prescriptions', ApotekerPrescriptions::class);
        // Route::get('/apoteker/inventory', ApotekerInventory::class);
    });

    // Kurir routes - only kurir can access
    Route::middleware('role:kurir')->group(function () {
        Route::get('/kurir/dashboard', KurirDashboard::class);
        // Add more kurir routes here
        // Route::get('/kurir/deliveries', KurirDeliveries::class);
        // Route::get('/kurir/routes', KurirRoutes::class);
    });

    // Pelanggan routes - only pelanggan can access
    Route::middleware('role:pelanggan')->group(function () {
        Route::get('/pelanggan/dashboard', PelangganDashboard::class);
        // Add more pelanggan routes here
        // Route::get('/pelanggan/profile', PelangganProfile::class);
        // Route::get('/pelanggan/orders', PelangganOrders::class);
        // Route::get('/pelanggan/cart', PelangganCart::class);
    });
});
