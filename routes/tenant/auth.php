<?php

use App\Http\Controllers\Tenant\Auth\AdminLoginController;
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
