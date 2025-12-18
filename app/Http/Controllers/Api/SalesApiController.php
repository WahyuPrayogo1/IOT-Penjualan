<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sales;
use App\Models\SalesItem;
use App\Models\StockHistory;
use Illuminate\Http\Request;

class SalesApiController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Sales::with('items.product')->latest()->get()
        ]);
    }

    public function show($id)
    {
        $sale = Sales::with('items.product')->find($id);

        if (!$sale) {
            return response()->json([
                'success' => false,
                'message' => 'Sale not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $sale
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'nullable|string',
            'payment_method' => 'required',
            'paid_amount' => 'required|numeric',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1'
        ]);

        // Hitung total
        $total = 0;
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            $total += $product->price * $item['quantity'];
        }

        // Buat Sales
        $sale = Sales::create([
            'invoice_number' => 'INV-' . time(),
            'customer_name' => $request->customer_name,
            'payment_method' => $request->payment_method,
            'total_amount' => $total,
            'paid_amount' => $request->paid_amount,
            'change_amount' => $request->paid_amount - $total,
            'created_by' => auth()->id() ?? 1, // API biasanya tidak pakai login
        ]);

        // Buat item dan kurangi stok
        foreach ($request->items as $item) {

            $product = Product::find($item['product_id']);

            // Simpan sale item
            SalesItem::create([
                'sale_id' => $sale->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->price,
                'subtotal' => ($product->price * $item['quantity']),
            ]);

            // Kurangi stok product
            $product->decrement('stock', $item['quantity']);

            // Tambah stock history
            StockHistory::create([
                'product_id' => $product->id,
                'type' => 'sale',
                'quantity' => $item['quantity'],
                'description' => 'Sale via API',
                'reference_id' => $sale->id
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sale created successfully',
            'data' => $sale->load('items.product')
        ]);
    }
}
