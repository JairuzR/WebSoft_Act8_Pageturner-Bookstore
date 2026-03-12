<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Helper method
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

        public function isVerified()
    {
        return !is_null($this->email_verified_at);
    }

    // Add relationship
    public function twoFactorSecret()
    {
        return $this->hasOne(TwoFactorSecret::class);
    }

    // Check if 2FA is enabled
    public function hasTwoFactorEnabled()
    {
        return $this->twoFactorSecret && $this->twoFactorSecret->enabled;
    }
}