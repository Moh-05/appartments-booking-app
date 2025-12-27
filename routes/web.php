<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// --------------------
// 👨‍💼 Admin Routes (Web Only)
// --------------------

Route::get('/admin/login', function () {
    return view('Admin_login'); 
});

Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login');

Route::middleware('auth:admin')->group(function () {


    // Dashboard
Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/admin/dashboard', function () {
        return view('Admin_Dashboard');
    })->name('admin.dashboard');

    // Notifications
    Route::get('/admin/notifications', [AdminController::class, 'notifications'])->name('admin.notifications');

    // Appartement approvals (Web actions)
    Route::post('/admin/appartements/{appartementId}/approve', [AdminController::class, 'approve_appartement'])->name('admin.appartements.approve');
    Route::post('/admin/appartements/{appartementId}/reject', [AdminController::class, 'reject_appartement'])->name('admin.appartements.reject');

    Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
});