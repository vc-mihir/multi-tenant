<?php

namespace App\Services\Central\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthService
{
    /**
     * Attempt SuperAdmin login, regenerate session on success, invalidate on failure.
     */
    public function attemptLogin(array $credentials, Request $request): bool
    {
        if (Auth::attempt($credentials) && Auth::user()->hasRole('SuperAdmin')) {
            $request->session()->regenerate();
            return true;
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return false;
    }

    /**
     * Logout SuperAdmin
     *
     * @param Request $request
     * @return void
     */
    public function logout(Request $request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
