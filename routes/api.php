<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AppartementController;
use App\Http\Controllers\AdminController;

// --------------------
// 🔐 Auth & User Routes
// --------------------
Route::post('/register', [UserController::class, 'register']);
Route::post('/verify', [UserController::class, 'verify']);
Route::post('/login', [UserController::class, 'login']);

Route::post('/forget-password', [UserController::class, 'forgetPassword']);
Route::post('/verify-reset-otp', [UserController::class, 'verifyResetOtp']);
Route::post('/reset-password', [UserController::class, 'resetPassword']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// --------------------
// 🏠 Appartement Routes
// --------------------
// Resource API for appartements (index, store, update, destroy)
Route::middleware('auth:sanctum')->apiResource('appartements', AppartementController::class);

// --------------------
// 📅 Booking Routes
// --------------------
Route::middleware('auth:sanctum')->group(function () {
    // Create booking for an appartement
    Route::post('/appartements/{appartementId}/bookings', [BookingController::class, 'store']);

    // Future: Cancel a booking
    Route::post('/bookings/{bookingId}/cancel', [BookingController::class, 'cancel']);
});

// --------------------
// 👨‍💼 Admin Routes
// --------------------
Route::middleware('auth:sanctum')->group(function () {
    // Notifications
    Route::get('/admin/notifications', [AdminController::class, 'notifications']);

    // Booking approvals
    Route::post('/admin/bookings/{bookingId}/approve', [AdminController::class, 'approve_booking']);
    Route::post('/admin/bookings/{bookingId}/decline', [AdminController::class, 'decline_booking']);

    // Appartement approvals
    Route::post('/admin/appartements/{appartementId}/approve', [AdminController::class, 'approve_appartement']);
    Route::post('/admin/appartements/{appartementId}/reject', [AdminController::class, 'reject_appartement']);
});