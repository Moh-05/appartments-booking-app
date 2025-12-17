<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
//love//
class UserController extends Controller
{

    public function register(Request $request)
    {
        $request->validate([
            'full_name'     => 'required|string|max:255',
            'username'      => 'required|string|max:255|unique:users,username',
            'phone'         => 'required|string|max:15|unique:users,phone',
            'password'      => 'required|string|min:6|confirmed',
            'profile_image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'id_image'      => 'required|image|mimes:png,jpg,jpeg|max:2048',
            'user_date'     => 'required|date',
        ]);

        $otp = rand(100000, 999999);

        $url = "https://api.ultramsg.com/" . env('ULTRAMSG_INSTANCE_ID') . "/messages/chat";

        // Send OTP via UltraMsg
        $response = Http::asForm()->post($url, [
            'token' => env('ULTRAMSG_TOKEN'),
            'to'    => $request->phone,
            'body'  => "Your OTP code is: $otp",
        ]);

        $respData = $response->json();

        if (isset($respData['error'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'UltraMsg returned error, message not sent.',
                'ultramsg_response' => $respData
            ], 400);
        }

        $profileImagePath = null;
        $idImagePath = null;

        if ($request->hasFile('profile_image')) {
            $profileImagePath = $request->file('profile_image')->store('profile_images', 'public');
        }

        if ($request->hasFile('id_image')) {
            $idImagePath = $request->file('id_image')->store('id_images', 'public');
        }

        Cache::put('pending_user', [
            'otp'           => $otp,
            'full_name'     => $request->full_name,
            'username'      => $request->username,
            'phone'         => $request->phone,
            'password'      => Hash::make($request->password),
            'profile_image' => $profileImagePath, // may be null if not uploaded
            'id_image'      => $idImagePath,
            'user_date'     => $request->user_date,
        ], now()->addMinutes(1));

        return response()->json([
            'status' => 'success',
            'message' => 'OTP sent successfully',
            'ultramsg_response' => $respData
        ]);
    }
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        // Get user data + OTP directly from cache
        $data = Cache::get('pending_user');

        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'No pending user found or OTP expired'
            ], 400);
        }

        if ($request->otp == $data['otp']) {
            $user = User::create([
                'full_name'     => $data['full_name'],
                'username'      => $data['username'],
                'phone'         => $data['phone'],
                'password'      => $data['password'],
                'profile_image' => $data['profile_image'] ?? null, // optional
                'id_image'      => $data['id_image'],              // required
                'user_date'     => $data['user_date'],             // required
            ]);

            // Clear cache after successful verification
            Cache::forget('pending_user');

            return response()->json([
                'status' => 'success',
                'message' => 'User registered and verified!',
                'user' => $user,
                'debug_otp' => $data['otp'] // Show last OTP
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid OTP',
            'debug_otp' => $data['otp'] // Show last OTP even if wrong
        ], 400);
    }
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            return response()->json(
                ['message' => 'Invalid username or password'],
                401
            );
        }

        $user = User::where('username', $request->username)->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(
            [
                'message' => 'Logged in successfully',
                'user' => $user,
                'token' => $token
            ],
            200
        );
    }

   public function forgetPassword(Request $request)
{
    $request->validate([
        'phone' => 'required|string|max:15',
    ]);

    // Normalize phone input (trim spaces, ensure consistent format)
    $phone = trim($request->phone);

    $user = User::where('phone', $phone)->first();
    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'Phone number not found',
        ], 404);
    }

    $otp = rand(100000, 999999);

    // Send OTP via UltraMsg
    $url = "https://api.ultramsg.com/" . env('ULTRAMSG_INSTANCE_ID') . "/messages/chat";
    $response = Http::asForm()->post($url, [
        'token' => env('ULTRAMSG_TOKEN'),
        'to'    => $phone,
        'body'  => "Your password reset OTP is: $otp",
    ]);

    $respData = $response->json();
    if (isset($respData['error'])) {
        return response()->json([
            'status' => 'error',
            'message' => 'UltraMsg error, OTP not sent',
            'ultramsg_response' => $respData
        ], 400);
    }

    // Store reset flow data linked to OTP
    Cache::put('reset_flow_' . $otp, [
        'phone' => $phone,
        'otp'   => $otp,
    ], now()->addMinutes(1));

    return response()->json([
        'status' => 'success',
        'message' => 'OTP sent successfully',
        'ultramsg_response' => $respData
    ]);
}


public function verifyResetOtp(Request $request)
{
    $request->validate([
        'otp' => 'required|digits:6',
    ]);

    $data = Cache::get('reset_flow_' . $request->otp);

    if (!$data) {
        return response()->json([
            'status' => 'error',
            'message' => 'Invalid or expired OTP'
        ], 400);
    }

    $resetToken = Str::random(40);

    Cache::put('reset_token_' . $resetToken, [
        'phone' => $data['phone'],
    ], now()->addMinutes(5));

    // Clear the OTP cache since it’s used
    Cache::forget('reset_flow_' . $request->otp);

    return response()->json([
        'status' => 'success',
        'message' => 'OTP verified successfully. Use reset token to reset password.',
        'reset_token' => $resetToken
    ]);
}
    public function resetPassword(Request $request)
    {
        $request->validate([
            'reset_token' => 'required|string',
            'password'    => 'required|string|min:6|confirmed',
        ]);

        $tokenData = Cache::get('reset_token_' . $request->reset_token);
        if (!$tokenData) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired reset token'
            ], 400);
        }

        $user = User::where('phone', $tokenData['phone'])->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Clear cache
        Cache::forget('reset_token_' . $request->reset_token);

        return response()->json([
            'status' => 'success',
            'message' => 'Password reset successfully'
        ]);
    }

     public function notifications()
    {
        $user = Auth::user();

        // Get all notifications (you can also filter unread/read)
        $notifications = $user->notifications;

        return response()->json([
            'message' => 'User notifications retrieved successfully',
            'notifications' => $notifications
        ]);
    }


}
