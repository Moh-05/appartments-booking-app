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
  $notifications = Auth::guard('admin')->user()->notifications->map(function ($notification) {
    $appartement = Appartement::with('owner')->find($notification->data['appartement_id']);

    return [
        'id'             => $notification->id,
        'message'        => $notification->data['message'] ?? null,
        'appartement_id' => $notification->data['appartement_id'] ?? null,
        'title'          => $appartement?->title ?? 'N/A',
        'owner'          => $appartement?->owner?->username ?? 'Unknown Owner',
        'status'         => $appartement?->approval_status ?? 'N/A',
        'created_at'     => $notification->created_at->format('Y-m-d H:i'),
    ];
});
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

    public function appartementsPage($username)
{
    $today = now();

    $user = User::with([
        'appartements.owner',
        'appartements.bookings' => function ($query) use ($today) {
            $query->where('status', 'booked')
                  ->where('start_date', '<=', $today)
                  ->where('end_date', '>=', $today);
        },
        'appartements.bookings.user'
    ])
    ->where('username', $username)
    ->firstOrFail();

    return view('User_Appartements', [
        'user' => $user,
        'appartements' => $user->appartements
    ]);
}

    public function userBookings($username)
{
    // نجيب المستخدم مع الحجوزات المرتبطة بالشقق
    $user = User::where('username', $username)
        ->with(['bookings.appartement'])
        ->firstOrFail();

    // نقسم الحجوزات إلى ongoing و past
    $ongoing = $user->bookings->filter(function ($booking) {
        return in_array($booking->status, ['pending', 'booked']);
    });

    $past = $user->bookings->filter(function ($booking) {
        return in_array($booking->status, ['cancelled', 'completed']);
    });

    return view('User_Bookings', [
        'username' => $user->username,
        'ongoingBookings' => $ongoing,
        'pastBookings' => $past,
    ]);

    
}
  public function userDetails($username)
    {
        // نجيب المستخدم حسب الـ username
        $user = User::where('username', $username)->firstOrFail();

        return view('User_Details', [
            'user' => $user
        ]);
    }

 public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // نحذف المستخدم
        $user->delete();

        return redirect()->route('admin.dashboard')->with('success', 'User deleted successfully');
    }



}