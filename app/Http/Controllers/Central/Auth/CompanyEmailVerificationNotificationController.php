<?php

namespace App\Http\Controllers\Central\Auth;

use App\Http\Controllers\Controller;
use App\Models\Central\Company;
use App\Services\Central\CompanyService;
use Illuminate\Http\RedirectResponse;
use Throwable;

class CompanyEmailVerificationNotificationController extends Controller
{
    /**
     * Initialize dependencies
     *
     * @param CompanyService $companyService
     */
    public function __construct(protected CompanyService $companyService) {}

    /**
     * Resend verification email
     *
     * @param integer $id
     * @return RedirectResponse
     */
    public function store(int $id): RedirectResponse
    {
        try {
            $company = Company::findOrFail($id);

            if ($company->hasVerifiedEmail()) {
                return redirect()->route('register')->with('status', 'Company account is already active.');
            }

            $this->companyService->resendVerificationEmail($company);

            return back()->with('status', 'Verification link sent successfully. Please check your inbox.');
        } catch (Throwable $e) {
            activity()->withProperties([
                'company_id' => $id,
                'exception'  => $e->getMessage(),
            ])->log('Company verification resend failed');

            return back()->with('error', 'Unable to resend verification email right now. Please try again.');
        }
    }
}
