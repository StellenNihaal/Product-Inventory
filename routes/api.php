<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\admin\ProductController;
use App\Http\Controllers\Api\admin\BrandController;
use App\Http\Controllers\Api\admin\CategoryController;
use App\Http\Controllers\Api\admin\OrderController;


// Public routes
Route::post('/login',  [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/products',      [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/brands',                    [BrandController::class, 'index']);
Route::get('/brands/{brand}',            [BrandController::class, 'show']);
Route::get('/brands/{brand}/products',   [BrandController::class, 'products']);
Route::get('/categories',                       [CategoryController::class, 'index']);
Route::get('/categories/{category}',            [CategoryController::class, 'show']);
Route::get('/categories/{category}/products',   [CategoryController::class, 'products']);


// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me',     [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/cart',                      [CartController::class, 'index']);
    Route::post('/cart/add/{product}',       [CartController::class, 'add']);
    Route::post('/cart/decrement/{product}', [CartController::class, 'decrement']);
    Route::patch('/cart/{cartItem}',         [CartController::class, 'update']);
    Route::delete('/cart/{cartItem}',        [CartController::class, 'remove']);
    Route::delete('/cart',                   [CartController::class, 'clear']);
    Route::post('/products',           [ProductController::class, 'store']);
    Route::post('/products/{product}', [ProductController::class, 'update']); // POST with _method=PUT for multipart
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    Route::post('/brands',          [BrandController::class, 'store']);
    Route::put('/brands/{brand}',   [BrandController::class, 'update']);
    Route::delete('/brands/{brand}', [BrandController::class, 'destroy']);
    Route::post('/categories',            [CategoryController::class, 'store']);
    Route::put('/categories/{category}',  [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    Route::get('/admin/orders',                        [OrderController::class, 'index']);
    Route::get('/admin/orders/{order}',                [OrderController::class, 'show']);
    Route::patch('/admin/orders/{order}/status',       [OrderController::class, 'updateStatus']);
    Route::get('/orders',                              [OrderController::class, 'userOrders']);
    Route::get('/orders/{order}',                      [OrderController::class, 'userOrderShow']);
});
