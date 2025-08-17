<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CartController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Cart API Routes
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/cart/count', [CartController::class, 'getCartCount'])->name('api.cart.count');
    Route::get('/cart/summary', [CartController::class, 'getCartSummary'])->name('api.cart.summary');
});