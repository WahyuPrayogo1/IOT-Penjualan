<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;
    protected $fillable = ['invoice_number', 'customer_name', 'total_amount', 'payment_method', 'paid_amount', 'change_amount', 'created_by'];

public function items()
{
    return $this->hasMany(SalesItem::class, 'sale_id'); // ← tambahkan 'sale_id'
}

}
