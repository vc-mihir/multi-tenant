<?php
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'role:SuperAdmin'])->group(function () {
    Route::get('/admin/dashboard', [AdminAuthController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/companies', [CompanyController::class, 'index'])->name('admin.companies.index');
    Route::get('/admin/companies/data', [CompanyController::class, 'data'])->name('admin.companies.data');
    Route::get('/admin/companies/{company}/edit', [CompanyController::class, 'edit'])->name('admin.companies.edit');
    Route::put('/admin/companies/{company}', [CompanyController::class, 'update'])->name('admin.companies.update');
    Route::delete('/admin/companies/{company}', [CompanyController::class, 'destroy'])->name('admin.companies.destroy');
    
    Route::get('/admin/settings', [ProfileController::class, 'edit'])->name('admin.settings');
    Route::put('/admin/settings', [ProfileController::class, 'update'])->name('admin.settings.update');

    Route::post('/admin/logout', [AdminAuthController::class, 'destroy'])->name('admin.logout');
});

require __DIR__.'/auth.php';
