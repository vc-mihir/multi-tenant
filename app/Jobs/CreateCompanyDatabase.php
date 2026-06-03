<?php

namespace App\Jobs;

use App\Models\Central\Company;
use App\Models\Central\CompanyDatabase;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;

class CreateCompanyDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param Company $company
     */
    public function __construct(public Company $company) {}

    /**
     * Handle the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $dbName = 'tenant_company_' . Str::slug($this->company->company_name, '_');

        try {
            // 1. Create and Configure Database
            DB::statement("CREATE DATABASE IF NOT EXISTS `{$dbName}`");

            config(['database.connections.tenant.database' => $dbName]);
            DB::purge('tenant');

            // 2. Run Migrations
            Artisan::call('migrate', [
                '--database' => 'tenant',
                '--path' => 'database/migrations/tenant',
                '--force' => true,
            ]);

            // 3. Seed Tenant Data
            $tenantCompanies = DB::connection('tenant')->table('companies');
            $plainEmail   = $this->company->company_email;
            $plainLicense = $this->company->license_number;
            $tenantData = [
                'company_name'          => $this->company->company_name,
                'subdomain'             => $this->company->subdomain,
                'company_email'         => encrypt($plainEmail),
                'company_email_hash'    => hash('sha256', strtolower($plainEmail)),
                'website'               => $this->company->website,
                'license_number'        => encrypt($plainLicense),
                'license_number_hash'   => hash('sha256', strtolower($plainLicense)),
                'address'               => $this->company->address,
                'country'               => $this->company->country,
                'state'                 => $this->company->state,
                'city'                  => $this->company->city,
                'password'              => $this->company->password,
                'status'                => $this->company->status,
                'email_verified_at'     => $this->company->email_verified_at,
                'updated_at'            => now(),
            ];

            if ($tenantCompanies->where('master_company_id', $this->company->id)->exists()) {
                $tenantCompanies->where('master_company_id', $this->company->id)->update($tenantData);
            } else {
                $tenantCompanies->insert(array_merge($tenantData, [
                    'id'                => (string) Str::uuid(),
                    'master_company_id' => $this->company->id,
                    'created_at'        => $this->company->created_at ?? now(),
                ]));
            }


            // 4. Update Master Database with Connection Info
            $defaultConnection = config('database.default');
            CompanyDatabase::updateOrCreate(
                ['company_id' => $this->company->id],
                [
                    'db_name' => $dbName,
                    'db_host' => config("database.connections.{$defaultConnection}.host"),
                    'db_port' => config("database.connections.{$defaultConnection}.port"),
                    'db_username' => Crypt::encryptString(config("database.connections.{$defaultConnection}.username")),
                    'db_password' => Crypt::encryptString(config("database.connections.{$defaultConnection}.password")),
                ]
            );
        } catch (Throwable $e) {
            activity()->withProperties([
                'company_id' => $this->company->id,
                'database_name' => $dbName,
                'exception' => $e->getMessage(),
            ])->log('Company database creation failed');

            throw $e;
        }
    }
}
