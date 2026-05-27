<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Auth\CompanyLoginRequest;
use App\Services\Tenant\Auth\TenantAdminAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminLoginController extends Controller
{
    /**
     * Initialize dependencies
     *
     * @param TenantAdminAuthService $authService
     */
    public function __construct(protected TenantAdminAuthService $authService) {}

    /**
     * Display the tenant admin login view.
     *
     * @return View
     */
    public function create(): View
    {
        return view('tenant.auth.admin-login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param CompanyLoginRequest $request
     * @return RedirectResponse
     */
    public function store(CompanyLoginRequest $request): RedirectResponse
    {
        $this->authService->login($request);

        return redirect()->route('tenant.admin.dashboard')->with('success', 'Login successfully.');
    }

    /**
     * Destroy an authenticated session.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        $this->authService->logout($request);

        return redirect()->route('tenant.admin.login')->with('success', 'Logout successfully.');
    }
}
