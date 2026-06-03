<?php

namespace App\Services\Central;

use App\Jobs\CreateCompanyDatabase;
use App\Models\Central\Company;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class CompanyService
{
    /**
     * Create a new company and handle verification or provisioning
     *
     * @param array $data
     * @param bool $isAdminCreation
     * @return Company
     */
    public function createCompany(array $data, bool $isAdminCreation = false): Company
    {
        try {
            return DB::transaction(function () use ($data, $isAdminCreation) {
                $company = Company::create([
                    'company_name'      => $data['company_name'],
                    'subdomain'         => $data['subdomain'],
                    'company_email'     => $data['company_email'],
                    'website'           => $data['website'],
                    'license_number'    => $data['license_number'],
                    'address'           => $data['address'],
                    'country'           => $data['country'],
                    'state'             => $data['state'],
                    'city'              => $data['city'],
                    'password'          => Hash::make($data['password']),
                    'status'            => $isAdminCreation ? 'active' : 'inactive',
                    'email_verified_at' => $isAdminCreation ? now() : null,
                ]);

                if ($isAdminCreation) {
                    DB::afterCommit(function () use ($company) {
                        CreateCompanyDatabase::dispatch($company);
                    });
                } else {
                    $company->sendEmailVerificationNotification();
                }

                return $company;
            });
        } catch (\Exception $e) {
            Log::error('CompanyService::createCompany', [
                'company_email' => $data['company_email'] ?? null,
                'error'         => $e->getMessage(),
            ]);
            throw new \Exception('Failed to create company. Please try again.');
        }
    }

    /**
     * Update company details and sync to tenant database
     *
     * @param Company $company
     * @param array $data
     * @return void
     */
    public function updateCompany(Company $company, array $data): void
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('CompanyService::updateCompany', [
                'company_id' => $company->id,
                'error'      => $e->getMessage(),
            ]);
            throw new \Exception('Failed to update company details.');
        }
    }

    /**
     * Soft-delete a company record (tenant database is preserved for potential restoration)
     *
     * @param Company $company
     * @return void
     */
    public function deleteCompany(Company $company): void
    {
        try {
            $company->delete();

            activity()
                ->causedBy(Auth::user())
                ->performedOn($company)
                ->event('deleted')
                ->withProperties(['company_id' => $company->id])
                ->log('Company soft-deleted');
        } catch (\Exception $e) {
            Log::error('CompanyService::deleteCompany', [
                'company_id' => $company->id,
                'error'      => $e->getMessage(),
            ]);
            throw new \Exception('Failed to delete company.');
        }
    }

    /**
     * Restore a soft-deleted company record
     *
     * @param Company $company
     * @return void
     */
    public function restoreCompany(Company $company): void
    {
        try {
            $company->restore();

            activity()
                ->causedBy(Auth::user())
                ->performedOn($company)
                ->event('restored')
                ->withProperties(['company_id' => $company->id])
                ->log('Company restored from archive');
        } catch (\Exception $e) {
            Log::error('CompanyService::restoreCompany', [
                'company_id' => $company->id,
                'error'      => $e->getMessage(),
            ]);
            throw new \Exception('Failed to restore company.');
        }
    }

    /**
     * Permanently delete a soft-deleted company and drop its tenant database
     *
     * @param Company $company
     * @return void
     */
    public function forceDeleteCompany(Company $company): void
    {
        try {
            $dbName = $company->database?->db_name;

            if ($dbName) {
                DB::statement("DROP DATABASE IF EXISTS `{$dbName}`");

                activity()
                    ->causedBy(Auth::user())
                    ->performedOn($company)
                    ->event('force_deleted')
                    ->withProperties(['company_id' => $company->id, 'db_name' => $dbName])
                    ->log('Tenant database dropped on permanent deletion');
            }

            $company->forceDelete();
        } catch (\Exception $e) {
            Log::error('CompanyService::forceDeleteCompany', [
                'company_id' => $company->id,
                'error'      => $e->getMessage(),
            ]);
            throw new \Exception('Failed to permanently delete company.');
        }
    }

    /**
     * Bulk delete companies and their tenant databases
     *
     * @param array $ids
     * @return int
     */
    public function bulkDeleteCompanies(array $ids): int
    {
        try {
            $companies    = Company::whereIn('id', $ids)->with('database')->get();
            $deletedCount = 0;

            foreach ($companies as $company) {
                try {
                    $this->deleteCompany($company);
                    $deletedCount++;
                } catch (\Exception $e) {
                    activity()
                        ->causedBy(Auth::user())
                        ->performedOn($company)
                        ->event('delete_failed')
                        ->withProperties(['error' => $e->getMessage(), 'company_id' => $company->id])
                        ->log('Bulk delete failed for company');
                }
            }

            return $deletedCount;
        } catch (\Exception $e) {
            Log::error('CompanyService::bulkDeleteCompanies', [
                'ids'   => $ids,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to bulk delete companies.');
        }
    }

    /**
     * Search companies by name or email
     *
     * @param string $query
     * @return Collection
     */
    public function searchCompanies(string $query): Collection
    {
        try {
            if (empty($query)) {
                return collect();
            }

            $emailHash = hash('sha256', strtolower($query));

            return Company::where('company_name', 'LIKE', "%{$query}%")
                ->orWhere('company_email_hash', $emailHash)
                ->limit(5)
                ->get(['id', 'company_name', 'company_email']);
        } catch (\Exception $e) {
            Log::error('CompanyService::searchCompanies', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to search companies.');
        }
    }

    /**
     * Verify company email, activate account and dispatch database provisioning
     *
     * @param string $id
     * @return string
     */
    public function verifyEmail(string $id): string
    {
        try {
            $company = Company::findOrFail($id);

            if (! $company->hasVerifiedEmail()) {
                $company->markEmailAsVerified();
                $company->update(['status' => 'active']);
                CreateCompanyDatabase::dispatch($company);
            }

            $baseHost = parse_url(config('app.url'), PHP_URL_HOST);

            return 'http://' . $company->subdomain . '.' . $baseHost;
        } catch (\Exception $e) {
            Log::error('CompanyService::verifyEmail', [
                'id'    => $id,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Unable to verify company email. Please try again.');
        }
    }

    /**
     * Resend email verification notification to company
     *
     * @param string $id
     * @return void
     */
    public function resendVerificationEmail(string $id): void
    {
        try {
            $company = Company::findOrFail($id);

            if ($company->hasVerifiedEmail()) {
                throw new \Exception('Company account is already active.');
            }

            $company->sendEmailVerificationNotification();
        } catch (\Exception $e) {
            Log::error('CompanyService::resendVerificationEmail', [
                'id'    => $id,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Fetch a company that has not yet verified its email
     *
     * @param string $id
     * @return Company
     */
    public function getUnverifiedCompany(string $id): Company
    {
        try {
            $company = Company::findOrFail($id);

            if ($company->hasVerifiedEmail()) {
                throw new \Exception('Company account is already active.');
            }

            return $company;
        } catch (\Exception $e) {
            Log::error('CompanyService::getUnverifiedCompany', [
                'id'    => $id,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Dispatch tenant database provisioning job for an eligible company
     *
     * @param Company $company
     * @return void
     */
    public function provisionDatabase(Company $company): void
    {
        try {
            if (! $company->email_verified_at || $company->database()->exists()) {
                throw new \Exception('This company is not eligible for database provisioning.');
            }

            CreateCompanyDatabase::dispatch($company);
        } catch (\Exception $e) {
            Log::error('CompanyService::provisionDatabase', [
                'company_id' => $company->id,
                'error'      => $e->getMessage(),
            ]);
            throw new \Exception($e->getMessage());
        }
    }
}
