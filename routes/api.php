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


require __DIR__ . '/auth.php';

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/users', [UserController::class, 'index']);

    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'productDetail']);
});

Route::group(['prefix' => 'stores/{storeId}', 'middleware' => 'auth:api'], function () {
    Route::get('/products', [ProductController::class, 'getProductsByStore']);
    Route::post('/products', [ProductController::class, 'postAddProduct']);
    Route::get('/products/{productId}', [ProductController::class, 'getProductByStore']);
    Route::put('/products/{productId}', [ProductController::class, 'putUpdateProduct']);
});

