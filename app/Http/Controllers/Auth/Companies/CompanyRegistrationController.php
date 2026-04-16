<?php

namespace App\Http\Controllers\Auth\Companies;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CompanyRegistrationRequest;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;
use Illuminate\View\View;

class CompanyRegistrationController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return View
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Validate the incoming company registration request.
     *
     * @param CompanyRegistrationRequest $request
     * @return RedirectResponse
     */
    public function store(CompanyRegistrationRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $defaultConnection = config('database.default');
            $databaseName = 'tenant_company_' . Str::slug($validated['company_name'], '_');

            $company = Company::create([
                'company_name' => $validated['company_name'],
                'company_email' => $validated['company_email'],
                'password' => $validated['password'],
                'status' => 'inactive',
                'email_verified_at' => null,
                'database_name' => $databaseName,
                'database_connection_details' => [
                    'connection' => $defaultConnection,
                    'driver' => config("database.connections.{$defaultConnection}.driver"),
                    'host' => config("database.connections.{$defaultConnection}.host"),
                    'port' => config("database.connections.{$defaultConnection}.port"),
                    'database' => $databaseName,
                ],
            ]);

            $company->sendEmailVerificationNotification();

            return redirect()->route('companies.verification.notice', [
                'id' => $company->id,
            ]);
        } catch (Throwable $e) {
            Log::error('Company registration email verification setup failed.', [
                'company_email' => $validated['company_email'] ?? null,
                'company_name' => $validated['company_name'] ?? null,
                'exception' => $e->getMessage(),
            ]);

            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->with('error', 'Unable to complete registration right now. Please try again.');
        }
    }
}
