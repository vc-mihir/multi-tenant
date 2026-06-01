<?php

namespace App\Services\Tenant\Admin;

use App\Models\Central\Company as CentralCompany;
use App\Models\Tenant\Company;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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

        try {
            DB::transaction(function () use ($tenantUser, $data) {
                $tenantUser->update($data);

                CentralCompany::on('mysql')
                    ->where('subdomain', $tenantUser->subdomain)
                    ->update($data);
            });

            Auth::guard('company')->login($tenantUser);
        } catch (Exception $e) {
            Log::error('TenantAdminProfileService::update', [
                'subdomain' => $tenantUser->subdomain,
                'error'     => $e->getMessage(),
            ]);
            throw new Exception('Failed to update company profile. Please try again.');
        }
    }

    /**
     * Soft-delete the company central record (tenant database is preserved for potential restoration).
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
            try {
                $centralCompany->delete();
            } catch (Exception $e) {
                Log::error('TenantAdminProfileService::deleteAccount', [
                    'subdomain' => $tenantUser->subdomain,
                    'error'     => $e->getMessage(),
                ]);
                throw new Exception('Failed to delete company account. Please try again.');
            }
        }

        Auth::guard('company')->logout();
        request()->session()->regenerate();
    }
}
