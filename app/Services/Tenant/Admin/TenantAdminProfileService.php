<?php

namespace App\Services\Tenant\Admin;

use App\Models\Central\Company as CentralCompany;
use App\Models\Tenant\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TenantAdminProfileService
{
    /**
     * Update the company profile in both tenant and central databases.
     *
     * @param Company $tenantUser
     * @param array $data
     * @return void
     */
    public function update(Company $tenantUser, array $data): void
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        DB::transaction(function () use ($tenantUser, $data) {
            $tenantUser->update($data);

            CentralCompany::on('mysql')
                ->where('subdomain', $tenantUser->subdomain)
                ->update($data);
        });

        Auth::guard('company')->login($tenantUser);
    }

    /**
     * Delete the company central record and drop the tenant database.
     *
     * @param Company $tenantUser
     * @return void
     */
    public function deleteAccount(Company $tenantUser): void
    {
        $centralCompany = CentralCompany::on('mysql')
            ->where('subdomain', $tenantUser->subdomain)
            ->first();

        if ($centralCompany) {
            $dbName = $centralCompany->database->db_name ?? null;

            DB::transaction(function () use ($centralCompany) {
                $centralCompany->delete();
            });

            if ($dbName) {
                DB::connection('mysql')->statement("DROP DATABASE IF EXISTS `{$dbName}`");
            }
        }
    }
}
