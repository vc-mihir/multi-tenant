<?php

namespace App\Services\Tenant\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantAdminAuthService
{
    /**
     * Logout the company guard session.
     *
     * @param Request $request
     * @return void
     */
    public function logout(Request $request): void
    {
        Auth::guard('company')->logout();
        $request->session()->regenerate();
        $request->session()->regenerateToken();
    }
}
