<?php

namespace App\Services\Tenant\Admin;

use App\Models\Tenant\User;
use Illuminate\Support\Facades\Log;

class TenantDashboardService
{
    /**
     * Get dashboard statistics for the tenant admin.
     *
     * @return array
     */
    public function getStats(): array
    {
        try {
            return [
                'usersCount'           => User::count(),
                'unverifiedUsersCount' => User::whereNull('email_verified_at')->count(),
            ];
        } catch (\Exception $e) {
            Log::error('TenantDashboardService::getStats', ['error' => $e->getMessage()]);
            throw new \Exception('Failed to load dashboard statistics. Please try again.');
        }
    }
}
