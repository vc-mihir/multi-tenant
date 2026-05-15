<?php

namespace App\Http\Controllers\Central\Auth;

use App\Http\Controllers\Controller;
use App\Models\Central\Company;
use Illuminate\Http\RedirectResponse;
use Throwable;

class CompanyEmailVerificationNotificationController extends Controller
{
    /**
     * Queue a fresh company email verification notification.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function store(int $id): RedirectResponse
    {
        try {
            $company = Company::findOrFail($id);

            if ($company->hasVerifiedEmail()) {
                return redirect()->route('register')->with('status', 'Company account is already active.');
            }

            $company->sendEmailVerificationNotification();

            return back()->with('status', 'Verification link sent successfully. Please check your inbox.');
        } catch (Throwable $e) {
            activity()->withProperties([
                'company_id' => $id,
                'exception' => $e->getMessage(),
            ])->log('Company verification resend failed');

            return back()->with('error', 'Unable to resend verification email right now. Please try again.');
        }
    }
}
