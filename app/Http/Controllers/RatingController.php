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

        // أضف أو حدّث التقييم في الجدول الوسيط
        $user->ratedAppartements()->syncWithoutDetaching([
            $appartement->id => ['rating' => $request->rating]
        ]);

        // حساب المتوسط من الجدول الوسيط
        $avg = $appartement->raters()->avg('rating');

        // تحديث العمود rating في جدول appartments
        $appartement->rating = $avg;
        $appartement->save();

        return response()->json([
            'status' => 'success',
            'your_rating' => $request->rating,
            'avgRating' => $appartement->rating, // نعرضه باسم avgRating لكن القيمة من العمود rating
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
            'avgRating' => $appartement->rating, // نعرضه باسم avgRating لكن القيمة من العمود rating
            'ratingsCount' => $appartement->raters()->count(),
        ]);
    }
}