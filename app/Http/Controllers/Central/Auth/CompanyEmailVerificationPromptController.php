<?php

namespace App\Http\Controllers\Central\Auth;

use App\Http\Controllers\Controller;
use App\Models\Central\Company;
use Illuminate\Http\RedirectResponse;
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
                return redirect()->route('register')->with('status', 'Company account is already active.');
            }

            return view('central.auth.verify-email', compact('company'));
        } catch (Throwable $e) {
            activity()->withProperties([
                'company_id' => $id,
                'exception' => $e->getMessage(),
            ])->log('Company verification prompt failed');

            return redirect()->route('register')->with('error', 'Unable to load verification page right now. Please try again.');
        }
    }
}
