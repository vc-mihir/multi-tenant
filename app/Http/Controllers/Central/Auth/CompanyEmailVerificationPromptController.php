<?php

namespace App\Http\Controllers\Central\Auth;

use App\Http\Controllers\Controller;
use App\Services\Central\CompanyService;
use Illuminate\View\View;

class CompanyEmailVerificationPromptController extends Controller
{
    /**
     * Inject dependencies
     *
     * @param CompanyService $companyService
     */
    public function __construct(protected CompanyService $companyService) {}

    /**
     * Display company email verification prompt
     *
     * @param int $id
     * @return View
     */
    public function __invoke(int $id): View
    {
        $company = $this->companyService->getUnverifiedCompany($id);

        return view('central.auth.verify-email', compact('company'));
    }
}
