<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\ShippingOptionController;
use App\Http\Controllers\Admin\VoucherController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::resource('banners', BannerController::class)->except(['show']);
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('products', ProductController::class)->except(['show']);
        Route::delete('products/{product}/images/{productImage}', [ProductController::class, 'destroyImage'])
            ->name('products.images.destroy');
        Route::resource('products.variants', ProductVariantController::class)->except(['show']);
        Route::delete('products/{product}/variants/{variant}/images/{productImage}', [ProductVariantController::class, 'destroyImage'])
            ->name('products.variants.images.destroy');
        Route::resource('shipping-options', ShippingOptionController::class)->except(['show']);
        Route::resource('vouchers', VoucherController::class)->except(['show']);
        Route::resource('orders', OrderController::class)->only(['index', 'show', 'update']);
        Route::post('orders/{order}/mark-processing', [OrderController::class, 'markProcessing'])->name('orders.mark-processing');
        Route::post('orders/{order}/mark-shipped', [OrderController::class, 'markShipped'])->name('orders.mark-shipped');
        Route::post('orders/{order}/mark-completed', [OrderController::class, 'markCompleted'])->name('orders.mark-completed');
        Route::post('orders/{order}/mark-cancelled', [OrderController::class, 'markCancelled'])->name('orders.mark-cancelled');
        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
    });
