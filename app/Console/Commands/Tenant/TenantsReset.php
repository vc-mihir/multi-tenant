<?php

namespace App\Console\Commands\Tenant;

use App\Models\Central\CompanyDatabase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class TenantsReset extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tenants:reset {tenant? : The company_id of a specific tenant}';

    /**
     * The console command description.
     */
    protected $description = 'Rollback all migrations for existing tenants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant');

        if ($tenantId) {
            $tenants = CompanyDatabase::where('company_id', $tenantId)->get();
            if ($tenants->isEmpty()) {
                $this->error("No database found for Tenant ID: {$tenantId}");
                return Command::FAILURE;
            }
        } else {
            $tenants = CompanyDatabase::all();
            if ($tenants->isEmpty()) {
                $this->info('No tenants found in the central database.');
                return Command::SUCCESS;
            }
        }

        $this->info("Found {$tenants->count()} tenants. Resetting (Full Rollback)...");

        foreach ($tenants as $tenant) {
            $this->resetTenant($tenant);
        }

        $this->info('All tenant reset tasks completed.');
        return Command::SUCCESS;
    }

    /**
     * Reset a single tenant.
     */
    protected function resetTenant(CompanyDatabase $tenant)
    {
        $this->line("Resetting tenant: {$tenant->db_name}");

        try {
            Config::set('database.connections.tenant.database', $tenant->db_name);
            Config::set('database.connections.tenant.host', $tenant->db_host);
            Config::set('database.connections.tenant.port', $tenant->db_port);
            Config::set('database.connections.tenant.username', Crypt::decryptString($tenant->db_username));
            Config::set('database.connections.tenant.password', Crypt::decryptString($tenant->db_password));

            DB::purge('tenant');
            DB::reconnect('tenant');

            Artisan::call('migrate:reset', [
                '--database' => 'tenant',
                '--path'     => 'database/migrations/tenant',
                '--force'    => true,
            ]);

            $output = Artisan::output();

            if (str_contains($output, 'Nothing to rollback')) {
                $this->comment("Nothing to reset for {$tenant->db_name}");
            } else {
                $this->info("Success: Reset {$tenant->db_name}");
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
