<?php

namespace App\Services\Central\Admin;

use App\Models\Central\Company;

class AdminDashboardService
{
    /**
     * Get dashboard stats
     *
     * @return array
     */
    public function getStats(): array
    {
        return [
            'totalCompanies'    => Company::count(),
            'pendingCompanies'  => Company::where('status', 'pending')->count(),
            'inactiveCompanies' => Company::where('status', 'inactive')->count(),
            'suspendedCompanies'=> Company::where('status', 'suspended')->count(),
            'recentCompanies'   => Company::latest()->take(4)->get(),
            'recoveryCompanies' => Company::whereNotNull('email_verified_at')
                ->whereDoesntHave('database')
                ->get(),
        ];
    }
}
