<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property numeric $price
 * @property numeric|null $rating
 * @property array<array-key, mixed>|null $images
 * @property int $space
 * @property int $rooms
 * @property int $floor
 * @property string $city
 * @property string $area
 * @property string|null $address
 * @property int $user_id
 * @property bool $available
 * @property string $approval_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $favoredByUsers
 * @property-read int|null $favored_by_users_count
 * @property-read \App\Models\User $owner
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $raters
 * @property-read int|null $raters_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appartement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appartement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appartement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appartement whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appartement whereApprovalStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appartement whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appartement whereAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appartement whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appartement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appartement whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appartement whereFloor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appartement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appartement whereImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appartement wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appartement whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appartement whereRooms($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appartement whereSpace($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appartement whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appartement whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appartement whereUserId($value)
 * @mixin \Eloquent
 */
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
        'approval_status'
    ];

    protected $casts = [
        'images'    => 'array',     // JSON → array
        'price'     => 'decimal:2', // exact money
        'rating'    => 'decimal:1', // rating out of 5
        'available' => 'boolean',
    ];


    public function isAvailable(): bool
    {
        $today = now();

        $activeBooking = $this->bookings()
            ->where('status', 'booked')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->exists();

        return !$activeBooking;
    }

    // Relations
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
      public function favoredByUsers()
    {
        return $this->belongsToMany(User::class, 'favorites', 'appartement_id', 'user_id');
    }
public function raters()
{
    return $this->belongsToMany(User::class, 'appartement_user_ratings')
                ->withPivot('rating')  
                ->withTimestamps();       
}

}
