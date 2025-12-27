<?php

namespace App\Http\Controllers;

use App\Models\Appartement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function rate(Request $request, $appartementId)
    {
        $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
        ]);

        $appartement = Appartement::findOrFail($appartementId);
        $user = Auth::user();

        $user->ratedAppartements()->syncWithoutDetaching([
            $appartement->id => ['rating' => $request->rating]
        ]);

        
        $avg = $appartement->raters()->avg('rating');

        $appartement->rating = $avg;
        $appartement->save();

        return response()->json([
            'status' => 'success',
            'your_rating' => $request->rating,
            'avgRating' => $appartement->rating, 
        ]);
    }

    public function myRating($appartementId)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401);
        }

        // جلب التقييم من الجدول الوسيط
        $rating = $user->ratedAppartements()
                       ->find($appartementId)?->pivot->rating;

        return response()->json([
            'status' => 'success',
            'rating' => $rating,
        ]);
    }

    public function apartmentAverage($appartementId)
    {
        $appartement = Appartement::findOrFail($appartementId);

        return response()->json([
            'status' => 'success',
            'avgRating' => $appartement->rating, 
            'ratingsCount' => $appartement->raters()->count(),
        ]);
    }
}