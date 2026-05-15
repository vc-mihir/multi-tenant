<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Central\Company as CentralCompany;
use App\Http\Requests\Tenant\UpdateProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Exception;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the company profile.
     *
     * @param string $tenant
     * @return View
     */
    public function edit(string $tenant): View
    {
        $company = Auth::guard('company')->user();

        return view('tenant.admin.profile.edit', compact('company'));
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

            return redirect()
                ->route('tenant.admin.profile')
                ->with('success', 'Company profile updated successfully.');

        } catch (Exception $e) {
            activity()->withProperties(['error' => $e->getMessage(), 'subdomain' => $tenantUser->subdomain])->log('Failed to update profile for tenant');

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
        $subdomain = $tenantUser->subdomain;
        
        // Ensure we are fetching the company from central DB
        $centralCompany = CentralCompany::on('mysql')->where('subdomain', $subdomain)->first();
        
        if ($centralCompany) {
            $dbName = $centralCompany->database->db_name ?? null;
            
            try {
                // Delete central record (cascades to company_databases)
                DB::transaction(function () use ($centralCompany) {
                    $centralCompany->delete();
                });

                // Drop the actual database outside of transaction
                if ($dbName) {
                    DB::connection('mysql')->statement("DROP DATABASE IF EXISTS `{$dbName}`");
                }

            } catch (Exception $e) {
                activity()->withProperties(['error' => $e->getMessage(), 'subdomain' => $subdomain])->log('Failed to delete tenant');
                return redirect()->back()->with('error', 'Failed to delete account. Please try again later.');
            }
        }

        Auth::guard('company')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('register', ['account_deleted' => true]);
    }
}
