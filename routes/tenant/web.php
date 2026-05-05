<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\Admin\AdminDashboardController;
use App\Http\Controllers\Tenant\Admin\ProfileController;
use App\Http\Controllers\Tenant\Admin\UserController;

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

Route::middleware('guest:company')->group(function () {
    Route::get('/login', function () {
        return view('welcome');
    })->name('tenant.login');

    Route::get('/register', function () {
        return view('tenant.auth.register');
    })->name('tenant.register');
});

Route::middleware('auth:company')->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->name('tenant.admin.dashboard');

    Route::get('/admin/profile', [ProfileController::class, 'edit'])
        ->name('tenant.admin.profile');
    Route::put('/admin/profile', [ProfileController::class, 'update'])
        ->name('tenant.admin.profile.update');

    // User Management
    Route::get('/admin/users', [UserController::class, 'index'])->name('tenant.admin.users.index');
    Route::get('/admin/users/data', [UserController::class, 'data'])->name('tenant.admin.users.data');
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('tenant.admin.users.create');
    Route::post('/admin/users', [UserController::class, 'store'])->name('tenant.admin.users.store');
    Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('tenant.admin.users.edit');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('tenant.admin.users.update');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('tenant.admin.users.destroy');
});

require __DIR__.'/auth.php';