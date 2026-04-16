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
            ]);

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
