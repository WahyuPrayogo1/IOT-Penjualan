<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
