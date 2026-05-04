<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Central\Company as CentralCompany;
use App\Http\Requests\Tenant\UpdateProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Exception;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the company profile.
     *
     * @return View
     */
    public function edit(): View
    {
        $company = Auth::guard('company')->user();

        return view('tenant.admin.profile.edit', compact('company'));
    }

    /**
     * Update the company profile.
     *
     * @param UpdateProfileRequest $request
     * @return RedirectResponse
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $tenantUser = Auth::guard('company')->user();
        $validated = $request->validated();

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        try {
            DB::transaction(function () use ($tenantUser, $validated) {
                $tenantUser->update($validated);

                CentralCompany::on('mysql')
                    ->where('subdomain', $tenantUser->subdomain)
                    ->update($validated);
            });

            Auth::guard('company')->login($tenantUser);

            Log::info("Profile updated successfully for tenant: {$tenantUser->subdomain}");

            return redirect()
                ->route('tenant.admin.profile')
                ->with('success', 'Company profile updated successfully.');

        } catch (Exception $e) {
            Log::error("Failed to update profile for tenant {$tenantUser->subdomain}: " . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'An error occurred while updating your profile. Please try again.');
        }
    }
}
