<?php

namespace App\Http\Controllers;

use App\Models\StockHistory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StockHistoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $history = StockHistory::with('product')->latest();

            return DataTables::of($history)
                ->addIndexColumn()
                ->addColumn('product', function($row) {
                    return $row->product->name ?? '-';
                })
                ->addColumn('type', function($row) {
                    return strtoupper($row->type);
                })
                ->addColumn('quantity', function($row) {
                    return number_format($row->quantity);
                })
                ->addColumn('date', function($row) {
                    return $row->created_at->format('d/m/Y H:i');
                })
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-sm btn-secondary" disabled>No Action</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('backend.stock_history.index');
    }
}
