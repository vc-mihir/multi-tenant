<?php

namespace App\Services\Central\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminAuthService
{
    /**
     * Attempt SuperAdmin login
     *
     * @param array $credentials
     * @param Request $request
     * @return void
     */
    public function attemptLogin(array $credentials, Request $request): void
    {
        try {
            if (Auth::attempt($credentials) && Auth::user()->hasRole('SuperAdmin')) {
                $request->session()->regenerate();
                return;
            }
        } catch (\Exception $e) {
            Log::error('AdminAuthService::attemptLogin', ['error' => $e->getMessage()]);
            throw new \Exception('Failed to login. Please try again.');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        throw new \Exception('Credentials do not match. Please try again.');
    }

    /**
     * Logout SuperAdmin
     *
     * @param Request $request
     * @return void
     */
    public function logout(Request $request): void
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        } catch (\Exception $e) {
            Log::error('AdminAuthService::logout', ['error' => $e->getMessage()]);
            throw new \Exception('Failed to logout. Please try again.');
        }
    }
}
