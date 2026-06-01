<?php

namespace App\Services\Central\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminAuthService
{
    /**
     * Attempt SuperAdmin login using hash-based email lookup so encrypted email is queryable.
     *
     * @param array $credentials
     * @param Request $request
     * @return void
     */
    public function attemptLogin(array $credentials, Request $request): void
    {
        try {
            $emailHash = hash('sha256', strtolower($credentials['email']));

            $user = User::where('email_hash', $emailHash)->first();

            if ($user && Hash::check($credentials['password'], $user->password) && $user->hasRole('SuperAdmin')) {
                Auth::guard('admin')->login($user);
                $request->session()->regenerate();

                activity()
                    ->causedBy($user)
                    ->performedOn($user)
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
