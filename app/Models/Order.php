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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
            public function Area() :BelongsTo {
        return $this->belongsTo(Area::class);
    }

    public function logs()
{
    return $this->hasMany(OrderLog::class);
}

    /**
     * Customer-facing order number (1-based per user, by order date).
     */
    public function userOrderNumber(): int
    {
        $position = static::query()
            ->where('user_id', $this->user_id)
            ->orderBy('created_at')
            ->orderBy('id')
            ->pluck('id')
            ->search($this->id);

        return $position === false ? 1 : $position + 1;
    }
}
