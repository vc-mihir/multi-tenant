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

Route::get('/', function () {
    return view('welcome');
})->name('dashboard');
