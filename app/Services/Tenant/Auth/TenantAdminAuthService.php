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
            Auth::guard('company')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        } catch (\Exception $e) {
            Log::error('TenantAdminAuthService::logout', ['error' => $e->getMessage()]);
            throw new \Exception('Failed to logout. Please try again.');
        }
    }
}
