<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Auth\UpdatePasswordRequest;
use App\Services\Tenant\Auth\TenantPasswordService;
use Illuminate\Http\RedirectResponse;

class PasswordController extends Controller
{
    /**
     * Initialize dependencies
     *
     * @param TenantPasswordService $passwordService
     */
    public function __construct(protected TenantPasswordService $passwordService) {}

    /**
     * Update the user's password.
     *
     * @param UpdatePasswordRequest $request
     * @return RedirectResponse
     */
    public function update(UpdatePasswordRequest $request): RedirectResponse
    {
        $this->passwordService->updatePassword($request->user(), $request->validated()['password']);

        return back()->with('status', 'password-updated');
    }
}
