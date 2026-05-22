<?php

namespace App\Http\Controllers\Central\Auth;

use App\Http\Controllers\Controller;
use App\Services\Central\CompanyService;
use Illuminate\Http\RedirectResponse;

class CompanyVerifyEmailController extends Controller
{
    /**
     * Inject dependencies
     *
     * @param CompanyService $companyService
     */
    public function __construct(protected CompanyService $companyService) {}

    /**
     * Verify company email and activate account
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function __invoke(int $id): RedirectResponse
    {
        $tenantUrl = $this->companyService->verifyEmail($id);

        return redirect()->route('register')
            ->with('status', "Company email verified successfully. Your account is now active. You can visit your website at {$tenantUrl} and for admin login visit {$tenantUrl}/admin/login");
    }
}
