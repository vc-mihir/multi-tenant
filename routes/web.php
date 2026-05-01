<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is the entry point for all web routes. It separates routes
| into Central and Tenant domain groups for better organization.
|
*/

// ─── Central Domain Routes ───────────────────────────────────────────────────
Route::domain(parse_url(config('app.url'), PHP_URL_HOST))->group(function () {
    require __DIR__.'/central/web.php';
});

// ─── Tenant Domain Routes ────────────────────────────────────────────────────
Route::domain('{tenant}.'.parse_url(config('app.url'), PHP_URL_HOST))->group(function () {
    require __DIR__.'/tenant/web.php';
});
