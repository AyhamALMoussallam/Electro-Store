<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Order extends Model
{

    protected $fillable = [
        'user_id',
        'area_id',
        'total_price',
        'status',
        'note',
    ];

             public function items()
        {
            return $this->hasMany(OrderItem::class);
        }

            public function User() :BelongsTo {
        return $this->belongsTo(User::class);
    }
            public function Area() :BelongsTo {
        return $this->belongsTo(Area::class);
    }

    public function logs()
{
    return $this->hasMany(OrderLog::class);
}

    
}
