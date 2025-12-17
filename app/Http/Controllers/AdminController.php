<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Booking;
use App\Models\Appartement;
use App\Notifications\BookingStatusNotification;
use App\Notifications\AppartementStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{


public function login(Request $request)
{
    $request->validate([
        'username' => 'required|string',
        'password' => 'required|string',
    ]);

    // Find admin by username
    $admin = Admin::where('username', $request->username)->first();

    // Compare plain text password directly
    if (!$admin || $admin->password !== $request->password) {
        return response()->json(['message' => 'Invalid username or password'], 401);
    }

    // Create Sanctum token
    $token = $admin->createToken('admin_auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Admin logged in successfully',
        'admin'   => $admin,
        'token'   => $token
    ], 200);
}

    // Show all notifications for the logged-in admin
    public function notifications()
    {
       $notifications = Auth::guard('admin')->user()->notifications;

        return response()->json([
            'notifications' => $notifications
        ]);
    }
    public function reject_appartement($appartementId)
    {
        $appartement = Appartement::findOrFail($appartementId);
        $appartement->approval_status = 'rejected';
        $appartement->save();

        // Notify the owner
        $appartement->owner->notify(new AppartementStatusNotification($appartement));

        return response()->json([
            'message' => 'Appartement rejected successfully',
            'appartement' => $appartement
        ]);
    }
}