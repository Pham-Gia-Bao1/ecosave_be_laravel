<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\SaveProductController;
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
Route::middleware('auconfig/auth.phpsanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Payment

// Authenticated User Routes
Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/payment', [PaymentController::class, 'makePayment']);
    Route::apiResource('/orders', OrderController::class);
    // Kiểm tra API bằng Postman hoặc Laravel Artisan
    // Lấy danh sách đơn hàng: GET /api/orders
    // Lấy đơn hàng cụ thể: GET /api/orders/{id}
    // Tạo đơn hàng: POST /api/orders
    // Cập nhật đơn hàng: PUT /api/orders/{id}
    // Xóa đơn hàng: DELETE /api/orders/{id}
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/cart', [CartController::class, 'getCart']);
    Route::post('/cart/add', [CartController::class, 'addToCart']);
    Route::get('/cart/{storeId}', [CartController::class, 'getCartDetail']);
    Route::delete('/cart/remove-item', [CartController::class, 'removeItem']);
    Route::put('/cart/update-quantity', [CartController::class, 'updateItemQuantity']);
    // product after scaned
    Route::get('/save-products', [SaveProductController::class, 'getSaveProductsByUser']);
    Route::post('/save-products', [SaveProductController::class, 'storeSaveProduct']);
});

// Store Products Routes (Authenticated)
Route::group(['prefix' => 'stores/{storeId}/products', 'middleware' => 'auth:api'], function () {
    Route::get('/', [ProductController::class, 'getProductsByStoreName']);
    Route::post('/', [ProductController::class, 'postAddProduct']);
    Route::get('/trashed', [ProductController::class, 'getTrashedProductsByStore']);

    Route::post('{productId}/restore', [ProductController::class, 'restoreProduct']);
    Route::delete('{productId}/force-delete', [ProductController::class, 'forceDeleteProduct']);

    Route::get('/{productId}', [ProductController::class, 'getProductByStore']);
    Route::put('/{productId}', [ProductController::class, 'putUpdateProduct']);
    Route::delete('/{productId}', [ProductController::class, 'deleteProduct']);
});


Route::post('/upload-image', [ImageController::class, 'upload']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categoriesByNameAndId', [CategoryController::class, 'getCategoryByNameAndId']);
Route::get('/store-id', [ProductController::class, 'getStoreId'])->middleware('auth:api');

// Authentication Routes
require __DIR__ . '/auth.php';
