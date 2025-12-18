<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CartApiController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/products/scan/{barcode}', [\App\Http\Controllers\Api\ProductApiController::class, 'scan']);

Route::post('/sales', [\App\Http\Controllers\Api\SalesApiController::class, 'store']);
Route::get('/sales/{id}', [\App\Http\Controllers\Api\SalesApiController::class, 'show']);
Route::get('/sales', [\App\Http\Controllers\Api\SalesApiController::class, 'index']);

Route::post('/cart/add', [CartApiController::class, 'add']);
Route::get('/cart/{device_id}', [CartApiController::class, 'show']);
Route::delete('/cart/item/{id}', [CartApiController::class, 'remove']);
Route::delete('/cart/{device_id}', [CartApiController::class, 'clear']);


Route::post('/cart/checkout', [CartApiController::class, 'checkout']);

Route::post('/midtrans/callback', [PaymentController::class, 'callback']);
Route::post('/cart/update-qty', [CartApiController::class, 'updateQty']);
Route::post('/cart/checkout-midtrans', [CartApiController::class, 'checkoutMidtrans']);

