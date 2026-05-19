<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\UpdateProfileRequest;
use App\Services\Tenant\Admin\TenantAdminProfileService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Initialize dependencies
     *
     * @param TenantAdminProfileService $profileService
     */
    public function __construct(protected TenantAdminProfileService $profileService) {}

    /**
     * Show the form for editing the company profile.
     *
     * @param string $tenant
     * @return View
     */
    public function edit(string $tenant): View
    {
        return view('tenant.admin.profile.edit', [
            'company' => Auth::guard('company')->user(),
        ]);
    }

    /**
     * Update the company profile.
     *
     * @param string $tenant
     * @param UpdateProfileRequest $request
     * @return RedirectResponse
     */
    public function update(string $tenant, UpdateProfileRequest $request): RedirectResponse
    {
        $tenantUser = Auth::guard('company')->user();

        try {
            $this->profileService->update($tenantUser, $request->validated());

            return redirect()
                ->route('tenant.admin.profile')
                ->with('success', 'Company profile updated successfully.');
        } catch (Exception $e) {
            activity()->withProperties(['error' => $e->getMessage(), 'subdomain' => $tenantUser->subdomain])
                ->log('Failed to update profile for tenant');

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'An error occurred while updating your profile. Please try again.');
        }
    }

    /**
     * Delete the company account and drop the tenant database.
     *
     * @param string $tenant
     * @return RedirectResponse
     */
    public function destroy(string $tenant): RedirectResponse
    {
        $tenantUser = Auth::guard('company')->user();

        try {
            $this->profileService->deleteAccount($tenantUser);
        } catch (Exception $e) {
            activity()->withProperties(['error' => $e->getMessage(), 'subdomain' => $tenantUser->subdomain])
                ->log('Failed to delete tenant');

            return redirect()->back()->with('error', 'Failed to delete account. Please try again later.');
        }

        Auth::guard('company')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('register', ['account_deleted' => true]);
    }
}
