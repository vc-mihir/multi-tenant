<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\CompanyDatabase;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CreateCompanyDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param Company $company
     */
    public function __construct(public Company $company)
    {
    }

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
            DB::connection('tenant')->table('companies')->updateOrInsert(
                ['master_company_id' => $this->company->id],
                [
                    'company_name' => $this->company->company_name,
                    'subdomain' => $this->company->subdomain,
                    'company_email' => $this->company->company_email,
                    'website' => $this->company->website,
                    'license_number' => $this->company->license_number,
                    'address' => $this->company->address,
                    'country' => $this->company->country,
                    'state' => $this->company->state,
                    'city' => $this->company->city,
                    'password' => $this->company->password,
                    'status' => $this->company->status,
                    'email_verified_at' => $this->company->email_verified_at,
                    'created_at' => $this->company->created_at ?? now(),
                    'updated_at' => now(),
                ],
            );

            $defaultPassword = Hash::make('Hello@123');

            User::on('tenant')->create([
                'name' => 'Admin User',
                'email' => 'admin@test.com',
                'password' => $defaultPassword,
                'email_verified_at' => now(),
            ]);

            User::on('tenant')->create([
                'name' => 'Staff User',
                'email' => 'staff@test.com',
                'password' => $defaultPassword,
                'email_verified_at' => now(),
            ]);

            // 4. Update Master Database with Connection Info
            $defaultConnection = config('database.default');
            CompanyDatabase::updateOrCreate(
                ['company_id' => $this->company->id],   
                [
                    'db_name' => $dbName,
                    'db_host' => config("database.connections.{$defaultConnection}.host"),
                    'db_port' => config("database.connections.{$defaultConnection}.port"),
                    'db_username' => config("database.connections.{$defaultConnection}.username"),
                    'db_password' => config("database.connections.{$defaultConnection}.password"),
                ]
            );

            Log::info('Company database created and seeded successfully.', [
                'company_id' => $this->company->id,
                'database_name' => $dbName,
            ]);
        } catch (Throwable $e) {
            Log::error('Company database creation failed.', [
                'company_id' => $this->company->id,
                'database_name' => $dbName,
                'exception' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
