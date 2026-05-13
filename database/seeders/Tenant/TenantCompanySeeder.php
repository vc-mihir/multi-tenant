<?php

namespace Database\Seeders\Tenant;

use App\Models\Central\CompanyDatabase;
use App\Models\Tenant\Company as TenantCompany;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantCompanySeeder extends Seeder
{
    /**
     * Seed the tenant's company details from the central database.
     */
    public function run(): void
    {
        $dbName = DB::connection('tenant')->getDatabaseName();

        $companyDatabase = CompanyDatabase::where('db_name', $dbName)->first();

        if (!$companyDatabase) {
            return;
        }

        $centralCompany = $companyDatabase->company;

        if (!$centralCompany) {
            return;
        }

        TenantCompany::updateOrCreate(
            ['master_company_id' => $centralCompany->id],
            [
                'company_name'      => $centralCompany->company_name,
                'subdomain'         => $centralCompany->subdomain,
                'company_email'     => $centralCompany->company_email,
                'website'           => $centralCompany->website,
                'license_number'    => $centralCompany->license_number,
                'address'           => $centralCompany->address,
                'country'           => $centralCompany->country,
                'state'             => $centralCompany->state,
                'city'              => $centralCompany->city,
                'password'          => $centralCompany->password,
                'status'            => $centralCompany->status,
                'email_verified_at' => $centralCompany->email_verified_at,
            ]
        );
    }
}
