<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Appartement;
use App\Models\Booking;
use App\Notifications\NewBookingNotification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // <-- make sure Controller is imported
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
   public function store(Request $request, $appartementId)
{
    $request->validate([
        'end_date' => 'required|date|after:today',
    ]);

    $appartement = Appartement::findOrFail($appartementId);

    $booking = Booking::create([
        'user_id'        => Auth::id(),
        'appartement_id' => $appartement->id,
        'end_date'       => $request->end_date,
        'status'         => 'pending',
    ]);

    // Load relations for notification
    $booking->load(['appartement.owner', 'user']);

    // 👉 Notify the owner of the appartement instead of admins
    $appartement->owner->notify(new \App\Notifications\NewBookingNotification($booking));

    return response()->json([
        'message' => 'Booking request submitted, waiting for owner approval',
        'booking' => $booking,
    ], 201);
}
}
