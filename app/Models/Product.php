<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Product extends Model
{
    
        public function Category() :BelongsTo {
        return $this->belongsTo(Category::class);
    }

         public function CartItem():HasMany {
        return $this->hasMany(CartItem::class);
    }
         public function OrderItem():HasMany {
        return $this->hasMany(OrderItem::class);
    }
}
