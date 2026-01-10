<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CartApiController;
use App\Http\Controllers\Api\ConfigApiController;
use App\Http\Controllers\Api\PaymentController;


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/products/scan/{barcode}', [\App\Http\Controllers\Api\ProductApiController::class, 'scan']);

Route::post('/sales', [\App\Http\Controllers\Api\SalesApiController::class, 'store']);
Route::get('/sales/{id}', [\App\Http\Controllers\Api\SalesApiController::class, 'show']);
Route::get('/sales', [\App\Http\Controllers\Api\SalesApiController::class, 'index']);


// Midtrans callback (harus public, tidak pakai auth)
Route::post('/midtrans/callback', [PaymentController::class, 'midtransCallback']);


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
            Route::post('/checkout', [CartApiController::class, 'checkout']);
            Route::post('/checkout-midtrans', [CartApiController::class, 'checkoutMidtrans']);
        // End Pembayaran Cash dan Midtrans
    });

    // Payment routes
    Route::prefix('payment')->group(function () {
        Route::get('/status/{invoice}', [PaymentController::class, 'checkStatus']);
        Route::get('/pending/{device_id}', [PaymentController::class, 'listPendingPayments']);
        Route::post('/cancel', [PaymentController::class, 'cancelPayment']);
    });
    Route::post('/midtrans/callback', [App\Http\Controllers\Api\CartApiController::class, 'midtransCallback']);


Route::get('/config/version', [ConfigApiController::class, 'version']);
Route::get('/config', [ConfigApiController::class, 'index']);
