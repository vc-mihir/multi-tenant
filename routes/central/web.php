<?php
use App\Http\Controllers\Central\Admin\AdminAuthController;
use App\Http\Controllers\Central\Admin\CompanyController;
use App\Http\Controllers\Central\Admin\ProfileController;
use App\Http\Controllers\Central\Admin\TenantRecoveryController;
use App\Http\Controllers\Shared\CsrfTokenController;
use Illuminate\Support\Facades\Route;

// ─── All central-domain routes ─────────────────────────────────────────────────
// The 'central' middleware aborts with 404 if accessed via any subdomain.

Route::middleware('central')->group(function () {

    // Root → redirect to company registration
    Route::get('/', function () {
        return redirect()->route('register');
    });

    // Shared CSRF Refresh Endpoint
    Route::get('/refresh-csrf', [CsrfTokenController::class, 'refresh'])->name('csrf.refresh');

    // ── Admin Panel (SuperAdmin only) ─────────────────────────────────────────
    Route::middleware(['auth:admin', 'role:SuperAdmin'])->group(function () {
        Route::get('/admin/dashboard', [AdminAuthController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/companies', [CompanyController::class, 'index'])->name('admin.companies.index');
        Route::get('/admin/companies/create', [CompanyController::class, 'create'])->name('admin.companies.create');
        Route::post('/admin/companies', [CompanyController::class, 'store'])->name('admin.companies.store');
        Route::get('/admin/companies/data', [CompanyController::class, 'data'])->name('admin.companies.data');
        Route::get('/admin/companies/archived', [CompanyController::class, 'archived'])->name('admin.companies.archived');
        Route::get('/admin/companies/archived/data', [CompanyController::class, 'archivedData'])->name('admin.companies.archived.data');
        Route::get('/admin/companies/{company}/edit', [CompanyController::class, 'edit'])->name('admin.companies.edit');
        Route::put('/admin/companies/{company}', [CompanyController::class, 'update'])->name('admin.companies.update');
        Route::delete('/admin/companies/bulk-delete', [CompanyController::class, 'bulkDelete'])->name('admin.companies.bulk-delete');
        Route::delete('/admin/companies/{company}', [CompanyController::class, 'destroy'])->name('admin.companies.destroy');
        Route::patch('/admin/companies/{company}/restore', [CompanyController::class, 'restore'])->name('admin.companies.restore')->withTrashed();
        Route::delete('/admin/companies/{company}/force-delete', [CompanyController::class, 'forceDelete'])->name('admin.companies.force-delete')->withTrashed();

        // Recovery: re-provision a tenant DB
        Route::post('/admin/recovery/provision/{company}', [TenantRecoveryController::class, 'provision'])->name('admin.recovery.provision');

        Route::get('/admin/settings', [ProfileController::class, 'edit'])->name('admin.settings');
        Route::put('/admin/settings', [ProfileController::class, 'update'])->name('admin.settings.update');

        Route::get('/admin/search/companies', [CompanyController::class, 'search'])->name('admin.companies.search');

        Route::post('/admin/logout', [AdminAuthController::class, 'destroy'])->name('admin.logout');
    });

    // auth.php inherits the 'central' middleware from this group
    require __DIR__.'/auth.php';
});
