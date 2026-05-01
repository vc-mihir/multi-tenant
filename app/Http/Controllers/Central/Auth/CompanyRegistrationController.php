<?php

namespace App\Http\Controllers\Central\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Central\Auth\CompanyRegistrationRequest;
use App\Services\CompanyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\View\View;

class CompanyRegistrationController extends Controller
{
    /**
     * The company service instance.
     */
    protected CompanyService $companyService;

    /**
     * Create a new controller instance.
     *
     * @param CompanyService $companyService
     */
    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    /**
     * Display the registration view.
     *
     * @return View
     */
    public function create(): View
    {
        return view('central.auth.register');
    }

    /**
     * Validate the incoming company registration request.
     *
     * @param CompanyRegistrationRequest $request
     * @return RedirectResponse
     */
    public function store(CompanyRegistrationRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $company = $this->companyService->createCompany($validated, false);

            return redirect()->route('companies.verification.notice', [
                'id' => $company->id,
            ]);
        } catch (Throwable $e) {
            Log::error('Company registration failed.', [
                'company_email' => $validated['company_email'] ?? null,
                'exception' => $e->getMessage(),
            ]);

            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->with('error', 'Unable to complete registration right now. Please try again.');
        }
    }
}
