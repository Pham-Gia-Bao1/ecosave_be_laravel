<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'productDetail']);

require __DIR__ . '/auth.php';

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/users', [UserController::class, 'index']);
});

Route::group(['prefix' => 'stores/{storeId}/products', 'middleware' => 'auth:api'], function () {
    Route::get('/', [ProductController::class, 'getProductsByStore']);
    Route::post('/', [ProductController::class, 'postAddProduct']);
    Route::get('/{productId}', [ProductController::class, 'getProductByStore']);
    Route::put('/{productId}', [ProductController::class, 'putUpdateProduct']);
    Route::delete('/{productId}', [ProductController::class, 'deleteProduct']);
    Route::post('/{productId}/restore', [ProductController::class, 'restoreProduct']);
    Route::delete('/{productId}/force-delete', [ProductController::class, 'forceDeleteProduct']);
});

