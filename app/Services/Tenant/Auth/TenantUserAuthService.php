<?php

namespace App\Services\Tenant\Auth;

use App\Http\Requests\Tenant\Auth\LoginRequest;
use App\Models\Tenant\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class TenantUserAuthService
{
    /**
     * Authenticate the tenant user and regenerate the session.
     *
     * @param LoginRequest $request
     * @return void
     * @throws ValidationException
     */
    public function login(LoginRequest $request): void
    {
        try {
            $request->authenticate();
            $request->session()->regenerate();

            activity()
                ->causedBy(Auth::guard('tenant_user')->user())
                ->performedOn(Auth::guard('tenant_user')->user())
                ->event('login')
                ->log('Tenant user logged in');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('TenantUserAuthService::login', ['error' => $e->getMessage()]);
            throw new \Exception('Failed to login. Please try again.');
        }
    }

    /**
     * Register a new tenant user and fire the Registered event.
     *
     * @param array $data
     * @return User
     */
    public function register(array $data): User
    {
        try {
            $user = User::create([
                'name'      => $data['name'],
                'email'     => $data['email'],
                'password'  => $data['password'],
                'is_active' => true,
            ]);

            event(new Registered($user));

            Auth::guard('tenant_user')->login($user);

            activity()
                ->causedBy($user)
                ->performedOn($user)
                ->event('registered')
                ->log('Tenant user registered and logged in');

            return $user;
        } catch (\Exception $e) {
            Log::error('TenantUserAuthService::register', [
                'email' => $data['email'],
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to register user. Please try again.');
        }
    }

    /**
     * Logout the tenant_user guard session.
     *
     * @param Request $request
     * @return void
     */
    public function logout(Request $request): void
    {
        try {
            $user = Auth::guard('tenant_user')->user();

            Auth::guard('tenant_user')->logout();
            $request->session()->regenerate();
            $request->session()->regenerateToken();

            activity()
                ->causedBy($user)
                ->performedOn($user)
                ->event('logout')
                ->log('Tenant user logged out');
        } catch (\Exception $e) {
            Log::error('TenantUserAuthService::logout', ['error' => $e->getMessage()]);
            throw new \Exception('Failed to logout. Please try again.');
        }
    }
}
