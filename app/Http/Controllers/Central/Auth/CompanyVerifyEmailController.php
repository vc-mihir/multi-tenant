<?php

namespace App\Http\Controllers\Central\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\CreateCompanyDatabase;
use App\Models\Central\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class CompanyVerifyEmailController extends Controller
{
    /**
     * Mark the company's email address as verified and activate the account.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function __invoke(int $id): RedirectResponse
    {
        try {
            $company = Company::findOrFail($id);

            if (! $company->hasVerifiedEmail()) {
                $company->markEmailAsVerified();
                $company->update([
                    'status' => 'active',
                ]);

                CreateCompanyDatabase::dispatch($company);
            }

            return redirect()->route('register')->with('status', 'Company email verified successfully. Your account is now active.');
        } catch (Throwable $e) {
            Log::error('Company email verification failed.', [
                'company_id' => $id,
                'exception' => $e->getMessage(),
            ]);

            return redirect()->route('register')->with('error', 'Unable to verify company email right now. Please try again.');
        }
    }
}
