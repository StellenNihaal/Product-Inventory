<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens,HasFactory, Notifiable;

    /**
     * SAFE fillable — role and is_active are intentionally excluded.
     * These must only be set via explicit assignment:
     *   $user->role = 'admin';
     *   $user->is_active = false;
     *   $user->save();
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo',
        'country',
        'state',
        // ❌ role     — never mass-assignable
        // ❌ is_active — never mass-assignable
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // Relationships
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}