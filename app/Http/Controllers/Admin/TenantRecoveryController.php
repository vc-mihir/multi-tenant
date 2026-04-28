<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Jobs\CreateCompanyDatabase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
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

            Log::info('Admin triggered manual tenant DB provisioning.', [
                'admin_id' => auth()->id(),
                'company_id' => $company->id
            ]);

            return back()->with('success', 'Tenant database creation job has been queued.');
        } catch (Exception $e) {
            Log::error('Failed to dispatch manual tenant DB provisioning job.', [
                'company_id' => $company->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to queue provisioning job. Please check logs.');
        }
    }
}
