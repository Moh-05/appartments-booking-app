<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Booking;
use App\Models\Appartement;
use App\Models\User;
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

    // ابحث عن الأدمن بالـ username
    $admin = Admin::where('username', $request->username)->first();

    // تحقق من كلمة المرور بالنص الصريح
    if (!$admin || $admin->password !== $request->password) {
        return back()->withErrors(['message' => 'Invalid username or password']);
    }

    // سجّل الأدمن في الـ guard (auth:admin) باستخدام الـ session
    Auth::guard('admin')->login($admin);

    // إعادة التوجيه مباشرة إلى الـ Dashboard
    return redirect()->route('admin.dashboard');
}


public function users()
{
    $users = User::with(['bookings', 'appartements'])->get();
    return view('Admin_Dashboard', compact('users'));
}

    // Show all notifications for the logged-in admin
    public function notifications()
    {
       $notifications = Auth::guard('admin')->user()->notifications;

        return response()->json([
            'notifications' => $notifications
        ]);
    }

    public function approve_appartement($appartementId)
{
    $appartement = Appartement::findOrFail($appartementId);
    $appartement->approval_status = 'approved';
    $appartement->save();

    // Notify the owner
    $appartement->owner->notify(new AppartementStatusNotification($appartement));

    return response()->json([
        'message' => 'Appartement approved successfully',
        'appartement' => $appartement
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