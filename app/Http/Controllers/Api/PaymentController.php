<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sales;
use App\Models\SalesItem;
use App\Models\StockHistory;
use App\Models\Cart;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
// PaymentController.php
public function callback(Request $request)
{
    $serverKey = env('MIDTRANS_SERVER_KEY');
    $hashed = hash("sha512", 
        $request->order_id . 
        $request->status_code . 
        $request->gross_amount . 
        $serverKey
    );

    if ($hashed != $request->signature_key) {
        return response()->json(['message' => 'Invalid signature'], 401);
    }

    $transactionStatus = $request->transaction_status;
    $orderId = $request->order_id;

    // Cari sale berdasarkan invoice_number
    $sale = Sales::where('invoice_number', $orderId)->first();

    if (!$sale) {
        return response()->json(['message' => 'Sale not found'], 404);
    }

    if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
        // Pembayaran sukses
        $sale->update([
            'status' => 'success',
            'paid_amount' => $request->gross_amount,
        ]);

        // Kurangi stok produk
        $saleItems = SalesItem::where('sale_id', $sale->id)->get();
        
        foreach ($saleItems as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->decrement('stock', $item->quantity);
                
                StockHistory::create([
                    'product_id' => $product->id,
                    'type' => 'sale',
                    'quantity' => $item->quantity,
                    'description' => 'Midtrans Payment: ' . $orderId,
                    'reference_id' => $sale->id,
                ]);
            }
        }

        // Clear cart berdasarkan device_id
        $cart = Cart::where('device_id', $sale->device_id)->first();
        if ($cart) {
            $cart->items()->delete();
        }

        return response()->json(['message' => 'Payment success']);

    } elseif ($transactionStatus == 'pending') {
        $sale->update(['status' => 'pending']);
        return response()->json(['message' => 'Payment pending']);

    } elseif ($transactionStatus == 'deny' || $transactionStatus == 'cancel' || $transactionStatus == 'expire') {
        $sale->update(['status' => 'failed']);
        return response()->json(['message' => 'Payment failed']);
    }

    return response()->json(['message' => 'Unknown status']);
}
}
