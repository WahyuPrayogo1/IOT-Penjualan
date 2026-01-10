<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
     protected $fillable = ['device_id', 'is_locked'];
    
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
    
    public function sale()
    {
        return $this->hasOne(Sale::class);
    }
}
