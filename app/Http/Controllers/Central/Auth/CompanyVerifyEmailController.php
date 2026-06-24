<?php

namespace App\Http\Controllers\Central\Auth;

use App\Http\Controllers\Controller;
use App\Services\Central\CompanyService;
use Illuminate\View\View;

class CompanyVerifyEmailController extends Controller
{
    /**
     * Inject dependencies
     *
     * @param CompanyService $companyService
     */
    public function __construct(protected CompanyService $companyService) {}

    /**
     * Verify the company email, then show a loader while the tenant provisions.
     *
     * verifyEmail() marks the account active and queues provisioning (the tenant
     * database + /etc/hosts mapping), which takes ~1–2s on the worker. Redirecting
     * straight to the subdomain would land before it resolves and show a transient
     * "site can't be reached" error. Instead we render a loader that polls until
     * the tenant is ready and then forwards to it. If provisioning never completes,
     * the loader falls back to the registration page with a success notice — the
     * account is already created and verified, so this is not an error state.
     *
     * @param string $id
     * @return View
     */
    public function __invoke(string $id): View
    {
        $tenantUrl = $this->companyService->verifyEmail($id);

        return view('central.auth.provisioning', [
            // ?activated=1 triggers the one-time "account is active" banner on the
            // tenant welcome page once the loader forwards there.
            'tenantUrl'   => $tenantUrl . '/?activated=1',
            'statusUrl'   => route('companies.provision.status', ['id' => $id]),
            'fallbackUrl' => route('companies.provision.fallback', ['id' => $id]),
        ]);
    }
}
