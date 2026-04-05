<?php

use App\Http\Controllers\Payment\MidtransCallbackController;
use App\Http\Controllers\ProfileController as LegacyProfileController;
use App\Http\Controllers\Web\AddressController;
use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\CatalogController;
use App\Http\Controllers\Web\CheckoutController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\ProductReviewController;
use App\Http\Controllers\Web\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/products', [CatalogController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/dashboard', function () {
    $user = Auth::user();

    return redirect()->route($user?->isAdmin() ? 'admin.dashboard' : 'orders.index');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [LegacyProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [LegacyProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [LegacyProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/items', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/cart/items/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/items/{item}', [CartController::class, 'destroy'])->name('cart.destroy');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::get('/checkout/voucher-preview', [CheckoutController::class, 'previewVoucher'])->name('checkout.voucher-preview');
    Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])->name('checkout.place-order');
    Route::get('/checkout/finish', [CheckoutController::class, 'finish'])->name('checkout.finish');
    Route::get('/checkout/unfinish', [CheckoutController::class, 'unfinish'])->name('checkout.unfinish');
    Route::get('/checkout/error', [CheckoutController::class, 'error'])->name('checkout.error');
    Route::post('/products/{product:slug}/reviews', [ProductReviewController::class, 'store'])->name('products.reviews.store');

    Route::get('/account/profile', [ProfileController::class, 'edit'])->name('account.profile.edit');
    Route::patch('/account/profile', [ProfileController::class, 'update'])->name('account.profile.update');
    Route::resource('/account/addresses', AddressController::class)
        ->except(['show'])
        ->names('addresses');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{orderNumber}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{orderNumber}/complete', [OrderController::class, 'complete'])->name('orders.complete');
    Route::delete('/orders/{orderNumber}', [OrderController::class, 'destroy'])->name('orders.destroy');
});

Route::post('/payments/midtrans/notification', MidtransCallbackController::class)
    ->middleware('throttle:30,1')
    ->name('payments.midtrans.notification');

require __DIR__.'/auth.php';
