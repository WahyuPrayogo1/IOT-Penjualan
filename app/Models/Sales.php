<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'invoice_number', 'customer_name', 'device_id', 'cart_id',
        'total_amount', 'payment_method', 'paid_amount', 'change_amount',
        'status', 'paid_at', 'failed_at', 'midtrans_data',
        'midtrans_transaction_id', 'midtrans_payment_type', 'created_by'
    ];
    
    protected $casts = [
        'midtrans_data' => 'array',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'created_at' => 'datetime',  // Tambahkan ini
        'updated_at' => 'datetime',  // Tambahkan ini
        'paid_at' => 'datetime',     // Tambahkan ini
        'failed_at' => 'datetime',   // Tambahkan ini
    ];
    
    // Atau jika Anda menggunakan Laravel versi lama (< 8.x),
    // bisa juga dengan properti $dates:
    // protected $dates = ['created_at', 'updated_at', 'paid_at', 'failed_at'];
    
    public function items()
    {
        return $this->hasMany(SalesItem::class, 'sale_id');
    }
    
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
}