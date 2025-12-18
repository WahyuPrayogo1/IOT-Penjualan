<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::query();

            return DataTables::of($products)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <a href="'.route('products.edit', $row->id).'" class="btn btn-sm btn-warning">Edit</a>
                        <form action="'.route('products.destroy', $row->id).'" method="POST" style="display:inline-block">
                            '.csrf_field().method_field('DELETE').'
                            <button class="btn btn-sm btn-danger" onclick="return confirm(\'Delete this?\')">Delete</button>
                        </form>
                    ';
                })->addColumn('price', function ($row) {
    return "Rp " . number_format($row->price, 0, ',', '.');
})
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('backend.products.index');
    }

    public function create()
    {
        return view('backend.products.form', ['isEdit' => false]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'barcode' => 'required|unique:products,barcode',
            'name' => 'required',
            'price' => 'required',
            'stock' => 'required|numeric',
            'image' => 'image|nullable'
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('uploads/products', 'public');
        }

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    public function edit(Product $product)
    {
        return view('backend.products.form', [
            'product' => $product,
            'isEdit' => true
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'barcode' => 'required|unique:products,barcode,'.$product->id,
            'name' => 'required',
            'price' => 'required',
            'stock' => 'required|numeric',
            'image' => 'image|nullable'
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('uploads/products', 'public');
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted.');
    }
}
