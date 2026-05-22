<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\UpdateProfileRequest;
use App\Services\Tenant\Admin\TenantAdminProfileService;
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
        $this->profileService->update(Auth::guard('company')->user(), $request->validated());

        return redirect()
            ->route('tenant.admin.profile')
            ->with('success', 'Company profile updated successfully.');
    }

    /**
     * Delete the company account and drop the tenant database.
     *
     * @param string $tenant
     * @return RedirectResponse
     */
    public function destroy(string $tenant): RedirectResponse
    {
        $this->profileService->deleteAccount(Auth::guard('company')->user());

        return redirect()->route('register', ['account_deleted' => true]);
    }
}
