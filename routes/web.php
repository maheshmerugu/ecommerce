<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\ShippingRateController as AdminShippingRateController;
use App\Http\Controllers\Customer\AuthController as CustomerAuthController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Test route for images
Route::get('test-images', function() {
    return view('test-images');
})->name('test.images');

// Debug route for images
Route::get('debug-images', function() {
    return view('debug-images');
})->name('debug.images');

// Test image URLs
Route::get('test-image-urls', function() {
    $product = App\Models\Product::with('images')->first();
    
    return [
        'asset_url' => $product && $product->images->count() > 0 ? asset('storage/' . $product->images->first()->image_path) : 'No product found',
        'helper_url' => $product && $product->images->count() > 0 ? product_image_url($product->images->first()->image_path) : 'No product found',
        'config_app_url' => config('app.url'),
        'storage_url' => $product && $product->images->count() > 0 ? Storage::url($product->images->first()->image_path) : 'No product found'
    ];
})->name('test.image.urls');

// Public Product Routes
Route::get('products', [ProductController::class, 'index'])->name('products.index');
Route::get('products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Cart Routes (available for both guests and logged-in users)
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('add', [CartController::class, 'add'])->name('add');
    Route::put('update/{cartItem}', [CartController::class, 'update'])->name('update');
    Route::delete('remove/{cartItem}', [CartController::class, 'remove'])->name('remove');
    Route::delete('clear', [CartController::class, 'clear'])->name('clear');
    Route::get('count', [CartController::class, 'count'])->name('count');
});

// Location endpoints for dynamic state/city lists
Route::get('locations/states', [LocationController::class, 'states'])->name('locations.states');
Route::get('locations/cities', [LocationController::class, 'cities'])->name('locations.cities');
Route::get('locations/pincodes', [LocationController::class, 'pincodes'])->name('locations.pincodes');
Route::get('shipping/calculate', [ShippingController::class, 'calculate'])->name('shipping.calculate');

// Wishlist Routes (available for both guests and logged-in users)
Route::prefix('wishlist')->name('wishlist.')->group(function () {
    Route::get('/', [WishlistController::class, 'index'])->name('index');
    Route::post('add', [WishlistController::class, 'add'])->name('add');
    Route::delete('remove/{wishlist}', [WishlistController::class, 'remove'])->name('remove');
    Route::delete('clear', [WishlistController::class, 'clear'])->name('clear');
    Route::get('count', [WishlistController::class, 'count'])->name('count');
});

// Customer Authentication Routes (no prefix)
Route::middleware('guest:customer')->group(function () {
    Route::get('login', [CustomerAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [CustomerAuthController::class, 'login']);
    Route::get('register', [CustomerAuthController::class, 'showRegister'])->name('register');
    Route::post('register', [CustomerAuthController::class, 'register']);
    
    // Password Reset for Customers
    Route::get('password/forgot', [CustomerAuthController::class, 'showForgotForm'])->name('password.request');
    Route::post('password/email', [CustomerAuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [CustomerAuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [CustomerAuthController::class, 'reset'])->name('password.update');
});

// Customer Protected Routes (no prefix)
Route::middleware(['auth:customer'])->group(function () {
    Route::post('logout', [CustomerAuthController::class, 'logout'])->name('customer.logout');
    Route::get('dashboard', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');
    
    // Profile Management
    Route::get('profile', [CustomerProfileController::class, 'show'])->name('customer.profile');
    Route::get('profile/show', [CustomerProfileController::class, 'show'])->name('customer.profile.show');
    Route::put('profile', [CustomerProfileController::class, 'update'])->name('customer.profile.update');
    Route::get('profile/edit', [CustomerProfileController::class, 'edit'])->name('customer.profile.edit');
    
    // Address Management
    Route::get('addresses', [CustomerProfileController::class, 'addresses'])->name('customer.addresses.index');
    Route::get('addresses/create', [CustomerProfileController::class, 'createAddress'])->name('customer.addresses.create');
    Route::post('addresses', [CustomerProfileController::class, 'storeAddress'])->name('customer.addresses.store');
    Route::get('addresses/{address}/edit', [CustomerProfileController::class, 'editAddress'])->name('customer.addresses.edit');
    Route::put('addresses/{address}', [CustomerProfileController::class, 'updateAddress'])->name('customer.addresses.update');
    Route::delete('addresses/{address}', [CustomerProfileController::class, 'destroyAddress'])->name('customer.addresses.destroy');
    Route::patch('addresses/{address}/default', [CustomerProfileController::class, 'setDefaultAddress'])->name('customer.addresses.set-default');

    // Order History
    Route::get('orders', [CustomerProfileController::class, 'orders'])->name('customer.orders.index');
    Route::get('orders/{order}', [CustomerProfileController::class, 'orderShow'])->name('customer.orders.show');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Authentication Routes
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AdminAuthController::class, 'login']);
    });

    // Admin Protected Routes
    Route::middleware(['auth:admin'])->group(function () {
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Category Management
        Route::resource('categories', AdminCategoryController::class);
        Route::patch('categories/{category}/toggle-status', [AdminCategoryController::class, 'toggleStatus'])->name('categories.toggle-status');

        // Product Management
        Route::resource('products', AdminProductController::class);
        Route::patch('products/{product}/toggle-status', [AdminProductController::class, 'toggleStatus'])->name('products.toggle-status');
        Route::patch('products/{product}/toggle-featured', [AdminProductController::class, 'toggleFeatured'])->name('products.toggle-featured');

        // Order Management
        Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');

        // Customer Management
        Route::get('customers', [AdminCustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/{customer}', [AdminCustomerController::class, 'show'])->name('customers.show');
        Route::patch('customers/{customer}/toggle-status', [AdminCustomerController::class, 'toggleStatus'])->name('customers.toggle-status');

        // Banner Management
        Route::resource('banners', AdminBannerController::class)->except(['show']);
        Route::patch('banners/{banner}/toggle-status', [AdminBannerController::class, 'toggleStatus'])->name('banners.toggle-status');

        // Settings
        Route::get('settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [AdminSettingController::class, 'update'])->name('settings.update');
        Route::put('settings/email', [AdminSettingController::class, 'updateEmail'])->name('settings.update-email');
        Route::post('settings/test-email', [AdminSettingController::class, 'testEmail'])->name('settings.test-email');
        Route::post('shipping-rates', [AdminShippingRateController::class, 'store'])->name('shipping-rates.store');
        Route::delete('shipping-rates/{shippingRate}', [AdminShippingRateController::class, 'destroy'])->name('shipping-rates.destroy');
    });
});

// Checkout Routes
Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->middleware('auth:customer')->name('index');
    Route::post('process', [CheckoutController::class, 'process'])->middleware('auth:customer')->name('process');
    Route::post('payment/success', [CheckoutController::class, 'paymentSuccess'])->middleware('auth:customer')->name('payment.success');
    Route::get('payment/failed', [CheckoutController::class, 'paymentFailed'])->middleware('auth:customer')->name('payment.failed');
    Route::get('success/{order}', [CheckoutController::class, 'success'])->middleware('auth:customer')->name('success');
});
