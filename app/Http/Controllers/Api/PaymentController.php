<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sales;
use App\Models\Cart;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // Check payment status
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
            'created_at' => $sale->created_at->format('Y-m-d H:i:s'),
        ];
        
        // Tambah data berdasarkan status
        if ($sale->status == 'completed') {
            $response['paid_at'] = $sale->paid_at->format('Y-m-d H:i:s');
            $response['paid_amount'] = (float)$sale->paid_amount;
            $response['change_amount'] = (float)$sale->change_amount;
        } elseif ($sale->status == 'failed') {
            $response['failed_at'] = $sale->failed_at->format('Y-m-d H:i:s');
            $response['reason'] = 'Payment failed';
        }
        
        // Cek cart
        if ($sale->cart_id) {
            $cart = Cart::find($sale->cart_id);
            if ($cart) {
                $response['cart'] = [
                    'id' => $cart->id,
                    'is_locked' => (bool)$cart->is_locked,
                    'items_count' => $cart->items()->count()
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
    
    // Cancel pending payment
    public function cancelPayment(Request $request)
    {
        $request->validate([
            'invoice' => 'required',
            'device_id' => 'required'
        ]);
        
        $sale = Sales::where('invoice_number', $request->invoice)
                    ->where('device_id', $request->device_id)
                    ->first();
        
        if (!$sale) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found'
            ], 404);
        }
        
        if ($sale->status != 'pending') {
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
        
        // Unlock cart
        if ($sale->cart_id) {
            Cart::where('id', $sale->cart_id)->update(['is_locked' => false]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Payment cancelled',
            'invoice' => $request->invoice,
            'new_status' => 'cancelled'
        ]);
    }
}