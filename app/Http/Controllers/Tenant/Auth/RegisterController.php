<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreUserRequest;
use App\Services\Tenant\Auth\TenantUserAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Initialize dependencies
     *
     * @param TenantUserAuthService $authService
     */
    public function __construct(protected TenantUserAuthService $authService) {}

    /**
     * Display the registration view.
     *
     * @return View
     */
    public function create(): View
    {
        return view('tenant.auth.register');
    }

    /**
     * Handle user registration request.
     *
     * @param StoreUserRequest $request
     * @return RedirectResponse
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = $this->authService->register($request->validated());

        Auth::guard('tenant_user')->login($user);

        return redirect()->route('verification.notice');
    }
}
