<?php

namespace App\Http\Controllers;

use App\Models\Appartement;
use App\Models\Booking;
use App\Notifications\NewBookingNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function store(Request $request, $appartementId)
    {
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after:start_date',
        ]);

        $appartement = Appartement::findOrFail($appartementId);

        // 👉 Prevent owner from booking their own appartement
        if ($appartement->owner->id === Auth::id()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'You cannot book your own appartement.'
            ], 403);
        }

        $startDate  = Carbon::parse($request->start_date);
        $endDate    = Carbon::parse($request->end_date);
        $days       = $startDate->diffInDays($endDate);
        $totalPrice = $days * $appartement->price;

        $booking = Booking::create([
            'user_id'        => Auth::id(),
            'appartement_id' => $appartement->id,
            'start_date'     => $request->start_date,
            'end_date'       => $request->end_date,
            'status'         => 'pending',
            'total_price'    => $totalPrice,
        ]);

        $booking->load(['appartement.owner', 'user']);

        // Notify the owner
        $appartement->owner->notify(new NewBookingNotification($booking));

        return response()->json([
            'message' => 'Booking request submitted, waiting for owner approval',
            'booking' => $booking,
        ], 201);
    }
    public function myBookings()
    {
        $bookings = Booking::with('appartement')
            ->where('user_id', Auth::id())
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $bookings
        ], 200);
    }
}
