<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Auth\CompanyLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminLoginController extends Controller
{
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
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('tenant.admin.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('company')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('tenant.admin.login')->with('status', 'Logout successfully');
    }
}
