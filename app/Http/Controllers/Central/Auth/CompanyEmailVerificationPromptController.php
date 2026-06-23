<?php

namespace App\Http\Controllers\Central\Auth;

use App\Http\Controllers\Controller;
use App\Models\Central\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CompanyEmailVerificationPromptController extends Controller
{
    /**
     * Display company email verification prompt
     *
     * @param string $id
     * @return View|RedirectResponse
     */
    public function __invoke(string $id): View|RedirectResponse
    {
        $company = Company::findOrFail($id);

        // An already-verified company must NOT render the "please verify" prompt.
        // Returning a redirect here (instead of throwing) is what prevents an
        // ERR_TOO_MANY_REDIRECTS loop: the global exception handler converts any
        // thrown exception into redirect()->back(), and back() can resolve to
        // this very page — so throwing here would bounce to itself forever.
        if ($company->hasVerifiedEmail()) {
            return redirect()->route('register')
                ->with('success', 'This company email is already verified — your account is active.');
        }

        return view('central.auth.verify-email', compact('company'));
    }
}
