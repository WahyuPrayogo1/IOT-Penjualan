<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sales;
use App\Models\SalesItem;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;


class SalesController extends Controller
{
    public function index(Request $request)
{
    if ($request->ajax()) {
        $sales = Sales::select('*');

        return DataTables::of($sales)
            ->addIndexColumn()
            ->addColumn('date', function($row){
                return $row->created_at->format('d/m/Y');
            })
            ->addColumn('action', function($row){
                return '
                    <a href="'.route('sales.show',$row->id).'" class="btn btn-sm btn-info">Detail</a>
                    <form action="'.route('sales.destroy', $row->id).'" method="POST" style="display:inline-block">
                        '.csrf_field().method_field('DELETE').'
                        <button class="btn btn-sm btn-danger" onclick="return confirm(\'Hapus?\')">Delete</button>
                    </form>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    return view('backend.sales.index');
}


    public function create()
    {
        return view('backend.sales.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'nullable|string',
            'payment_method' => 'required|string',
            'paid_amount' => 'required|numeric',
            'items' => 'required|array',
            'items.*.product_id' => 'required',
            'items.*.quantity' => 'required|numeric|min:1'
        ]);

        // Hitung total
        $total = 0;
        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);
            $total += $product->price * $item['quantity'];
        }

        // Create sale
        $sale = Sales::create([
            'invoice_number' => 'INV-' . time(),
            'customer_name' => $request->customer_name,
            'payment_method' => $request->payment_method,
            'total_amount' => $total,
            'paid_amount' => $request->paid_amount,
            'change_amount' => $request->paid_amount - $total,
            'created_by' => auth()->id()
        ]);

        // Create sale items + reduce stock
        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);

            SalesItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $product->price,
                'subtotal' => $product->price * $item['quantity'],
            ]);

            // Reduce stock
            $product->update([
                'stock' => $product->stock - $item['quantity']
            ]);

            // Create stock history
            StockHistory::create([
                'product_id' => $product->id,
                'type' => 'sale',
                'quantity' => $item['quantity'],
                'description' => 'Sale Transaction',
                'reference_id' => $sale->id
            ]);
        }

        return redirect()->route('sales.index')->with('success', 'Sale created successfully');
    }

    public function show(Sales $sale)
    {
        return view('backend.sales.show', compact('sale'));
    }

    public function destroy(Sales $sale)
    {
        // Kembalikan stok sebelum delete
        foreach ($sale->items as $item) {
            $item->product->update([
                'stock' => $item->product->stock + $item->quantity
            ]);
        }

        $sale->delete();

        return redirect()->route('sales.index')->with('success', 'Sale deleted successfully.');
    }
}
