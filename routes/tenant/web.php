<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\AdminDashboardController;
use App\Http\Controllers\Tenant\ProfileController;

/*
|--------------------------------------------------------------------------
| Tenant Web Routes
|--------------------------------------------------------------------------
|
| These routes are only accessible via tenant subdomains.
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('tenant.index');

Route::middleware('auth:company')->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->name('tenant.admin.dashboard');

    Route::get('/admin/profile', [ProfileController::class, 'edit'])
        ->name('tenant.admin.profile');
    Route::put('/admin/profile', [ProfileController::class, 'update'])
        ->name('tenant.admin.profile.update');
});

require __DIR__.'/auth.php';