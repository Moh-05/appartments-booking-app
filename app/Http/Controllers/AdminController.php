<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Appartement;
use App\Notifications\BookingStatusNotification;
use App\Notifications\AppartementStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // Show all notifications for the logged-in admin
    public function notifications()
    {
        $notifications = Auth::user()->notifications; // only for this admin

        return response()->json([
            'notifications' => $notifications
        ]);
    }

    // Approve a booking
    public function approve_booking($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        $booking->status = 'booked';
        $booking->save();

        // Notify the user who made the booking
        $booking->user->notify(new BookingStatusNotification($booking));

        return response()->json([
            'message' => 'Booking approved successfully',
            'booking' => $booking
        ]);
    }

    // Decline a booking
    public function decline_booking($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        $booking->status = 'canceled';
        $booking->save();

        // Notify the user who made the booking
        $booking->user->notify(new BookingStatusNotification($booking));

        return response()->json([
            'message' => 'Booking declined successfully',
            'booking' => $booking
        ]);
    }

    // Approve an appartement
    public function approve_appartement($appartementId)
    {
        $appartement = Appartement::findOrFail($appartementId);
        $appartement->approval_status = 'approved';
        $appartement->save();

        // Notify the owner
        $appartement->user->notify(new AppartementStatusNotification($appartement));

        return response()->json([
            'message' => 'Appartement approved successfully',
            'appartement' => $appartement
        ]);
    }

    // Reject an appartement
    public function reject_appartement($appartementId)
    {
        $appartement = Appartement::findOrFail($appartementId);
        $appartement->approval_status = 'rejected';
        $appartement->save();

        // Notify the owner
        $appartement->user->notify(new AppartementStatusNotification($appartement));

        return response()->json([
            'message' => 'Appartement rejected successfully',
            'appartement' => $appartement
        ]);
    }
}