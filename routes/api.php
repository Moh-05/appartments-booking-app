<?php

use App\Http\Controllers\AppartementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [UserController::class, 'register']);
Route::post('/verify', [UserController::class, 'verify']);
Route::post('login', [UserController::class, 'login']);
Route::post('/forget-password', [UserController::class, 'forgetPassword']);
Route::post('/verify-reset-otp', [UserController::class, 'verifyResetOtp']);
Route::post('/reset-password', [UserController::class, 'resetPassword']);
Route::middleware('auth:sanctum')->post('/logout-all', [UserController::class, 'logoutAll']);

Route::middleware('auth:sanctum')->get('/profile', [ProfileController::class, 'show']);
Route::middleware('auth:sanctum')->post('/profile/update', [ProfileController::class, 'update']);

Route::post('/store/appartements', [AppartementController::class, 'store'])->middleware('auth:sanctum');
Route::delete('/appartements/{id}', [AppartementController::class, 'destroy'])->middleware('auth:sanctum');
//lovee
