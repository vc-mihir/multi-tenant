<?php

namespace App\Http\Controllers\Central\Auth;

use App\Http\Controllers\Controller;
use App\Models\Central\Company;
use App\Services\Central\CompanyService;
use Illuminate\Http\RedirectResponse;
use Throwable;

class CompanyVerifyEmailController extends Controller
{
    /**
     * Initialize dependencies
     *
     * @param CompanyService $companyService
     */
    public function __construct(protected CompanyService $companyService) {}

    /**
     * Verify Email
     *
     * @param integer $id
     * @return RedirectResponse
     */
    public function __invoke(int $id): RedirectResponse
    {
        try {
            $company = Company::findOrFail($id);
            $this->companyService->verifyEmail($company);

            $baseHost = parse_url(config('app.url'), PHP_URL_HOST);
            $tenantUrl = 'http://' . $company->subdomain . '.' . $baseHost;

            return redirect()->route('register')
                ->with('status', "Company email verified successfully. Your account is now active. You can visit your website at {$tenantUrl} and for admin login visit {$tenantUrl}/admin/login");
        } catch (Throwable $e) {
            activity()->withProperties([
                'company_id' => $id,
                'exception'  => $e->getMessage(),
            ])->log('Company email verification failed');

            return redirect()->route('register')
                ->with('error', 'Unable to verify company email right now. Please try again.');
        }
    }
}
