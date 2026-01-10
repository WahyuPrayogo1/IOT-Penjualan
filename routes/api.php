<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CartApiController;
use App\Http\Controllers\Api\ConfigApiController;


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/products/scan/{barcode}', [\App\Http\Controllers\Api\ProductApiController::class, 'scan']);

Route::post('/sales', [\App\Http\Controllers\Api\SalesApiController::class, 'store']);
Route::get('/sales/{id}', [\App\Http\Controllers\Api\SalesApiController::class, 'show']);
Route::get('/sales', [\App\Http\Controllers\Api\SalesApiController::class, 'index']);

    // Cart API Routes
    Route::prefix('cart')->group(function () {
        // Get cart by device_id
        Route::get('/{device_id}', [CartApiController::class, 'show']);
        
        // Add item (from IOT scanner)
        Route::post('/add', [CartApiController::class, 'add']);
        
        // Update quantity (from mobile)
        Route::post('/update-quantity', [CartApiController::class, 'updateQuantity']);
        
        // Remove item - HARUS dengan device_id validasi
        Route::post('/remove-item', [CartApiController::class, 'remove']);

        // Clear cart - spesifik route
        Route::post('/clear', [CartApiController::class, 'clear']);
        
        // Pembayaran Cash dan Midtrans
            Route::post('checkout', [CartApiController::class, 'checkout']); // Untuk CASH
            Route::post('checkout/midtrans', [CartApiController::class, 'checkoutMidtrans']); // Untuk MIDTRANS
        // End Pembayaran Cash dan Midtrans
    });


Route::get('/config/version', [ConfigApiController::class, 'version']);
Route::get('/config', [ConfigApiController::class, 'index']);
