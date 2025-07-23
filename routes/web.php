<?php

use App\Livewire\Dashboard;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Apoteker\Dashboard as ApotekerDashboard;
use App\Livewire\Kurir\Dashboard as KurirDashboard;
use App\Livewire\Pelanggan\Dashboard as PelangganDashboard;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use Illuminate\Support\Facades\Route;

Route::get('/', Dashboard::class);

// Auth
Route::get('/login', Login::class);
Route::get('/register', Register::class);

// Pelanggan
Route::get('/pelanggan/dashboard', PelangganDashboard::class);

// Kurir
Route::get('/kurir/dashboard', KurirDashboard::class);

// Admin
Route::get('/admin/dashboard', AdminDashboard::class);

// Apoteker
Route::get('/apoteker/dashboard', ApotekerDashboard::class);
