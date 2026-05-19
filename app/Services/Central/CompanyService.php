<?php

namespace App\Services\Central;

use App\Models\Central\Company;
use App\Jobs\CreateCompanyDatabase;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
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

    /**
     * Update a company record and sync the change into its tenant database.
     *
     * @throws Throwable
     */
    public function updateCompany(Company $company, array $data): void
    {
        DB::transaction(function () use ($company, $data) {
            $company->update($data);

            if ($company->database?->db_name) {
                Config::set('database.connections.tenant.database', $company->database->db_name);
                DB::purge('tenant');

                DB::connection('tenant')->table('companies')
                    ->where('master_company_id', $company->id)
                    ->update($data);
            }
        });
    }

    /**
     * Drop the tenant database (if it exists) and delete the central company record.
     *
     * @throws Exception
     */
    public function deleteCompany(Company $company): void
    {
        $dbName = $company->database?->db_name;

        if ($dbName) {
            DB::statement("DROP DATABASE IF EXISTS `{$dbName}`");
        }

        $company->delete();
    }

    /**
     * Bulk-delete companies by ID; returns the count of successfully deleted records.
     */
    public function bulkDeleteCompanies(array $ids): int
    {
        $companies   = Company::whereIn('id', $ids)->with('database')->get();
        $deletedCount = 0;

        foreach ($companies as $company) {
            try {
                $this->deleteCompany($company);
                $deletedCount++;
            } catch (Exception $e) {
                activity()
                    ->withProperties(['error' => $e->getMessage(), 'company_id' => $company->id])
                    ->log('Bulk delete failed for company');
            }
        }

        return $deletedCount;
    }

    /**
     * Search companies by name or email; returns up to 5 matches.
     */
    public function searchCompanies(string $query): Collection
    {
        return Company::where('company_name', 'LIKE', "%{$query}%")
            ->orWhere('company_email', 'LIKE', "%{$query}%")
            ->limit(5)
            ->get(['id', 'company_name', 'company_email']);
    }

    /**
     * Mark a company email as verified, activate the account, and dispatch database provisioning.
     */
    public function verifyEmail(Company $company): void
    {
        if (! $company->hasVerifiedEmail()) {
            $company->markEmailAsVerified();
            $company->update(['status' => 'active']);
            CreateCompanyDatabase::dispatch($company);
        }
    }

    /**
     * Re-send the email verification notification.
     */
    public function resendVerificationEmail(Company $company): void
    {
        $company->sendEmailVerificationNotification();
    }

    /**
     * Dispatch database provisioning for an eligible company.
     *
     * @throws InvalidArgumentException  when the company is not eligible.
     * @throws Exception                 when the job dispatch fails.
     */
    public function provisionDatabase(Company $company): void
    {
        if (! $company->email_verified_at || $company->database()->exists()) {
            throw new InvalidArgumentException('This company is not eligible for database provisioning.');
        }

        CreateCompanyDatabase::dispatch($company);
    }
}
