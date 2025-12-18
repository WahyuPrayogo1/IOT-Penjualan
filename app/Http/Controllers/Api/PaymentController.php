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
    public function callback(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');

        // Validate signature
        $signature = hash('sha512', 
            $request->order_id . 
            $request->status_code . 
            $request->gross_amount . 
            $serverKey
        );

        if ($signature !== $request->signature_key) {
            return response()->json(['success' => false, 'message' => 'Invalid signature'], 403);
        }

        // Ambil sales berdasarkan invoice
        $sale = Sales::where('invoice_number', $request->order_id)->first();

        if (!$sale) {
            return response()->json(['success' => false, 'message' => 'Sale not found'], 404);
        }

        // Jika sukses dari Midtrans
        if ($request->transaction_status === 'capture' || $request->transaction_status === 'settlement') {

            // Update sales
            $sale->update([
                'status' => 'success',
                'paid_amount' => $sale->total_amount,
                'change_amount' => 0,
            ]);

            // Ambil cart berdasarkan device_id awal
            $cart = Cart::where('device_id', $sale->device_id)->with('items.product')->first();

            if ($cart) {
                foreach ($cart->items as $ci) {

                    // Buat sales item
                    SalesItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $ci->product_id,
                        'quantity' => $ci->quantity,
                        'price' => $ci->product->price,
                        'subtotal' => $ci->product->price * $ci->quantity,
                    ]);

                    // Kurangi stok
                    $ci->product->decrement('stock', $ci->quantity);

                    // Catat history stok
                    StockHistory::create([
                        'product_id' => $ci->product_id,
                        'type' => 'sale',
                        'quantity' => $ci->quantity,
                        'description' => 'Midtrans Payment',
                        'reference_id' => $sale->id,
                    ]);
                }

                // Bersihkan cart
                $cart->items()->delete();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Callback processed'
        ]);
    }
}
