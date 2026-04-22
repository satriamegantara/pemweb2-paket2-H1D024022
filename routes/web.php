<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\ProductCatalogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductCatalogController::class, 'home'])->name('home');
Route::get('/products', [ProductCatalogController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductCatalogController::class, 'show'])->name('products.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index')->middleware('cart');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add')->middleware('cart');
Route::post('/cart/update/{product}', [CartController::class, 'update'])->name('cart.update')->middleware('cart');
Route::post('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove')->middleware('cart');

Route::middleware(['auth', 'verified', 'role:pembeli', 'cart'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/my-orders', [\App\Http\Controllers\Buyer\OrderController::class, 'index'])->name('buyer.orders.index');
    Route::get('/my-orders/{order}', [\App\Http\Controllers\Buyer\OrderController::class, 'show'])->name('buyer.orders.show');
    Route::post('/my-orders/{order}/cancel', [\App\Http\Controllers\Buyer\OrderController::class, 'cancel'])->name('buyer.orders.cancel');
});

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', \App\Http\Controllers\Admin\DashboardController::class)->name('dashboard');
    Route::resource('categories', AdminCategoryController::class)->except(['show']);
    Route::get('orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::post('orders/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.status');
});

Route::middleware(['auth', 'verified', 'role:penjual'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/', \App\Http\Controllers\Seller\DashboardController::class)->name('dashboard');
    Route::get('store/create', [\App\Http\Controllers\Seller\StoreController::class, 'create'])->name('store.create');
    Route::post('store', [\App\Http\Controllers\Seller\StoreController::class, 'store'])->name('store.store');
    Route::get('store', [\App\Http\Controllers\Seller\StoreController::class, 'edit'])->name('store.edit');
    Route::put('store', [\App\Http\Controllers\Seller\StoreController::class, 'update'])->name('store.update');

    Route::resource('products', \App\Http\Controllers\Seller\ProductController::class)->except(['show']);
    Route::post('products/{product}/images/{image}/primary', [\App\Http\Controllers\Seller\ProductController::class, 'setPrimaryImage'])->name('products.setPrimaryImage');
    Route::get('orders', [\App\Http\Controllers\Seller\OrderController::class, 'index'])->name('orders.index');
    Route::post('orders/{order}/status', [\App\Http\Controllers\Seller\OrderController::class, 'updateStatus'])->name('orders.status');

    Route::get('reports/sales', [\App\Http\Controllers\Seller\ReportController::class, 'index'])->name('reports.sales.index');
    Route::get('reports/sales/pdf', [\App\Http\Controllers\Seller\ReportController::class, 'exportPdf'])->name('reports.sales.pdf');
    Route::get('reports/sales/excel', [\App\Http\Controllers\Seller\ReportController::class, 'exportExcel'])->name('reports.sales.excel');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
