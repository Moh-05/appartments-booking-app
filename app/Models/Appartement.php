<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appartement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'price',
        'rating',
        'images',
        'space',
        'rooms',
        'floor',
        'city',
        'area',
        'address',
        'user_id',
        'available',
    ];

    protected $casts = [
        'images'    => 'array',     // JSON → array
        'price'     => 'decimal:2', // exact money
        'rating'    => 'decimal:1', // rating out of 5
        'available' => 'boolean',
    ];

    // Relations
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}