<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UploadLimitsController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('/upload-limits', UploadLimitsController::class)->name('upload-limits');

    Route::resource('products', ProductController::class);
    Route::resource('purchases', PurchaseController::class);
    Route::resource('sales', SaleController::class);
    Route::resource('customers', CustomerController::class)->only(['index', 'show']);
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('orders/{order}/print', [OrderController::class, 'print'])->name('orders.print');

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });
});
