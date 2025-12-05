<?php

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
//lovee
