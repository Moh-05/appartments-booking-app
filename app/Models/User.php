<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'full_name',
        'username',
        'phone',
        'password',
        'profile_image',
        'id_image',
        'user_date',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'user_date' => 'date',
        // 'password'  => 'hashed',
    ];

    // Relations
    public function appartements()
    {
        return $this->hasMany(Appartement::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
