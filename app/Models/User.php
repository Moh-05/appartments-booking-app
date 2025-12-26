<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $full_name
 * @property string $username
 * @property string $phone
 * @property string $password
 * @property string $profile_image
 * @property string $id_image
 * @property \Illuminate\Support\Carbon $user_date
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Appartement> $appartements
 * @property-read int|null $appartements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Appartement> $favoriteAppartements
 * @property-read int|null $favorite_appartements_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Appartement> $ratedAppartements
 * @property-read int|null $rated_appartements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIdImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfileImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUserDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 * @mixin \Eloquent
 */
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
   
      public function favoriteAppartements()
    {
        return $this->belongsToMany(Appartement::class, 'favorites', 'user_id', 'appartement_id');
    }

public function ratedAppartements()
{
    return $this->belongsToMany(Appartement::class, 'appartement_user_ratings')
                ->withPivot('rating')
                ->withTimestamps();
}

}
