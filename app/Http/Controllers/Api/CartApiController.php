<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Sales;
use App\Models\SalesItem;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Midtrans\Snap;
use Midtrans\Config;

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
            'device_id' => $device_id,  // 👈 TAMBAH INI
            'items' => [],
            'total' => 0,
            'cart_id' => null,
            'message' => 'Cart is empty'
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
            'product_image' => $item->product->image ?? null, // optional
        ];
    }

    return response()->json([
        'success' => true,
        'device_id' => $device_id,      // 👈 TAMBAH INI
        'cart_id' => $cart->id,         // 👈 TAMBAH INI (jika perlu)
        'items' => $items,
        'total' => $total,
        'items_count' => count($items), // 👈 TAMBAH INI
        'message' => 'Cart retrieved successfully'
    ]);
}

public function remove(Request $request)
{
    $request->validate([
        'device_id' => 'required|string',
        'item_id' => 'required|integer|min:1',
    ]);
    
    // Cari cart berdasarkan device_id
    $cart = Cart::where('device_id', $request->device_id)->first();
    
    if (!$cart) {
        return response()->json([
            'success' => false,
            'message' => 'Cart not found for this device'
        ], 404);
    }
    
    // Cari item yang ada di cart tersebut
    $item = CartItem::where('id', $request->item_id)
                    ->where('cart_id', $cart->id)
                    ->with('product') // Tambah with('product')
                    ->first();
    
    if (!$item) {
        return response()->json([
            'success' => false,
            'message' => 'Item not found in your cart'
        ], 404);
    }
    
    // ❌❌❌ HAPUS INI! Stok belum pernah dikurangi!
    // $item->product->increment('stock', $item->quantity);
    
    // Simpan info sebelum dihapus
    $productName = $item->product->name;
    
    // Hapus item
    $item->delete();
    
    return response()->json([
        'success' => true,
        'message' => 'Item removed from cart',
        'device_id' => $request->device_id,
        'removed_item_id' => $request->item_id,
        'product_name' => $productName,
        'removed_quantity' => $item->quantity // Tambah info
    ]);
}

public function clear(Request $request)
{
    $request->validate([
        'device_id' => 'required|string',
    ]);
    
    $device_id = $request->device_id;
    $cart = Cart::where('device_id', $device_id)->with('items.product')->first();

    if (!$cart) {
        return response()->json([
            'success' => true,
            'device_id' => $device_id,
            'message' => 'Cart already empty or not found'
        ]);
    }

    $itemsCount = $cart->items()->count();
    $cart->items()->delete();

    return response()->json([
        'success' => true,
        'device_id' => $device_id,
        'message' => 'Cart cleared',
        'removed_items_count' => $itemsCount
    ]);
}

