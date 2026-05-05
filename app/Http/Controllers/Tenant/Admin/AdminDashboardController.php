<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display the tenant admin dashboard.
     *
     * @param string $tenant
     * @return View
     */
    public function index(string $tenant): View
    {
        return view('tenant.admin.dashboard');
    }
}
