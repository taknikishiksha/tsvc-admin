<?php

use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\RegisterRoleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes (Custom Login System)
|--------------------------------------------------------------------------
| NOTE:
| - Breeze / AuthenticatedSessionController REMOVED
| - Login & Logout are handled via routes/web.php
|   using Auth\LoginController
| - This file only keeps:
|   Registration, Password Reset, Email Verification, Confirm Password
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Registration (Role-based)
    |--------------------------------------------------------------------------
    */

    // Role selection page
    Route::get('register', function () {
        return view('auth.register-role');
    })->name('register');

    // Allowed public roles
    $publicRolesPattern = 'student|teacher|client|intern|volunteer|donor|corporate';

    // Role-specific registration form
    Route::get('register/{role}', function ($role) {
        if (! in_array($role, config('registration.public_roles', []))) {
            abort(404);
        }
        return view('auth.register', compact('role'));
    })->where('role', $publicRolesPattern)->name('register.role');

    // Role-based registration submit
    Route::post('register/{role}', [RegisterRoleController::class, 'store'])
        ->where('role', $publicRolesPattern)
        ->name('register.role.store');

    /*
    |--------------------------------------------------------------------------
    | Password Reset (Guest)
    |--------------------------------------------------------------------------
    */

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Email Verification
    |--------------------------------------------------------------------------
    */

    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    /*
    |--------------------------------------------------------------------------
    | Confirm Password
    |--------------------------------------------------------------------------
    */

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    /*
    |--------------------------------------------------------------------------
    | Update Password (Authenticated)
    |--------------------------------------------------------------------------
    */

    Route::put('password', [PasswordController::class, 'update'])
        ->name('password.update');

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    | IMPORTANT:
    | Actual logout route is defined in routes/web.php
    | via Auth\LoginController@logout
    |--------------------------------------------------------------------------
    */
});
