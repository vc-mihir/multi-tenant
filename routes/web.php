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
require __DIR__.'/central/web.php';

// ─── Tenant Domain Routes ────────────────────────────────────────────────────
// require __DIR__.'/tenant/web.php';
