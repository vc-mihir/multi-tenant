<?php

namespace App\Services\Tenant\Auth;

use App\Models\Tenant\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantUserAuthService
{
    /**
     * Register a new tenant user and fire the Registered event.
     *
     * @param array $data
     * @return User
     */
    public function register(array $data): User
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'],
        ]);

        event(new Registered($user));

        return $user;
    }

    /**
     * Logout the tenant_user guard session.
     *
     * @param Request $request
     * @return void
     */
    public function logout(Request $request): void
    {
        Auth::guard('tenant_user')->logout();
        $request->session()->regenerate();
        $request->session()->regenerateToken();
    }
}
