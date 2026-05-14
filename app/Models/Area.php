<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Area extends Model
{
            public function City() :BelongsTo {
        return $this->belongsTo(City::class);
    }
}
