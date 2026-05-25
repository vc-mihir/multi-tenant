<?php

namespace App\Http\Controllers\Central\Auth;

use App\Http\Controllers\Controller;
use App\Services\Central\CompanyService;
use Illuminate\Http\RedirectResponse;

class CompanyEmailVerificationNotificationController extends Controller
{
    /**
     * Inject dependencies
     *
     * @param CompanyService $companyService
     */
    public function __construct(protected CompanyService $companyService) {}

    /**
     * Resend company email verification notification
     *
     * @param string $id
     * @return RedirectResponse
     */
    public function store(string $id): RedirectResponse
    {
        $this->companyService->resendVerificationEmail($id);

        return back()->with('status', 'Verification link sent successfully. Please check your inbox.');
    }
}
