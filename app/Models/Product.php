<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['barcode', 'name', 'price', 'stock', 'unit', 'description', 'image', 'is_active'];

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockHistory()
    {
        return $this->hasMany(StockHistory::class);
    }
}
