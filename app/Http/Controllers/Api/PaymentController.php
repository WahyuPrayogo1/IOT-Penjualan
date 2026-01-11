<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sales;
use App\Models\Cart;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
public function checkStatus($invoice)
{
    $sale = Sales::where('invoice_number', $invoice)->first();
    
    if (!$sale) {
        return response()->json([
            'success' => false,
            'message' => 'Invoice not found: ' . $invoice
        ], 404);
    }
    
    $response = [
        'success' => true,
        'invoice' => $invoice,
        'status' => $sale->status,
        'payment_method' => $sale->payment_method,
        'total' => (float)$sale->total_amount,
        'device_id' => $sale->device_id,
        'created_at' => $sale->created_at ? $sale->created_at->format('Y-m-d H:i:s') : null,
    ];
    
    // **TAMBAHKAN DATA MIDTRANS JIKA ADA**
    $midtransData = $sale->midtrans_data ? json_decode($sale->midtrans_data, true) : [];
    
    if ($sale->payment_method == 'midtrans') {
        $snapToken = $midtransData['snap_token'] ?? null;
        
        $response['midtrans'] = [
            'has_token' => !empty($snapToken),
            'transaction_id' => $sale->midtrans_transaction_id,
            'payment_type' => $sale->midtrans_payment_type,
            'data_available' => !empty($midtransData),
        ];
        
        // Hanya berikan payment_url jika status masih pending dan ada token
        if ($sale->status == 'pending' && $snapToken) {
            $response['payment_url'] = "https://app.sandbox.midtrans.com/snap/v2/vtweb/" . $snapToken;
            $response['snap_token'] = $snapToken;
            
            // Hitung waktu kadaluarsa (24 jam dari created_at)
            if ($sale->created_at) {
                $expiredAt = $sale->created_at->addHours(24);
                $response['payment_expired'] = $expiredAt->format('Y-m-d H:i:s');
                $response['payment_expired_timestamp'] = $expiredAt->timestamp;
                
                // Tambah status expired jika melewati batas waktu
                if (now()->greaterThan($expiredAt)) {
                    $response['payment_status'] = 'expired';
                } else {
                    $response['payment_status'] = 'active';
                }
            }
        }
    }
    
    // Tambah data berdasarkan status
    if ($sale->status == 'completed') {
        $response['paid_at'] = $sale->paid_at ? $sale->paid_at->format('Y-m-d H:i:s') : null;
        $response['paid_amount'] = (float)$sale->paid_amount;
        $response['change_amount'] = (float)$sale->change_amount;
        
        // Tambah success page URL
        $response['success_url'] = url('/payment/success?invoice=' . $invoice);
        
    } elseif ($sale->status == 'failed') {
        $response['failed_at'] = $sale->failed_at ? $sale->failed_at->format('Y-m-d H:i:s') : null;
        $response['reason'] = 'Payment failed';
        
    } elseif ($sale->status == 'pending') {
        $response['pending_since'] = $sale->created_at ? $sale->created_at->format('Y-m-d H:i:s') : null;
        $response['instruction'] = 'Please complete the payment using the provided payment URL';
    }
    
    // Cek cart
    if ($sale->cart_id) {
        $cart = Cart::find($sale->cart_id);
        if ($cart) {
            $response['cart'] = [
                'id' => $cart->id,
                'is_locked' => (bool)$cart->is_locked,
                'items_count' => $cart->items()->count(),
                'locked_status' => $cart->is_locked ? 'Cart is locked during payment' : 'Cart is active'
            ];
        }
    }
    
    return response()->json($response);
} 
    // List all pending payments for a device
    public function listPendingPayments($device_id)
    {
        $pendingSales = Sales::where('device_id', $device_id)
                            ->where('status', 'pending')
                            ->orderBy('created_at', 'desc')
                            ->get(['invoice_number', 'total_amount', 'created_at']);
        
        return response()->json([
            'success' => true,
            'device_id' => $device_id,
            'pending_count' => $pendingSales->count(),
            'pending_payments' => $pendingSales
        ]);
    }
    

public function cancelPayment(Request $request)
{
    $request->validate([
        'sale_id' => 'required|exists:sales,id',
        'invoice' => 'required'
    ]);
    
    $sale = Sales::find($request->sale_id);
    
    if ($sale->status !== 'pending') {
        return response()->json([
            'success' => false,
            'message' => 'Only pending payments can be cancelled'
        ], 400);
    }
    
    // Update sale status
    $sale->update([
        'status' => 'cancelled',
        'failed_at' => now()
    ]);
    
    // Unlock cart jika ada
    if ($sale->cart_id) {
        $cart = Cart::find($sale->cart_id);
        if ($cart) {
            $cart->update(['is_locked' => false]);
        }
    }
    
    return response()->json([
        'success' => true,
        'message' => 'Payment cancelled successfully',
        'invoice' => $sale->invoice_number
    ]);
}
}