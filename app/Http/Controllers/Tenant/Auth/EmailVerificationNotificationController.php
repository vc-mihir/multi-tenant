<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Http\Controllers\Controller;
use App\Services\Tenant\Auth\TenantEmailVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Initialize dependencies
     *
     * @param TenantEmailVerificationService $verificationService
     */
    public function __construct(protected TenantEmailVerificationService $verificationService) {}

    /**
     * Send a new email verification notification.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        if (! $this->verificationService->sendVerification($request->user())) {
            return redirect()->intended(route('tenant.index', absolute: false));
        }

        return back()->with('success', 'A new verification link has been sent to your email address.');
    }
}
