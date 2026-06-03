<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Bookmark;
use App\Models\Tag;
use App\Models\Collection;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\CustomVerifyEmail;
use App\Notifications\ResetPasswordEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'google_id',
        'avatar',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'integer',
        ];
    }

    public function isAdmin(): bool
    {
        return (int) $this->role === 1;
    }

    public function canSignInWithPassword(): bool
    {
        if ($this->hasVerifiedEmail()) {
            return true;
        }

        if ($this->google_id) {
            $this->forceFill(['email_verified_at' => $this->email_verified_at ?? now()])->save();

            return true;
        }

        return false;
    }


            public function cart()
            {
                return $this->hasOne(Cart::class);
            }

         public function Order():HasMany {
        return $this->hasMany(Order::class);
    }

         public function Review():HasMany {
        return $this->hasMany(Review::class);
    }

    /**
     * Override default email verification notification
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail());
    }

    /**
     * Override default password reset notification to use custom email
     */
    public function sendPasswordResetNotification(#[\SensitiveParameter] $token)
    {
        $this->notify(new ResetPasswordEmail($token));
    }
}
