<?php

namespace App\Http\Controllers\Central\Admin;

use App\Http\Controllers\Controller;
use App\Models\Central\Company;
use App\Services\Central\CompanyService;
use Exception;
use Illuminate\Http\RedirectResponse;
use InvalidArgumentException;

class TenantRecoveryController extends Controller
{
    /**
     * Instantiate dependencies
     *
     * @param CompanyService $companyService
     */
    public function __construct(protected CompanyService $companyService) {}

    /**
     * Provision Tenant Database
     *
     * @param Company $company
     * @return RedirectResponse
     */
    public function provision(Company $company): RedirectResponse
    {
        try {
            $this->companyService->provisionDatabase($company);

            activity()->withProperties([
                'admin_id'   => auth()->id(),
                'company_id' => $company->id,
            ])->log('Admin triggered manual tenant DB provisioning');

            return back()->with('success', 'Tenant database creation job has been queued.');
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        } catch (Exception $e) {    
            activity()->withProperties([
                'company_id' => $company->id,
                'error'      => $e->getMessage(),
            ])->log('Failed to dispatch manual tenant DB provisioning job');

            return back()->with('error', 'Failed to queue provisioning job. Please check logs.');
        }
    }
}
