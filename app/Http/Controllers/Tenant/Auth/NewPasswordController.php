<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Auth\ResetPasswordRequest;
use App\Services\Tenant\Auth\TenantPasswordService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Initialize dependencies
     *
     * @param TenantPasswordService $passwordService
     */
    public function __construct(protected TenantPasswordService $passwordService) {}

    /**
     * Display the password reset view.
     *
     * @param Request $request
     * @return View
     */
    public function create(Request $request): View
    {
        return view('tenant.auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @param ResetPasswordRequest $request
     * @return RedirectResponse
     */
    public function store(ResetPasswordRequest $request): RedirectResponse
    {
        $status = $this->passwordService->resetPassword($request->validated());

        return $status == Password::PASSWORD_RESET
            ? redirect()->route('tenant.login')->with('success', __($status))
            : back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
    }
}
