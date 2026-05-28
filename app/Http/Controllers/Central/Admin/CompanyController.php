<?php

namespace App\Http\Controllers\Central\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Central\Admin\BulkDeleteCompaniesRequest;
use App\Http\Requests\Central\Admin\StoreCompanyRequest;
use App\Http\Requests\Central\Admin\UpdateCompanyRequest;
use App\Models\Central\Company;
use App\Services\Central\Admin\CompanyDataTableService;
use App\Services\Central\CompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    /**
     * Inject dependencies
     *
     * @param CompanyService $companyService
     * @param CompanyDataTableService $dataTableService
     */
    public function __construct(
        protected CompanyService $companyService,
        protected CompanyDataTableService $dataTableService,
    ) {}

    /**
     * Load companies listing view
     *
     * @return View
     */
    public function index(): View
    {
        return view('central.admin.companies.index');
    }

    /**
     * Load archived companies view
     *
     * @return View
     */
    public function archived(): View
    {
        return view('central.admin.companies.archived');
    }

    /**
     * Return DataTables JSON for archived (soft-deleted) companies
     *
     * @return JsonResponse
     */
    public function archivedData(): JsonResponse
    {
        return $this->dataTableService->getArchivedData();
    }

    /**
     * Load company creation view
     *
     * @return View
     */
    public function create(): View
    {
        return view('central.admin.companies.create');
    }

    /**
     * Store a new company
     *
     * @param StoreCompanyRequest $request
     * @return RedirectResponse
     */
    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        $this->companyService->createCompany($request->validated(), true);

        return redirect()->route('admin.companies.index')
            ->with('success', 'Company created successfully. Database provisioning has been queued.');
    }

    /**
     * Get companies DataTable data
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function data(Request $request): JsonResponse
    {
        return $this->dataTableService->getData($request->get('status'));
    }

    /**
     * Load company edit view
     *
     * @param Company $company
     * @return View
     */
    public function edit(Company $company): View
    {
        return view('central.admin.companies.edit', compact('company'));
    }

    /**
     * Update company details
     *
     * @param UpdateCompanyRequest $request
     * @param Company $company
     * @return RedirectResponse
     */
    public function update(UpdateCompanyRequest $request, Company $company): RedirectResponse
    {
        $this->companyService->updateCompany($company, $request->validated());

        return redirect()->route('admin.companies.index')
            ->with('success', 'Company details updated and synced across databases.');
    }

    /**
     * Restore a soft-deleted company
     *
     * @param Company $company
     * @return JsonResponse
     */
    public function restore(Company $company): JsonResponse
    {
        $this->companyService->restoreCompany($company);

        return response()->json(['success' => true, 'message' => 'Company has been restored successfully.']);
    }

    /**
     * Permanently delete a soft-deleted company and drop its tenant database
     *
     * @param Company $company
     * @return JsonResponse
     */
    public function forceDelete(Company $company): JsonResponse
    {
        $this->companyService->forceDeleteCompany($company);

        return response()->json(['success' => true, 'message' => 'Company permanently deleted and database dropped.']);
    }

    /**
     * Delete company and its database
     *
     * @param Company $company
     * @return JsonResponse
     */
    public function destroy(Company $company): JsonResponse
    {
        $this->companyService->deleteCompany($company);

        return response()->json(['success' => true, 'message' => 'Company archived successfully.']);
    }

    /**
     * Bulk delete companies and their databases
     *
     * @param BulkDeleteCompaniesRequest $request
     * @return JsonResponse
     */
    public function bulkDelete(BulkDeleteCompaniesRequest $request): JsonResponse
    {
        $deletedCount = $this->companyService->bulkDeleteCompanies($request->validated()['ids']);

        return response()->json([
            'success' => true,
            'message' => "Successfully archived {$deletedCount} companies.",
        ]);
    }

    /**
     * Search companies by name or email
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $results = $this->companyService->searchCompanies($request->get('q', ''))
            ->map(fn($company) => [
                'id'    => $company->id,
                'name'  => $company->company_name,
                'email' => $company->company_email,
                'url'   => route('admin.companies.edit', $company->id),
            ]);

        return response()->json($results);
    }
}
