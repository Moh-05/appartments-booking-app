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

    // 🚫 منع المالك من حجز شقته الخاصة
    if ($appartement->owner->id === Auth::id()) {
        return response()->json([
            'status'  => 'error',
            'message' => 'You cannot book your own appartement.'
        ], 403);
    }

    $startDate = Carbon::parse($request->start_date);
    $endDate   = Carbon::parse($request->end_date);

    // ✅ تحقق من وجود حجز فعّال (لم ينته بعد)
    $activeBooking = $appartement->bookings()
        ->where('status', 'booked')
        ->where('end_date', '>=', now())
        ->orderBy('end_date', 'desc')
        ->first();

    if ($activeBooking) {
        $activeEndDate = Carbon::parse($activeBooking->end_date);

        // الشرط: بداية الحجز الجديد لازم تكون أكبر 100% من نهاية الحجز الحالي
        if ($startDate->lessThanOrEqualTo($activeEndDate)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'This appartement is booked until ' . $activeEndDate->format('Y-m-d') .
                             '. You can only book it starting from ' . $activeEndDate->copy()->addDay()->format('Y-m-d')
            ], 403);
        }
    }

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

    $appartement->owner->notify(new NewBookingNotification($booking));

    return response()->json([
        'status'  => 'success',
        'message' => 'Booking request submitted, waiting for owner approval',
        'booking' => $booking,
    ], 201);
}
    public function cancelBooking($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        //  Ensure only the user who made the booking can cancel it
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

    $startDate = Carbon::parse($request->start_date);
    $endDate   = Carbon::parse($request->end_date);

    $activeBooking = $booking->appartement->bookings()
        ->where('status', 'booked')
        ->where('end_date', '>=', now())
        ->orderBy('end_date', 'desc')
        ->first();

    if ($activeBooking) {
        $activeEndDate = Carbon::parse($activeBooking->end_date);

        if ($startDate->lessThanOrEqualTo($activeEndDate)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'This appartement is booked until ' . $activeEndDate->format('Y-m-d') .
                             '. You can only update your booking to start from ' . $activeEndDate->copy()->addDay()->format('Y-m-d')
            ], 403);
        }
    }

    $days       = $startDate->diffInDays($endDate);
    $totalPrice = $days * $booking->appartement->price;

    $booking->start_date  = $request->start_date;
    $booking->end_date    = $request->end_date;
    $booking->total_price = $totalPrice;
    $booking->status      = 'pending';
    $booking->save();

    
    $booking->appartement->owner->notify(new NewBookingNotification($booking));

    return response()->json([
        'status'  => 'success',
        'message' => 'Booking updated successfully. Waiting for owner approval.',
        'booking' => $booking
    ]);
}
   public function pastBookings()
{
    $bookings = Booking::with(['appartement.owner']) 
        ->where('user_id', Auth::id())
        ->whereIn('status', ['canceled', 'completed'])
        ->get();

    return response()->json([
        'status' => 'success',
        'data'   => $bookings
    ], 200);
}

public function ongoingBookings()
{
    $bookings = Booking::with(['appartement.owner']) 
        ->where('user_id', Auth::id())
        ->whereIn('status', ['booked', 'pending'])
        ->get();

    return response()->json([
        'status' => 'success',
        'data'   => $bookings
    ], 200);
}
}
