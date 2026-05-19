<?php

namespace App\Services\Tenant\Admin;

use App\Models\Tenant\User;

class TenantDashboardService
{
    /**
     * Get dashboard statistics for the tenant admin.
     *
     * @return array
     */
    public function getStats(): array
    {
        return [
            'usersCount'           => User::count(),
            'unverifiedUsersCount' => User::whereNull('email_verified_at')->count(),
        ];
    }
}
