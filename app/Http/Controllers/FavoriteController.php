<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function store($appartementId)
    {
        $favorite = Favorite::firstOrCreate([
            'user_id'       => Auth::id(),
            'appartement_id' => $appartementId,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Appartement added to favorites',
            'data'   => $favorite
        ]);
    }

    public function index()
    {
        $favorites = Favorite::with('appartement')
            ->where('user_id', Auth::id())
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $favorites
        ]);
    }

    public function destroy($appartementId)
    {
        Favorite::where('user_id', Auth::id())
            ->where('appartement_id', $appartementId)
            ->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Appartement removed from favorites'
        ]);
    }
}
