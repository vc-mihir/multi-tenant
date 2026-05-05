<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant\User;
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
        $usersCount = User::count();
        $unverifiedUsersCount = User::whereNull('email_verified_at')->count();
        return view('tenant.admin.dashboard', compact('usersCount', 'unverifiedUsersCount'));
    }
}
