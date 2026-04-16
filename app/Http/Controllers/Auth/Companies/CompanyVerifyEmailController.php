<?php

namespace App\Http\Controllers\Auth\Companies;

use App\Http\Controllers\Controller;
use App\Models\Company;
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
            }

            return redirect()->route('login')->with('status', 'Company email verified successfully. Your account is now active.');
        } catch (Throwable $e) {
            Log::error('Company email verification failed.', [
                'company_id' => $id,
                'exception' => $e->getMessage(),
            ]);

            return redirect()->route('login')->with('error', 'Unable to verify company email right now. Please try again.');
        }
    }
}
