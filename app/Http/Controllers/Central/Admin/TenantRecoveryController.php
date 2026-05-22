<?php

namespace App\Http\Controllers\Central\Admin;

use App\Http\Controllers\Controller;
use App\Models\Central\Company;
use App\Services\Central\CompanyService;
use Illuminate\Http\RedirectResponse;

class TenantRecoveryController extends Controller
{
    /**
     * Inject dependencies
     *
     * @param CompanyService $companyService
     */
    public function __construct(protected CompanyService $companyService) {}

    /**
     * Provision tenant database for a company
     *
     * @param Company $company
     * @return RedirectResponse
     */
    public function provision(Company $company): RedirectResponse
    {
        $this->companyService->provisionDatabase($company);

        return back()->with('success', 'Tenant database creation job has been queued.');
    }
}
