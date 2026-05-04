<?php

namespace App\Services;

use App\Models\Central\Company;
use App\Jobs\CreateCompanyDatabase;
use Illuminate\Support\Facades\DB;
use Throwable;

class CompanyService
{
    /**
     * Create a new company record and handle verification/provisioning logic.
     *
     * @param array $data
     * @param bool $isAdminCreation
     * @return Company
     * @throws Throwable
     */
    public function createCompany(array $data, bool $isAdminCreation = false): Company
    {
        return DB::transaction(function () use ($data, $isAdminCreation) {
            $company = Company::create([
                'company_name' => $data['company_name'],
                'subdomain' => $data['subdomain'],
                'company_email' => $data['company_email'],
                'website' => $data['website'] ?? null,
                'license_number' => $data['license_number'] ?? null,
                'address' => $data['address'] ?? null,
                'country' => $data['country'] ?? null,
                'state' => $data['state'] ?? null,
                'city' => $data['city'] ?? null,
                'password' => $data['password'],
                'status' => $isAdminCreation ? 'active' : 'inactive',
                'email_verified_at' => $isAdminCreation ? now() : null,
            ]);

            $company->assignRole('Company');

            if ($isAdminCreation) {
                DB::afterCommit(function () use ($company) {
                    CreateCompanyDatabase::dispatch($company);
                });
            } else {
                $company->sendEmailVerificationNotification();
            }

            return $company;
        });
    }
}
