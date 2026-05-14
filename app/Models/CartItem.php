<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class CartItem extends Model
{
        public function Cart() :BelongsTo {
        return $this->belongsTo(Cart::class);
    }    
        public function Product() :BelongsTo {
        return $this->belongsTo(Product::class);
    }    
}
