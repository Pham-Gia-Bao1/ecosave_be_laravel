<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Product Routes
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'productDetail']);

// Category Routes
Route::apiResource('categories', CategoryController::class);

// Store Routes
Route::apiResource('stores', StoreController::class);
// Route::get('/stores/{storeId}/products', [ProductController::class, 'getAllProductsByStoreId']);

// Khôi phục cửa hàng đã xóa
Route::post('stores/{id}/restore', [StoreController::class, 'restore']);

//Xóa vĩnh viễn cửa hàng
Route::delete('stores/{id}/force-delete', [StoreController::class, 'forceDelete']);

// User Routes (Protected by Sanctum Authentication)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Payment
Route::post('/payment', [PaymentController::class, 'makePayment']);

// Authenticated User Routes
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/users', [UserController::class, 'index']);
});

// Store Products Routes (Authenticated)
Route::group(['prefix' => 'stores/{storeId}/products', 'middleware' => 'auth:api'], function () {
    Route::get('/', [ProductController::class, 'getProductsByStore']);
    Route::post('/', [ProductController::class, 'postAddProduct']);
    Route::get('/{productId}', [ProductController::class, 'getProductByStore']);
    Route::put('/{productId}', [ProductController::class, 'putUpdateProduct']);
    Route::delete('/{productId}', [ProductController::class, 'deleteProduct']);
    Route::post('/{productId}/restore', [ProductController::class, 'restoreProduct']);
    Route::delete('/{productId}/force-delete', [ProductController::class, 'forceDeleteProduct']);
});

// Authentication Routes
require __DIR__ . '/auth.php';
