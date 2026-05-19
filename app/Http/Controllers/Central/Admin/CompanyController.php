<?php

namespace App\Http\Controllers\Central\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Central\Admin\StoreCompanyRequest;
use App\Http\Requests\Central\Admin\UpdateCompanyRequest;
use App\Models\Central\Company;
use App\Services\Central\Admin\CompanyDataTableService;
use App\Services\Central\CompanyService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    /**
     * Initialize dependencies
     *
     * @param CompanyService $companyService
     * @param CompanyDataTableService $dataTableService
     */
    public function __construct(
        protected CompanyService $companyService,
        protected CompanyDataTableService $dataTableService,
    ) {}

    /**
     * Load Admin Companies View
     *
     * @return View
     */
    public function index(): View
    {
        return view('central.admin.companies.index');
    }

    /**
     * Load Admin Companies Create View
     *
     * @return View
     */
    public function create(): View
    {
        return view('central.admin.companies.create');
    }

    /**
     * Store new company
     *
     * @param StoreCompanyRequest $request
     * @return RedirectResponse
     */
    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        try {
            $this->companyService->createCompany($request->validated(), true);

            return redirect()->route('admin.companies.index')
                ->with('success', 'Company created successfully. Database provisioning has been queued.');
        } catch (Exception $e) {
            activity()->withProperties(['error' => $e->getMessage()])->log('Admin company creation failed');
            return back()->withInput()->with('error', 'Failed to create company: ' . $e->getMessage());
        }
    }

    /**
     * Get Companies Data
     *
     * @return JsonResponse
     */
    public function data(): JsonResponse
    {
        return $this->dataTableService->getData(request('status'));
    }

    /**
     * Load Admin Companies Edit View
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
        try {
            $this->companyService->updateCompany($company, $request->validated());

            return redirect()->route('admin.companies.index')
                ->with('success', 'Company details updated and synced across databases.');
        } catch (Exception $e) {
            activity()->withProperties(['error' => $e->getMessage(), 'company_id' => $company->id])
                ->log('Failed to update and sync company');
            return back()->with('error', 'Failed to update company details. ' . $e->getMessage());
        }
    }

    /**
     * Delete company and its database
     *
     * @param Company $company
     * @return JsonResponse
     */
    public function destroy(Company $company): JsonResponse
    {
        if (! auth()->user()->hasRole('SuperAdmin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        try {
            $this->companyService->deleteCompany($company);
        } catch (Exception $e) {
            activity()->withProperties(['error' => $e->getMessage(), 'company_id' => $company->id])
                ->log('Failed to delete company');
            return response()->json(['success' => false, 'message' => 'Failed to delete company.'], 500);
        }

        return response()->json(['success' => true, 'message' => 'Company and database successfully purged.']);
    }

    /**
     * Bulk delete companies and their databases
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No companies selected.'], 422);
        }

        $deletedCount = $this->companyService->bulkDeleteCompanies($ids);

        return response()->json([
            'success' => true,
            'message' => "Successfully deleted {$deletedCount} companies and their databases.",
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
        $query = $request->get('q');

        if (empty($query)) {
            return response()->json([]);
        }

        $results = $this->companyService->searchCompanies($query)->map(fn ($company) => [
            'id'    => $company->id,
            'name'  => $company->company_name,
            'email' => $company->company_email,
            'url'   => route('admin.companies.edit', $company->id),
        ]);

        return response()->json($results);
    }
}
