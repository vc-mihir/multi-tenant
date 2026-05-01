<?php

use App\Http\Controllers\Central\Admin\AdminAuthController;
use App\Http\Controllers\Central\Auth\CompanyEmailVerificationNotificationController;
use App\Http\Controllers\Central\Auth\CompanyEmailVerificationPromptController;
use App\Http\Controllers\Central\Auth\CompanyRegistrationController;
use App\Http\Controllers\Central\Auth\CompanyVerifyEmailController;
use Illuminate\Support\Facades\Route;

// ─── Guest-only (no session required) ─────────────────────────────────────────

Route::middleware('guest')->group(function () {

    // SuperAdmin login (central domain only)
    Route::get('admin/login', [AdminAuthController::class, 'create'])
        ->name('admin.login');

    Route::post('admin/login', [AdminAuthController::class, 'store'])
        ->middleware('throttle:admin_login')
        ->name('admin.login.post');

    // Company self-registration (central domain only)
    Route::get('company-register', [CompanyRegistrationController::class, 'create'])
        ->name('register');

    Route::post('company-register', [CompanyRegistrationController::class, 'store']);
});

// ─── Company email verification (signed URLs, no auth required) ────────────────

Route::prefix('companies')->name('companies.')->group(function () {
    Route::get('verify-email/{id}/notice', CompanyEmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::post('email/verification-notification/{id}', [CompanyEmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('verify-email/{id}', CompanyVerifyEmailController::class)
        ->middleware('throttle:6,1')
        ->name('verification.verify');
});
