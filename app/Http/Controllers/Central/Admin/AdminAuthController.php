<?php

namespace App\Http\Controllers\Central\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Central\Admin\AdminLoginRequest;
use App\Services\Central\Admin\AdminAuthService;
use App\Services\Central\Admin\AdminDashboardService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    /**
     * Initialize dependencies
     *
     * @param AdminDashboardService $dashboardService
     * @param AdminAuthService $authService
     */
    public function __construct(
        protected AdminDashboardService $dashboardService,
        protected AdminAuthService $authService,
    ) {}

    /**
     * Load Admin Dashboard View and display statistics
     *
     * @return View
     */
    public function index(): View
    {
        return view('central.admin.dashboard', $this->dashboardService->getStats());
    }

    /**
     * Load Admin Login View
     *
     * @return View
     */
    public function create(): View
    {
        return view('central.admin.login');
    }

    /**
     * Check Credentials and Login Admin
     *
     * @param AdminLoginRequest $request
     * @return RedirectResponse
     */
    public function store(AdminLoginRequest $request): RedirectResponse
    {
        try {
            if ($this->authService->attemptLogin($request->validated(), $request)) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return back()->with('error', 'credentials does not match try again');
        } catch (Exception $e) {
            return back()->with('error', 'credentials does not match try again');
        }
    }

    /**
     * Logout Admin 
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        $this->authService->logout($request);

        return redirect()->route('admin.login')->with('success', 'Logout successfully');
    }
}
