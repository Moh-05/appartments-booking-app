<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AppartementController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\ProfileController;


// --------------------
// 🔐 Auth & User Routes
// --------------------
Route::post('/register', [UserController::class, 'register']);
Route::post('/verify', [UserController::class, 'verify']);
Route::post('/login', [UserController::class, 'login']);

Route::post('/forget-password', [UserController::class, 'forgetPassword']);
Route::post('/verify-reset-otp', [UserController::class, 'verifyResetOtp']);
Route::post('/reset-password', [UserController::class, 'resetPassword']);
Route::middleware('auth:sanctum')->post('/logout-all', [UserController::class, 'logoutAll']);

Route::middleware('auth:sanctum')->get('/profile', [ProfileController::class, 'show']);
Route::middleware('auth:sanctum')->post('/profile/update', [ProfileController::class, 'update']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // 👉 User notifications
    Route::get('/user/notifications', [UserController::class, 'notifications']);
});

// --------------------
// 🏠 Appartement Routes
// --------------------
Route::middleware('auth:sanctum')->apiResource('appartements', AppartementController::class);
Route::middleware('auth:sanctum')->get('/appartements/filter', [AppartementController::class, 'filter']);
// --------------------
// 📅 Booking Routes
// --------------------
Route::middleware('auth:sanctum')->get('/user/bookings', [BookingController::class, 'myBookings']);
Route::middleware('auth:sanctum')->group(function () {

    // Create booking for an appartement
    Route::post('/appartements/{appartementId}/bookings', [BookingController::class, 'store']);

    // Cancel a booking
    Route::post('/bookings/{bookingId}/cancel', [BookingController::class, 'cancel']);
});

// --------------------
// 👨‍💼 Admin Routes
// --------------------
Route::post('/admin/login', [AdminController::class, 'login']);

Route::middleware('auth:admin')->group(function () {
    // Notifications
    Route::get('/admin/notifications', [AdminController::class, 'notifications']);

    // appartement approvals
    Route::post('/admin/appartements/{appartementId}/approve', [AdminController::class, 'approve_appartement']);
    Route::post('/admin/appartements/{appartementId}/reject', [AdminController::class, 'reject_appartement']);
});

// --------------------
// 🏠 Owner Routes
// --------------------
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/owner/bookings/{bookingId}/approve', [OwnerController::class, 'approveBooking']);
    Route::post('/owner/bookings/{bookingId}/decline', [OwnerController::class, 'declineBooking']);
});

// تعديل شقة معينة

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/appartements/{id}', [AppartementController::class, 'update']);
});
