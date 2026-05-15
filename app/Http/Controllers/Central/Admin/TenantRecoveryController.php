<?php

namespace App\Http\Controllers\Central\Admin;

use App\Http\Controllers\Controller;
use App\Models\Central\Company;
use App\Jobs\CreateCompanyDatabase;
use Illuminate\Http\RedirectResponse;
use Exception;

class TenantRecoveryController extends Controller
{
    /**
     * Trigger tenant database creation manually.
     *
     * @param Company $company
     * @return RedirectResponse
     */
    public function provision(Company $company): RedirectResponse
    {
        if (!$company->email_verified_at || $company->database()->exists()) {
            return back()->with('error', 'This company is not eligible for database provisioning.');
        }

        try {
            CreateCompanyDatabase::dispatch($company);

            activity()->withProperties([
                'admin_id' => auth()->id(),
                'company_id' => $company->id
            ])->log('Admin triggered manual tenant DB provisioning');

            return back()->with('success', 'Tenant database creation job has been queued.');
        } catch (Exception $e) {
            activity()->withProperties([
                'company_id' => $company->id,
                'error' => $e->getMessage()
            ])->log('Failed to dispatch manual tenant DB provisioning job');

            return back()->with('error', 'Failed to queue provisioning job. Please check logs.');
        }
    }
}
