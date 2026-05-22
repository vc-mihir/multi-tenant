<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Auth\ForgotPasswordRequest;
use App\Services\Tenant\Auth\TenantPasswordService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Initialize dependencies
     *
     * @param TenantPasswordService $passwordService
     */
    public function __construct(protected TenantPasswordService $passwordService) {}

    /**
     * Display the password reset link request view.
     *
     * @return View
     */
    public function create(): View
    {
        return view('tenant.auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param ForgotPasswordRequest $request
     * @return RedirectResponse
     */
    public function store(ForgotPasswordRequest $request): RedirectResponse
    {
        $status = $this->passwordService->sendResetLink($request->validated()['email']);

        return $status == Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
    }
}
