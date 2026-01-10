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
        $sales = Sales::select([
            'id',
            'invoice_number',
            'customer_name',
            'total_amount',
            'payment_method',
            'status',           // Pastikan ada kolom ini
            'device_id',        // Tambah jika perlu
            'created_at',
            'paid_at'           // Untuk info kapan dibayar
        ]);

        return DataTables::of($sales)
            ->addIndexColumn()
            ->addColumn('date', function($row){
                return $row->created_at->format('d/m/Y H:i');
            })
            ->addColumn('formatted_total', function($row){
                return 'Rp ' . number_format($row->total_amount, 0, ',', '.');
            })
           // In your DataTables code in index method:
->addColumn('status_badge', function($row){
    // Render badge status
    if ($row->status === 'completed') {
        $badge = '<span class="badge bg-success">Completed</span>';
        if ($row->paid_at) {
            // Fix: Parse the paid_at string to Carbon object before formatting
            $paidAt = \Carbon\Carbon::parse($row->paid_at);
            $badge .= '<br><small>Paid: ' . $paidAt->format('d/m/Y H:i') . '</small>';
        }
    } elseif ($row->status === 'pending') {
        $badge = '<span class="badge bg-warning">Pending</span>';
    } elseif ($row->status === 'failed') {
        $badge = '<span class="badge bg-danger">Failed</span>';
    } elseif ($row->status === 'cancelled') {
        $badge = '<span class="badge bg-secondary">Cancelled</span>';
    } else {
        $badge = '<span class="badge bg-info">' . $row->status . '</span>';
    }
    return $badge;
})
            ->addColumn('action', function($row){
                $buttons = '
                    <a href="'.route('sales.show',$row->id).'" class="btn btn-sm btn-info">Detail</a>
                ';
                
                // Tambah tombol cancel hanya untuk pending
                if ($row->status === 'pending' && $row->payment_method === 'midtrans') {
                    $buttons .= '
                        <button class="btn btn-sm btn-warning cancel-payment" 
                                data-id="'.$row->id.'" 
                                data-invoice="'.$row->invoice_number.'">
                            Cancel
                        </button>
                    ';
                }
                
                $buttons .= '
                    <form action="'.route('sales.destroy', $row->id).'" method="POST" style="display:inline-block">
                        '.csrf_field().method_field('DELETE').'
                        <button class="btn btn-sm btn-danger" onclick="return confirm(\'Hapus transaksi ini?\')">Delete</button>
                    </form>
                ';
                
                return $buttons;
            })
            ->rawColumns(['action', 'status_badge']) // TAMBAH 'status_badge' di sini
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