public function updateQuantity(Request $request)
{
    // 1. VALIDASI INPUT
    $request->validate([
        'device_id' => 'required|string',
        'item_id' => 'required|integer|min:1',
        'quantity' => 'required|integer|min:0|max:9999', // Tambah max jadi 9999
    ]);
    
    // 2. CARI CART
    $cart = Cart::where('device_id', $request->device_id)->first();
    
    if (!$cart) {
        return response()->json([
            'success' => false,
            'message' => 'Cart tidak ditemukan'
        ], 404);
    }
    
    // 3. CARI ITEM DENGAN PRODUCT
    $item = CartItem::where('id', $request->item_id)
                    ->where('cart_id', $cart->id)
                    ->with('product')
                    ->first();
    
    if (!$item) {
        return response()->json([
            'success' => false,
            'message' => 'Item tidak ditemukan di keranjang Anda'
        ], 404);
    }
    
    $oldQuantity = $item->quantity;
    $newQuantity = $request->quantity;
    $product = $item->product;
    
    // 4. KASUS: QUANTITY JADI 0 → HAPUS ITEM
    if ($newQuantity === 0) {
        $item->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Item dihapus dari keranjang',
            'device_id' => $request->device_id,
            'item_id' => $item->id,
            'product_name' => $product->name,
            'old_quantity' => $oldQuantity,
            'new_quantity' => 0,
            'action' => 'removed',
            'subtotal' => 0,
            'timestamp' => now()->toDateTimeString()
        ]);
    }
    
    // 5. VALIDASI STOK UNTUK SEMUA KASUS
    
    // 🔴🔴🔴 PERHITUNGAN STOK YANG BENAR 🔴🔴🔴
    // Stok tersedia = Stok total di database
    $availableStock = $product->stock;
    
    // Jika quantity baru > stok tersedia, TOLAK!
    if ($newQuantity > $availableStock) {
        return response()->json([
            'success' => false,
            'message' => 'Stok tidak cukup',
            'details' => [
                'requested_quantity' => $newQuantity,
                'available_stock' => $availableStock,
                'current_in_cart' => $oldQuantity,
                'can_add_max' => $availableStock, // Maksimal yang bisa ditambah
                'reason' => 'Requested quantity exceeds available stock'
            ]
        ], 400);
    }
    
    // 6. TENTUKAN ACTION TYPE
    if ($newQuantity > $oldQuantity) {
        $actionType = 'increased';
        $change = '+' . ($newQuantity - $oldQuantity);
        $changeDescription = "Ditambah " . ($newQuantity - $oldQuantity);
    } elseif ($newQuantity < $oldQuantity) {
        $actionType = 'decreased';
        $change = '-' . ($oldQuantity - $newQuantity);
        $changeDescription = "Dikurangi " . ($oldQuantity - $newQuantity);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Quantity tidak berubah'
        ], 400);
    }
    
    // 7. UPDATE QUANTITY DI DATABASE
    $item->update(['quantity' => $newQuantity]);
    
    // 8. HITUNG SUBTOTAL
    $oldSubtotal = $product->price * $oldQuantity;
    $newSubtotal = $product->price * $newQuantity;
    $subtotalChange = $newSubtotal - $oldSubtotal;
    
    // 9. RETURN RESPONSE SUKSES
    return response()->json([
        'success' => true,
        'message' => 'Quantity berhasil diupdate',
        'device_id' => $request->device_id,
        'cart_id' => $cart->id,
        'item_id' => $item->id,
        'product' => [
            'id' => $product->id,
            'name' => $product->name,
            'barcode' => $product->barcode,
            'price' => (int)$product->price,
            'stock' => (int)$product->stock,
            'unit' => $product->unit ?? 'pcs',
        ],
        'quantity' => [
            'old' => (int)$oldQuantity,
            'new' => (int)$newQuantity,
            'change' => $change,
            'change_description' => $changeDescription,
        ],
        'subtotal' => [
            'old' => (int)$oldSubtotal,
            'new' => (int)$newSubtotal,
            'change' => (int)$subtotalChange,
            'change_formatted' => ($subtotalChange >= 0 ? '+' : '') . number_format($subtotalChange),
        ],
        'stock_info' => [
            'available' => (int)$availableStock,
            'remaining_after_update' => (int)($availableStock - $newQuantity),
            'warning' => $availableStock - $newQuantity < 10 ? 'Stok hampir habis' : null,
        ],
        'action' => $actionType,
        'timestamp' => now()->toDateTimeString(),
        'notes' => 'Stok hanya dikurangi saat checkout, tidak saat update cart'
    ]);
}

