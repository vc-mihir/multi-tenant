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
            $guard = Auth::guard('admin');

            if ($guard->attempt($credentials) && $guard->user()->hasRole('SuperAdmin')) {
                $request->session()->regenerate();

                activity()
                    ->causedBy($guard->user())
                    ->performedOn($guard->user())
                    ->event('login')
                    ->log('SuperAdmin logged in');

                return;
            }
        } catch (\Exception $e) {
            Log::error('AdminAuthService::attemptLogin', ['error' => $e->getMessage()]);
            throw new \Exception('Failed to login. Please try again.');
        }

        Auth::guard('admin')->logout();
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
            $user = Auth::guard('admin')->user();

            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            activity()
                ->causedBy($user)
                ->performedOn($user)
                ->event('logout')
                ->log('SuperAdmin logged out');
        } catch (\Exception $e) {
            Log::error('AdminAuthService::logout', ['error' => $e->getMessage()]);
            throw new \Exception('Failed to logout. Please try again.');
        }
    }
}
