<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


// --------------------
// 👨‍💼 Admin Routes (Web Only)
// --------------------

Route::get('/admin/login', function () {
    return view('Admin_login');
});

Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login');

Route::middleware('auth:admin')->group(function () {

    // Dashboard → يعرض جدول المستخدمين
    Route::get('/admin/dashboard', [AdminController::class, 'users'])->name('admin.dashboard');

    // Notifications
    Route::get('/admin/notifications', [AdminController::class, 'notifications'])->name('admin.notifications');

    // Appartement approvals (Web actions)
    Route::post('/admin/appartements/{appartementId}/approve', [AdminController::class, 'approve_appartement'])->name('admin.appartements.approve');
    Route::post('/admin/appartements/{appartementId}/reject', [AdminController::class, 'reject_appartement'])->name('admin.appartements.reject');

    Route::get('/user/{username}/appartements', [AdminController::class, 'appartementsPage']);

    Route::get('/user/{username}/bookings', [AdminController::class, 'userBookings']);

    Route::get('/user/{username}/details', [AdminController::class, 'userDetails']);

    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');


    // Logout
    Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
});