<?php

namespace App\Http\Controllers\Tenant\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\User\ProfileUpdateRequest;
use App\Services\Tenant\User\TenantUserProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Initialize dependencies
     *
     * @param TenantUserProfileService $profileService
     */
    public function __construct(protected TenantUserProfileService $profileService) {}

    /**
     * Show the user profile page.
     *
     * @return View
     */
    public function edit(): View
    {
        return view('tenant.user.profile', [
            'user' => Auth::guard('tenant_user')->user(),
        ]);
    }

    /**
     * Update the user profile.
     *
     * @param ProfileUpdateRequest $request
     * @return RedirectResponse
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = Auth::guard('tenant_user')->user();

        $needsVerification = $this->profileService->update($user, $request->validated());

        if ($needsVerification) {
            return redirect()->route('verification.notice')
                ->with('success', 'Email updated. Please verify your new email address.')
                ->with('email_changed', true);
        }

        return redirect()
            ->route('tenant.user.profile')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Delete the user's account.
     *
     * @return RedirectResponse
     */
    public function destroy(): RedirectResponse
    {
        $this->profileService->deleteAccount(Auth::guard('tenant_user')->user());

        return redirect()
            ->route('tenant.login')
            ->with('success', 'Your account has been deleted.');
    }
}
