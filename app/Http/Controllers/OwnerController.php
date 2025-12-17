<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerController extends Controller
{
   public function approveBooking($bookingId)
{
    $booking = Booking::findOrFail($bookingId);

    $booking->status = 'booked';
    $booking->start_date = now(); 
    $booking->save();

    $appartement = $booking->appartement;
    $appartement->available = false;
    $appartement->save();


    $booking->user->notify(new \App\Notifications\BookingStatusNotification($booking));

    return response()->json([
        'message' => 'Booking approved successfully',
        'booking' => $booking
    ]);
}
}
