<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProfileController extends Controller
{
    public function show()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return response()->json([
            'status' => 'success',
            'profile' => [
                'full_name'     => $user->full_name,
                'username'      => $user->username,
                'phone'         => $user->phone,
                'profile_image' => $user->profile_image ?? null,
                'id_image'      => $user->id_image,
                'user_date'     => $user->user_date,
            ]
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

    
        if ($request->hasFile('profile_image')) {
            $user->profile_image = $request->file('profile_image')->store('profile_images', 'public');
        }

        
        if ($request->hasFile('id_image')) {
            $user->id_image = $request->file('id_image')->store('id_images', 'public');
        }

        
        $user->full_name = $request->filled('full_name') ? $request->full_name : $user->full_name;
        $user->username  = $request->filled('username')  ? $request->username  : $user->username;
        $user->phone     = $request->filled('phone')     ? $request->phone     : $user->phone;
        $user->user_date = $request->filled('user_date') ? $request->user_date : $user->user_date;

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'profile' => $user
        ]);
    }
}