public function checkout(Request $request)
{
    $request->validate([
        'device_id' => 'required',
        'payment_method' => 'required|in:cash,midtrans', // Tetap ada midtrans option
        'paid_amount' => 'required_if:payment_method,cash',
        'customer_name' => 'nullable|string',
    ]);

    // JIKA MIDTRANS → Redirect ke endpoint midtrans
    if ($request->payment_method == 'midtrans') {
        return $this->checkoutMidtrans($request);
    }

    // LOGIC CASH (tetap sama seperti sebelumnya)
    $cart = Cart::where('device_id', $request->device_id)
                ->with('items.product')
                ->first();

    if (!$cart || $cart->items->isEmpty()) {
        return response()->json(['success' => false, 'message' => 'Cart is empty'], 400);
    }

    // Hitung total
    $total = 0;
    foreach ($cart->items as $item) {
        $total += $item->product->price * $item->quantity;
    }

    // Validasi uang kurang
    if ($request->paid_amount < $total) {
        return response()->json([
            'success' => false,
            'message' => 'Jumlah yang dibayarkan tidak cukup. Harus: ' . $total,
        ], 400);
    }

    // Buat sales
    $sale = Sales::create([
        'invoice_number' => 'INV-' . time(),
        'customer_name' => $request->customer_name,
        'total_amount' => $total,
        'payment_method' => 'cash',
        'paid_amount' => $request->paid_amount,
        'change_amount' => $request->paid_amount - $total,
        'status' => 'success',
        'created_by' => auth()->id() ?? 1,
    ]);

    // Proses items dan kurangi stok
    foreach ($cart->items as $ci) {
        SalesItem::create([
            'sale_id' => $sale->id,
            'product_id' => $ci->product_id,
            'quantity' => $ci->quantity,
            'price' => $ci->product->price,
            'subtotal' => $ci->product->price * $ci->quantity,
        ]);

        $ci->product->decrement('stock', $ci->quantity);

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

public function checkoutMidtrans(Request $request)
{
    $request->validate([
        'device_id' => 'required',
        'customer_name' => 'nullable|string',
    ]);

    $cart = Cart::where('device_id', $request->device_id)
                ->with('items.product')
                ->first();

    if (!$cart || $cart->items->isEmpty()) {
        return response()->json([
            "success" => false,
            "message" => "Cart is empty"
        ], 400);
    }

    // Hitung total
    $total = 0;
    foreach ($cart->items as $item) {
        $total += $item->product->price * $item->quantity;
    }

    // Generate invoice
    $invoice = "INV-" . time();

    // 1. SIMPAN SALE STATUS PENDING
    $sale = Sales::create([
        'invoice_number' => $invoice,
        'customer_name'  => $request->customer_name,
        'payment_method' => 'midtrans',
        'total_amount'   => $total,
        'paid_amount'    => 0,
        'change_amount'  => 0,
        'status'         => 'pending', // Status pending
        'created_by'     => auth()->id() ?? null,
    ]);

    // 2. Simpan SALE ITEMS (stok jangan dikurangi dulu!)
    foreach ($cart->items as $ci) {
        SalesItem::create([
            'sale_id'    => $sale->id,
            'product_id' => $ci->product_id,
            'quantity'   => $ci->quantity,
            'price'      => $ci->product->price,
            'subtotal'   => $ci->product->price * $ci->quantity,
        ]);
    }

    // 3. Buat SNAP TOKEN Midtrans
    Config::$serverKey = env('MIDTRANS_SERVER_KEY');
    Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
    Config::$isSanitized = true;
    Config::$is3ds = true;

    $snapToken = Snap::getSnapToken([
        'transaction_details' => [
            'order_id' => $invoice,
            'gross_amount' => $total,
        ],
        'customer_details' => [
            'first_name' => $request->customer_name ?? 'Customer',
        ],
    ]);

    // 4. JANGAN CLEAR CART DULU! Tunggu callback dari Midtrans
    // $cart->items()->delete(); // ❌ JANGAN DIHAPUS

    return response()->json([
        "success"     => true,
        "message"     => "Checkout created",
        "invoice"     => $invoice,
        "total"       => $total,
        "snap_token"  => $snapToken,
        "sale_id"     => $sale->id // Tambahkan sale_id untuk reference
    ]);
}




}
