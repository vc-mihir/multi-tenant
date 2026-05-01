<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tenant Web Routes
|--------------------------------------------------------------------------
|
| These routes are only accessible via tenant subdomains.
|
*/

Route::get('/dashboard', function () {
    return 'Tenant Dashboard (Placeholder)';
})->name('dashboard');
