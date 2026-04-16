<?php

namespace App\Jobs;

use App\Models\Company;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateCompanyDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Company $company)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $dbName = $this->company->database_name;

        try {
            DB::statement("CREATE DATABASE `{$dbName}`");

            config(['database.connections.tenant.database' => $dbName]);
            DB::purge('tenant');
            Artisan::call('migrate', [
                '--database' => 'tenant',
                '--path' => 'database/migrations/tenant',
                '--force' => true,
            ]);

            DB::connection('tenant')->table('companies')->updateOrInsert(
                ['master_company_id' => $this->company->id],
                [
                    'company_name' => $this->company->company_name,
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

            Log::info('Company database created successfully.', [
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
