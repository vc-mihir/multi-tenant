<?php

namespace App\Http\Controllers\Auth\Companies;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\View\View;

class CompanyEmailVerificationPromptController extends Controller
{
    /**
     * Display the company email verification prompt.
     *
     * @param int $id
     * @return RedirectResponse|View
     */
    public function __invoke(int $id): RedirectResponse|View
    {
        try {
            $company = Company::findOrFail($id);

            if ($company->hasVerifiedEmail()) {
                return redirect()->route('login')->with('status', 'Company account is already active.');
            }

            return view('auth.verify-email', compact('company'));
        } catch (Throwable $e) {
            Log::error('Company verification prompt failed.', [
                'company_id' => $id,
                'exception' => $e->getMessage(),
            ]);

            return redirect()->route('login')->with('error', 'Unable to load verification page right now. Please try again.');
        }
    }
}
