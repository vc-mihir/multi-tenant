<?php

namespace Database\Seeders;

use App\Jobs\CreateCompanyDatabase;
use App\Models\Central\Company;
use App\Models\Central\CompanyDatabase;
use Database\Seeders\Tenant\TenantDatabaseSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    /**
     * Seed a demo company record into the central database and provision its tenant database.
     *
     * Creates the central company record, then synchronously runs the same
     * CreateCompanyDatabase job used by the web flow: creates the MySQL database,
     * runs tenant migrations, seeds the tenant companies table, and stores
     * encrypted DB credentials in company_databases.
     *
     * @return void
     */
    public function run(): void
    {
        $company = Company::firstOrCreate(
            ['company_email_hash' => hash('sha256', strtolower('mihir@test.com'))],
            [
                'company_name'      => 'Mihir',
                'subdomain'         => 'mihir',
                'website'           => 'https://mihir.test',
                'license_number'    => 'LIC-2026-001',
                'address'           => '123 Main Street',
                'country'           => 'India',
                'state'             => 'Gujarat',
                'city'              => 'Ahmedabad',
                'password'          => Hash::make('Hello@123'),
                'status'            => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Provision the tenant DB if it hasn't been created yet.
        if ($company->database()->doesntExist()) {
            CreateCompanyDatabase::dispatchSync($company);
        }

        // Point the tenant connection at this company's database and seed tenant data.
        $dbRecord = CompanyDatabase::where('company_id', $company->id)->firstOrFail();
        config(['database.connections.tenant.database' => $dbRecord->db_name]);
        DB::purge('tenant');

        $this->call(TenantDatabaseSeeder::class);
    }
}
