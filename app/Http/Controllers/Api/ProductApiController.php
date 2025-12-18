<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductApiController extends Controller
{
    public function scan($barcode)
    {
        // Cari berdasarkan barcode / QR code
        $product = Product::where('barcode', $barcode)->first();

        // Jika tidak ditemukan
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        // Jika ditemukan
        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }
}
