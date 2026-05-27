<?php

namespace App\Http\Controllers\Central\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Central\Admin\AdminLoginRequest;
use App\Services\Central\Admin\AdminAuthService;
use App\Services\Central\Admin\AdminDashboardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    /**
     * Injecting dependencies
     *
     * @param AdminDashboardService $dashboardService
     * @param AdminAuthService $authService
     */
    public function __construct(
        protected AdminDashboardService $dashboardService,
        protected AdminAuthService $authService,
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        return view('central.admin.dashboard', $this->dashboardService->getStats());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        return view('central.auth.login');
    }

    /**
     * Admin login handler
     *
     * @param AdminLoginRequest $request
     * @return RedirectResponse
     */
    public function store(AdminLoginRequest $request): RedirectResponse
    {
        $this->authService->attemptLogin($request->validated(), $request);

        return redirect()->route('admin.dashboard')->with('success', 'Login successfully');
    }

    /**
     * Admin logout handler
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
