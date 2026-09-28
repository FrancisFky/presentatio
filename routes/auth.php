<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Connexion et mot de passe oublié (non connecté)
Route::middleware('guest.admin')->group(function () {
    Route::get('login', [AuthController::class, 'login'])->name('login');
    Route::post('login', [AuthController::class, 'store'])->middleware('throttle:10,1')->name('login.store');

    Route::get('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
    Route::post('forgot-password', [AuthController::class, 'sendResetLink'])->middleware('throttle:5,1')->name('forgot-password.store');

    Route::get('reset-password/{token}', [AuthController::class, 'resetPassword'])->name('password.reset');
    Route::post('reset-password', [AuthController::class, 'updatePassword'])->middleware('throttle:5,1')->name('password.update');
});

// Code reçu par e-mail, et déconnexion (possible avant le code)
Route::middleware('auth.admin')->group(function () {
    Route::get('verify-otp', [AuthController::class, 'verifyOtp'])->name('otp.verify');
    Route::post('verify-otp', [AuthController::class, 'verifyOtpStore'])->middleware('throttle:10,1')->name('otp.verify.store');
    Route::post('resend-otp', [AuthController::class, 'resendOtp'])->middleware('throttle:3,1')->name('otp.resend');

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

// Profil : après le code
Route::middleware(['auth.admin', 'check.otp'])->group(function () {
    Route::get('profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('change-password', [ProfileController::class, 'changePassword'])->name('password.change');
});
