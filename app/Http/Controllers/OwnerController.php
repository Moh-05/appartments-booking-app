<?php

namespace App\Http\Controllers;

use App\Models\Appartement;
use App\Models\Booking;
use App\Notifications\BookingStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerController extends Controller
{
    public function approveBooking($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        $booking->status = 'booked';
        $booking->save();

        $appartement = $booking->appartement;
        $appartement->available = false;
        $appartement->save();

        $booking->user->notify(new BookingStatusNotification($booking));

        return response()->json([
            'message' => 'Booking approved successfully',
            'booking' => $booking
        ]);
    }
    public function rejectBooking($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        $booking->status = 'canceled';
        $booking->save();

        $appartement = $booking->appartement;
        $appartement->available = true;
        $appartement->save();

        $booking->user->notify(new BookingStatusNotification($booking));

        return response()->json([
            'message' => 'Booking canceled successfully',
            'booking' => $booking
        ]);
    }

    public function myAppartements()
    {
        $owner = Auth::user();

        $appartements = $owner->appartements;

        return response()->json([
            'status' => 'success',
            'data'   => $appartements
        ], 200);
    }
}
