<?php

namespace App\Http\Controllers;

use App\Models\Appartement;
use App\Models\Booking;
use App\Notifications\BookingStatusNotification;
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

    public function cancelBooking($bookingId)
{
    $booking = Booking::findOrFail($bookingId);

    // ✅ Ensure only the user who made the booking can cancel it
    if ($booking->user_id !== Auth::id()) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Unauthorized: You can only cancel your own booking.'
        ], 403);
    }

    $booking->status = 'cancelled';
    $booking->save();

    // If the booking was already approved, make appartement available again
    $appartement = $booking->appartement;
    if ($appartement && $booking->status === 'cancelled') {
        $appartement->available = true;
        $appartement->save();
    }

    // Notify the owner about the cancellation
    $appartement->owner->notify(new BookingStatusNotification($booking));

    return response()->json([
        'message' => 'Booking canceled successfully',
        'booking' => $booking
    ]);
}

public function updateBooking(Request $request, $bookingId)
{
    $booking = Booking::findOrFail($bookingId);

    // ✅ Ensure only the user who made the booking can update it
    if ($booking->user_id !== Auth::id()) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Unauthorized: You can only update your own booking.'
        ], 403);
    }

    $request->validate([
        'start_date' => 'required|date|after_or_equal:today',
        'end_date'   => 'required|date|after:start_date',
    ]);

    $startDate  = Carbon::parse($request->start_date);
    $endDate    = Carbon::parse($request->end_date);
    $days       = $startDate->diffInDays($endDate);
    $totalPrice = $days * $booking->appartement->price;

    $booking->start_date  = $request->start_date;
    $booking->end_date    = $request->end_date;
    $booking->total_price = $totalPrice;
    $booking->status      = 'pending'; 
    $booking->save();

    // Notify the owner about the update
    $booking->appartement->owner->notify(new NewBookingNotification($booking));

    return response()->json([
        'message' => 'Booking updated successfully. Waiting for owner approval.',
        'booking' => $booking
    ]);
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