<?php

use App\Http\Controllers\Tenant\Auth\AdminLoginController;
use App\Http\Controllers\Tenant\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Tenant\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Tenant\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tenant Auth Routes
|--------------------------------------------------------------------------
|
| These routes handle authentication for tenant users.
|
*/

Route::middleware('guest:company')->group(function () {
    // Tenant Admin Login
    Route::get('/admin/login', [AdminLoginController::class, 'create'])
        ->name('tenant.admin.login');

    Route::post('/admin/login', [AdminLoginController::class, 'store'])
        ->name('tenant.admin.login.post');
});

Route::middleware('auth:company')->group(function () {
    Route::post('/admin/logout', [AdminLoginController::class, 'destroy'])
        ->name('tenant.admin.logout');
});

// ─── Tenant User Email Verification ──────────────────────────────────────────
Route::middleware('auth:tenant_user')->group(function () {
    Route::get('/verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});
