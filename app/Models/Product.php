<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Product extends Model
{

    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'description',
        'price',
        'image',
        'stock',
    ];

    
        public function category() :BelongsTo {
        return $this->belongsTo(Category::class);
    }

        public function brand() :BelongsTo {
        return $this->belongsTo(Category::class);
    }

         public function cartItem():HasMany {
        return $this->hasMany(CartItem::class);
    }
         public function orderItem():HasMany {
        return $this->hasMany(OrderItem::class);
    }
        public function review():HasMany {
        return $this->hasMany(Review::class);
    }
}
