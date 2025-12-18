<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartApiController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'device_id' => 'required',
            'barcode' => 'required',
        ]);

        // 1. Cek product berdasarkan barcode
        $product = Product::where('barcode', $request->barcode)->first();

        if (!$product) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Product not found',
                ],
                404,
            );
        }

        // 2. Cari cart berdasarkan device_id
        $cart = Cart::firstOrCreate([
            'device_id' => $request->device_id,
        ]);

        // 3. Apakah product sudah ada di cart?
        $item = CartItem::where('cart_id', $cart->id)->where('product_id', $product->id)->first();

        if ($item) {
            // Jika sudah ada, tambahkan quantity
            $item->increment('quantity');
        } else {
            // Jika belum ada, tambahkan item baru
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => 1,
            ]);
        }

        // 4. Return data cart terbaru
        return response()->json([
            'success' => true,
            'message' => 'Item added to cart',
            'data' => $cart->load('items.product'),
        ]);
    }

    public function show($device_id)
    {
        $cart = Cart::where('device_id', $device_id)->with('items.product')->first();

        if (!$cart) {
            return response()->json([
                'success' => true,
                'items' => [],
                'total' => 0,
            ]);
        }

        $items = [];
        $total = 0;

        foreach ($cart->items as $item) {
            $subtotal = $item->product->price * $item->quantity;
            $total += $subtotal;

            $items[] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'barcode' => $item->product->barcode,
                'price' => $item->product->price,
                'quantity' => $item->quantity,
                'subtotal' => $subtotal,
            ];
        }

        return response()->json([
            'success' => true,
            'items' => $items,
            'total' => $total,
        ]);
    }

    public function remove($id)
    {
        $item = CartItem::find($id);

        if (!$item) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Item not found',
                ],
                404,
            );
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed',
        ]);
    }

    public function clear($device_id)
    {
        $cart = Cart::where('device_id', $device_id)->first();

        if ($cart) {
            $cart->items()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared',
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'device_id' => 'required',
            'payment_method' => 'required|in:cash,midtrans',
            'paid_amount' => 'required_if:payment_method,cash',
        ]);

        $cart = Cart::where('device_id', $request->device_id)->with('items.product')->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Cart is empty'], 400);
        }

        // Hitung total
        $total = 0;
        foreach ($cart->items as $item) {
            $total += $item->product->price * $item->quantity;
        }

        // Buat sales dulu (midtrans = pending)
        $sale = Sale::create([
            'invoice_number' => 'INV-' . time(),
            'customer_name' => $request->customer_name,
            'total_amount' => $total,
            'payment_method' => $request->payment_method,
            'paid_amount' => $request->payment_method == 'cash' ? $request->paid_amount : 0,
            'change_amount' => $request->payment_method == 'cash' ? $request->paid_amount - $total : 0,
            'status' => $request->payment_method == 'cash' ? 'success' : 'pending',
            'created_by' => auth()->id() ?? 1,
        ]);

        // ⬇ Kalau cash → langsung proses
        if ($request->payment_method == 'cash') {
            foreach ($cart->items as $ci) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $ci->product_id,
                    'quantity' => $ci->quantity,
                    'price' => $ci->product->price,
                    'subtotal' => $ci->product->price * $ci->quantity,
                ]);

                // Kurangi stok
                $ci->product->decrement('stock', $ci->quantity);

                // Riwayat stok
                StockHistory::create([
                    'product_id' => $ci->product->id,
                    'type' => 'sale',
                    'quantity' => $ci->quantity,
                    'description' => 'Checkout by Cash',
                    'reference_id' => $sale->id,
                ]);
            }

            // Clear cart
            $cart->items()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Checkout successful',
                'data' => $sale->load('items.product'),
            ]);
        }

        // ⬇ Kalau MIDTRANS → lanjut generate Snap Token
        return $this->checkoutMidtrans($sale, $cart);
    }

    public function checkoutMidtrans($sale, $cart)
    {
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $payload = [
            'transaction_details' => [
                'order_id' => $sale->invoice_number,
                'gross_amount' => $sale->total_amount,
            ],
            'customer_details' => [
                'first_name' => $sale->customer_name ?? 'Customer',
            ],
        ];

        $snapToken = \Midtrans\Snap::getSnapToken($payload);

        return response()->json([
            'success' => true,
            'payment' => 'midtrans',
            'snap_token' => $snapToken,
            'invoice' => $sale->invoice_number,
        ]);
    }

    public function updateQty(Request $request)
    {
        $request->validate([
            'item_id' => 'required',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = CartItem::find($request->item_id);

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Item not found'], 404);
        }

        $item->update(['quantity' => $request->quantity]);

        return response()->json([
            'success' => true,
            'message' => 'Quantity updated',
        ]);
    }
}
