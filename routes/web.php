<?php
use App\Http\Controllers\Admin\AdminAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AdminAuthController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'store'])
        ->middleware('throttle:admin_login')
        ->name('admin.login.post');
});

Route::middleware(['auth', 'role:SuperAdmin'])->group(function () {
    Route::get('/admin/dashboard', [AdminAuthController::class, 'index'])->name('admin.dashboard');

    Route::post('/admin/logout', [AdminAuthController::class, 'destroy'])->name('admin.logout');
});

require __DIR__.'/auth.php';
