<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display the tenant admin dashboard.
     *
     * @return View
     */
    public function index(): View
    {
        return view('tenant.admin.dashboard');
    }
}
