<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Http\Controllers\Controller;
use App\Services\Tenant\Auth\TenantEmailVerificationService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Initialize dependencies
     *
     * @param TenantEmailVerificationService $verificationService
     */
    public function __construct(protected TenantEmailVerificationService $verificationService) {}

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param EmailVerificationRequest $request
     * @return RedirectResponse
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect(route('tenant.dashboard', absolute: false).'?verified=1');
        }

        $this->verificationService->verify($request->user());

        return redirect(route('tenant.dashboard', absolute: false).'?verified=1');
    }
}
