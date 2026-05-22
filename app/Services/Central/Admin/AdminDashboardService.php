<?php

namespace App\Services\Central\Admin;

use App\Models\Central\Company;
use Illuminate\Support\Facades\Log;

class AdminDashboardService
{
    /**
     * Get dashboard statistics
     *
     * @return array
     */
    public function getStats(): array
    {
        try {
            return [
                'totalCompanies'     => Company::count(),
                'pendingCompanies'   => Company::where('status', 'pending')->count(),
                'inactiveCompanies'  => Company::where('status', 'inactive')->count(),
                'suspendedCompanies' => Company::where('status', 'suspended')->count(),
                'recentCompanies'    => Company::latest()->take(4)->get(),
                'recoveryCompanies'  => Company::whereNotNull('email_verified_at')
                    ->whereDoesntHave('database')
                    ->get(),
            ];
        } catch (\Exception $e) {
            Log::error('AdminDashboardService::getStats', ['error' => $e->getMessage()]);
            throw new \Exception('Failed to load dashboard statistics.');
        }
    }
}
