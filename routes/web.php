<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/dashboard', function () {
    return view('livewire.pelanggan.dashboard');
});
Route::get('/login', function () {
    return view('livewire.pelanggan.auth.login');
});
Route::get('/register', function () {
    return view('livewire.pelanggan.auth.register');
});