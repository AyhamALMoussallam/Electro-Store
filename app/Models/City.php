<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
     public function Area():HasMany {
        return $this->hasMany(Area::class);
    }

         public function Order():HasMany {
        return $this->hasMany(Order::class);
    }
}

