<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\AdminDashboardController;

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
})->name('dashboard');

Route::middleware('auth:company')->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->name('tenant.admin.dashboard');
});

require __DIR__.'/auth.php';