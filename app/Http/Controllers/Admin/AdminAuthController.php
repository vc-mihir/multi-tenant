<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        return view('admin.dashboard');
    }

    /**
     * Display the super admin login page.
     *
     * @return View
     */
    public function create(): View
    {
        return view('admin.login');
    }

    /**
     * Handle the super admin authentication.
     *
     * @param AdminLoginRequest $request
     * @return RedirectResponse
     */
    public function store(AdminLoginRequest $request): RedirectResponse
    {
        try {
            
            if (Auth::attempt($request->validated()) && Auth::user()->hasRole('SuperAdmin')) {
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            }

            Auth::logout();
            return back()->with('error', 'credentials does not match try again');
        } catch (Exception $e) {
            return back()->with('error', 'credentials does not match try again');
        }
    }

    /**
     * Destroy an authenticated session.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Logout successfully');
    }
}
