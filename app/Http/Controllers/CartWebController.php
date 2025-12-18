<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartWebController extends Controller
{
    // Tampilkan semua cart
    public function index()
    {
        $carts = Cart::with('items.product')->get();

        return view('backend.cart.index', compact('carts'));
    }

    // Detail 1 cart berdasarkan IoT device
    public function detail($device_id)
    {
        $cart = Cart::where('device_id', $device_id)
                    ->with('items.product')
                    ->first();

        return view('backend.cart.detail', compact('cart', 'device_id'));
    }
}
