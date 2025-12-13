<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // Needed for login/auth
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'username',
        'password',
    ];

   // protected $hidden = [
     //   'password',
   // ];
}