<?php

namespace App\Console\Commands\Tenant;

use App\Models\Central\CompanyDatabase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class TenantsMigrate extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tenants:migrate {tenant?}';

    /**
     * The console command description.
     */
    protected $description = 'Run migrations for existing tenants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant');

        if ($tenantId) {
            $tenants = CompanyDatabase::where('company_id', $tenantId)->get();
            if ($tenants->isEmpty()) {
                return $this->error("No database found for Tenant ID: {$tenantId}");
            }
        } else {
            $tenants = CompanyDatabase::all();
            if ($tenants->isEmpty()) {
                return $this->info('No tenants found in the central database.');
            }
        }

        $this->info("Found {$tenants->count()} tenants. Starting migrations...");

        foreach ($tenants as $tenant) {
            $this->migrateTenant($tenant);
        }

        $this->info('All tenant migration tasks completed.');
    }

    /**
     * Migrate a single tenant.
     *
     * @param CompanyDatabase $tenant
     * @return void
     */
    protected function migrateTenant(CompanyDatabase $tenant)
    {
        $this->line("Migrating tenant: {$tenant->db_name}");

        try {
            Config::set('database.connections.tenant.database', $tenant->db_name);
            Config::set('database.connections.tenant.host', $tenant->db_host);
            Config::set('database.connections.tenant.port', $tenant->db_port);
            Config::set('database.connections.tenant.username', Crypt::decryptString($tenant->db_username));
            Config::set('database.connections.tenant.password', Crypt::decryptString($tenant->db_password));

            DB::purge('tenant');
            DB::reconnect('tenant');

            Artisan::call('migrate', [
                '--database' => 'tenant',
                '--path'     => 'database/migrations/tenant',
                '--force'    => true,
            ]);

            $output = Artisan::output();

            if (str_contains($output, 'Nothing to migrate')) {
                $this->comment("Nothing to migrate for {$tenant->db_name}");
            } else {
                $this->info("Success: Migrated {$tenant->db_name}");
                $this->line($output);
            }
        } catch (\Exception $e) {
            $this->error("Failed with error for {$tenant->db_name}: " . $e->getMessage());
        } finally {
            DB::disconnect('tenant');
        }

        $this->line('-----------------------------------------');
    }
}
