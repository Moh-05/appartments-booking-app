<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'appartement_id',
        'user_id',
        'start_date',
        'end_date',
        'status',
        'total_price',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'total_price'=> 'decimal:2',
    ];

    // Relations
    public function appartement()
    {
        return $this->belongsTo(Appartement::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}