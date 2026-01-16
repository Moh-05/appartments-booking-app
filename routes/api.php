<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AppartementController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RatingController;

// --------------------
// 🔐 Auth & User Routes
// --------------------
Route::post('/register', [UserController::class, 'register']);
Route::post('/verify', [UserController::class, 'verify']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

Route::post('/forget-password', [UserController::class, 'forgetPassword']);
Route::post('/verify-reset-otp', [UserController::class, 'verifyResetOtp']);
Route::post('/reset-password', [UserController::class, 'resetPassword']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn(Request $request) => $request->user());
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile/update', [ProfileController::class, 'update']);
    Route::get('/user/notifications', [UserController::class, 'notifications']);
    Route::get('/users/{username}/profile', [UserController::class, 'profile_user_side']);

    //change password 
    Route::post('/change-password', [UserController::class, 'changePassword']);
    //delte your account !!!
   Route::post('/user/delete', [UserController::class, 'deleteAccount'])->name('user.delete');
});

// --------------------
// 🏠 Appartement Routes
// --------------------
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('appartements', AppartementController::class);
    Route::get('/filter', [AppartementController::class, 'filter']);
});

// --------------------
// 📅 Booking Routes
// --------------------
Route::middleware('auth:sanctum')->group(function () {
    // User bookings
    Route::get('/bookings/past', [BookingController::class, 'pastBookings']);
    Route::get('/bookings/ongoing', [BookingController::class, 'ongoingBookings']);


    // Create booking for an appartement
    Route::post('/appartements/{appartementId}/bookings', [BookingController::class, 'store']);

    // Update booking
    Route::put('/bookings/{bookingId}', [BookingController::class, 'updateBooking']);

    // Cancel booking (user side)
    Route::post('/bookings/{bookingId}/cancel', [BookingController::class, 'cancelBooking']);
});

// --------------------
// 👨‍💼 Admin Routes
// --------------------
Route::post('/admin/login', [AdminController::class, 'login']);


    Route::get('/admin/notifications', [AdminController::class, 'notifications']);

    // Appartement approvals
    Route::post('/admin/appartements/{appartementId}/approve', [AdminController::class, 'approve_appartement']);
    Route::post('/admin/appartements/{appartementId}/reject', [AdminController::class, 'reject_appartement']);


// --------------------
// 🏠 Owner Routes
// --------------------
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/owner/bookings/{bookingId}/approve', [OwnerController::class, 'approveBooking']);
    Route::post('/owner/bookings/{bookingId}/reject', [OwnerController::class, 'rejectBooking']);

    // 🏠 Get all appartments owned by the authenticated owner
    Route::get('/owner/appartements', [OwnerController::class, 'myAppartements']);
});

// favorites
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/favorites/{id}', [FavoriteController::class, 'toggleFavorite']);
    Route::get('/favorites', [FavoriteController::class, 'myFavorites']);



    //ratings
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/appartements/{id}/rate', [RatingController::class, 'rate']);
        Route::get('/appartements/{id}/my-rating', [RatingController::class, 'myRating']);
    });

    Route::get('/appartements/{id}/average-rating', [RatingController::class, 'apartmentAverage']);



});
