<?php

namespace App\Services\Tenant\Auth;

use App\Http\Requests\Tenant\Auth\CompanyLoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class TenantAdminAuthService
{
    /**
     * Authenticate the company and regenerate the session.
     *
     * @param CompanyLoginRequest $request
     * @return void
     * @throws ValidationException
     */
    public function login(CompanyLoginRequest $request): void
    {
        try {
            $request->authenticate();
            $request->session()->regenerate();

            activity()
                ->causedBy(Auth::guard('company')->user())
                ->performedOn(Auth::guard('company')->user())
                ->event('login')
                ->log('Tenant admin logged in');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('TenantAdminAuthService::login', ['error' => $e->getMessage()]);
            throw new \Exception('Failed to login. Please try again.');
        }
    }

    /**
     * Logout the company guard session.
     *
     * @param Request $request
     * @return void
     */
    public function logout(Request $request): void
    {
        try {
            $user = Auth::guard('company')->user();

            Auth::guard('company')->logout();
            $request->session()->regenerate();
            $request->session()->regenerateToken();

            activity()
                ->causedBy($user)
                ->performedOn($user)
                ->event('logout')
                ->log('Tenant admin logged out');
        } catch (\Exception $e) {
            Log::error('TenantAdminAuthService::logout', ['error' => $e->getMessage()]);
            throw new \Exception('Failed to logout. Please try again.');
        }
    }
}
