<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggleFavorite($appartementId)
{
    $user = auth()->user();

    $exists = $user->favoriteAppartements()->where('appartement_id', $appartementId)->exists();

    if ($exists) {
        $user->favoriteAppartements()->detach($appartementId);

        return response()->json([
            'status' => 'success',
            'message' => 'Removed from favorites'
        ]);
    }

    $user->favoriteAppartements()->attach($appartementId);

    return response()->json([
        'status' => 'success',
        'message' => 'Added to favorites'
    ]);
}

//
    public function myFavorites()
{
    $today = now();

    $user = auth()->user();

    $favorites = $user->favoriteAppartements()->with([
        'owner',
        'bookings' => function ($query) use ($today) {
            $query->where('status', 'booked')
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today);
        },
        'bookings.user'
    ])->get();

    return response()->json([
        'status' => 'success',
        'data'   => $favorites
    ]);
}


  
}
