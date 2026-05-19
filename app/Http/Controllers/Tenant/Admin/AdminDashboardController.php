<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Services\Tenant\Admin\TenantDashboardService;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Initialize dependencies
     *
     * @param TenantDashboardService $dashboardService
     */
    public function __construct(protected TenantDashboardService $dashboardService) {}

    /**
     * Display the tenant admin dashboard.
     *
     * @param string $tenant
     * @return View
     */
    public function index(string $tenant): View
    {
        return view('tenant.admin.dashboard', $this->dashboardService->getStats());
    }
}
