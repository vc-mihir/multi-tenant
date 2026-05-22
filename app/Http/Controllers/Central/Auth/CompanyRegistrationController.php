<?php

namespace App\Http\Controllers\Central\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Central\Auth\CompanyRegistrationRequest;
use App\Services\Central\CompanyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CompanyRegistrationController extends Controller
{
    /**
     * Inject dependencies
     *
     * @param CompanyService $companyService
     */
    public function __construct(protected CompanyService $companyService) {}

    /**
     * Load company registration view
     *
     * @return View
     */
    public function create(): View
    {
        return view('central.auth.register');
    }

    /**
     * Register a new company
     *
     * @param CompanyRegistrationRequest $request
     * @return RedirectResponse
     */
    public function store(CompanyRegistrationRequest $request): RedirectResponse
    {
        $company = $this->companyService->createCompany($request->validated(), false);

        return redirect()->route('companies.verification.notice', ['id' => $company->id]);
    }
}
