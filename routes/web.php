<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;

// Root route
Route::get('/', function () {
    if (auth()->check() && !request()->has('preview')) {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('user.dashboard');
    }

    $totalProducts   = \App\Models\Product::count();
    $totalUsers      = \App\Models\User::where('role', 'user')->count();
    $totalCategories = \App\Models\Category::count();
    $totalBrands     = \App\Models\Brand::count();
    $categories      = \App\Models\Category::withCount('products')->take(6)->get();
    $cms             = \App\Models\LandingContent::allKeyed();

    return view('landing', compact(
        'totalProducts',
        'totalUsers',
        'totalCategories',
        'totalBrands',
        'categories',
        'cms'
    ));
})->name('landing');

// Admin profile
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/password', [ProfileController::class, 'editPassword'])->name('profile.password');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        $totalProducts   = \App\Models\Product::count();
        $totalUsers      = \App\Models\User::count();
        $totalCategories = \App\Models\Category::count();
        $totalBrands     = \App\Models\Brand::count();
        $recentProducts  = \App\Models\Product::with('category', 'brand')->latest()->take(5)->get();
        return view('dashboard', compact('totalProducts', 'totalUsers', 'totalCategories', 'totalBrands', 'recentProducts'));
    })->name('dashboard');

    Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/toggle', [App\Http\Controllers\Admin\UserController::class, 'toggle'])->name('users.toggle');

    Route::resource('products', AdminProductController::class);

    Route::get('/landing', [App\Http\Controllers\Admin\LandingPageController::class, 'index'])->name('landing.index');
    Route::patch('/landing', [App\Http\Controllers\Admin\LandingPageController::class, 'update'])->name('landing.update');

    Route::get('categories/{category}/products', [CategoryController::class, 'products'])->name('categories.products');
    Route::resource('categories', CategoryController::class);

    Route::get('brands/{brand}/products', [BrandController::class, 'products'])->name('brands.products');
    Route::resource('brands', BrandController::class);

    // ✅ Admin orders
    Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.status');
});

// Product routes
Route::middleware(['auth'])->group(function () {
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
});

// Checkout routes
Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/intent', [CheckoutController::class, 'createIntent'])->name('checkout.intent'); // ✅ new
    Route::post('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/order/{order}', [CheckoutController::class, 'orderShow'])->name('order.show');
    Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
});

// User routes
Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [UserProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/password', [UserProfileController::class, 'editPassword'])->name('profile.password');
    Route::patch('/profile/password', [UserProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

    Route::middleware('throttle:30,1')->group(function () {
        Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
        Route::post('/cart/decrement/{product}', [CartController::class, 'decrement'])->name('cart.decrement');
        Route::patch('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
        Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
    });
});

require __DIR__ . '/auth.php';
