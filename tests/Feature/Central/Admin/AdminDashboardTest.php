<?php

use App\Models\Central\CompanyDatabase;
use App\Services\Central\Admin\AdminDashboardService;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Support\Facades\Crypt;

beforeEach(function (): void {
    setCentralDomain();
    $this->seed(AdminUserSeeder::class);
});


// ─── Group 1: getStats() ──────────────────────────────────────────────────────

describe('getStats', function (): void {
    test('returns zero counts and empty collections when there are no companies', function (): void {
        $stats = app(AdminDashboardService::class)->getStats();

        expect($stats['totalCompanies'])->toBe(0)
            ->and($stats['pendingCompanies'])->toBe(0)
            ->and($stats['inactiveCompanies'])->toBe(0)
            ->and($stats['suspendedCompanies'])->toBe(0)
            ->and($stats['recentCompanies'])->toHaveCount(0)
            ->and($stats['recoveryCompanies'])->toHaveCount(0);
    });

    test('returns company counts grouped by status', function (): void {
        seedCompany(['company_name' => 'Pending One', 'subdomain' => 'p1', 'company_email' => 'p1@x.com', 'license_number' => 'L1', 'status' => 'pending']);
        seedCompany(['company_name' => 'Pending Two', 'subdomain' => 'p2', 'company_email' => 'p2@x.com', 'license_number' => 'L2', 'status' => 'pending']);
        seedCompany(['company_name' => 'Inactive One', 'subdomain' => 'i1', 'company_email' => 'i1@x.com', 'license_number' => 'L3', 'status' => 'inactive']);
        seedCompany(['company_name' => 'Suspended One', 'subdomain' => 's1', 'company_email' => 's1@x.com', 'license_number' => 'L4', 'status' => 'suspended']);
        seedCompany(['company_name' => 'Active One', 'subdomain' => 'a1', 'company_email' => 'a1@x.com', 'license_number' => 'L5', 'status' => 'active']);

        $stats = app(AdminDashboardService::class)->getStats();

        expect($stats['totalCompanies'])->toBe(5)
            ->and($stats['pendingCompanies'])->toBe(2)
            ->and($stats['inactiveCompanies'])->toBe(1)
            ->and($stats['suspendedCompanies'])->toBe(1);
    });

    test('recentCompanies is capped at four', function (): void {
        foreach (range(1, 5) as $i) {
            seedCompany(['company_name' => "Company {$i}", 'subdomain' => "co{$i}", 'company_email' => "co{$i}@x.com", 'license_number' => "LIC-{$i}"]);
        }

        expect(app(AdminDashboardService::class)->getStats()['recentCompanies'])->toHaveCount(4);
    });

    test('recoveryCompanies includes only verified companies without a database', function (): void {
        $recoverable = seedCompany(['company_name' => 'Recoverable Co', 'subdomain' => 'recoverable', 'company_email' => 'r@x.com', 'license_number' => 'LIC-R']);

        // Verified but already provisioned → excluded.
        $provisioned = seedCompany(['company_name' => 'Provisioned Co', 'subdomain' => 'provisioned', 'company_email' => 'p@x.com', 'license_number' => 'LIC-P']);
        CompanyDatabase::create([
            'company_id'  => $provisioned->id,
            'db_name'     => 'tenant_company_provisioned',
            'db_host'     => '127.0.0.1',
            'db_port'     => '3306',
            'db_username' => Crypt::encryptString('dbuser'),
            'db_password' => Crypt::encryptString('dbsecret'),
        ]);

        // Unverified → excluded.
        seedCompany(['company_name' => 'Unverified Co', 'subdomain' => 'unverified', 'company_email' => 'u@x.com', 'license_number' => 'LIC-U', 'email_verified_at' => null]);

        $recovery = app(AdminDashboardService::class)->getStats()['recoveryCompanies'];

        expect($recovery)->toHaveCount(1)
            ->and($recovery->first()->id)->toBe($recoverable->id);
    });
});


// ─── Group 2: Dashboard Page ──────────────────────────────────────────────────

describe('dashboard page', function (): void {
    test('guest is redirected to login', function (): void {
        $this->get('/admin/dashboard')
            ->assertRedirect(route('admin.login'));
    });

    test('authenticated super admin can view the dashboard', function (): void {
        $this->actingAs(seededAdmin(), 'admin')
            ->get('/admin/dashboard')
            ->assertOk();
    });

    test('the dashboard view receives the company statistics', function (): void {
        seedCompany(['company_name' => 'Pending One', 'subdomain' => 'pending1', 'company_email' => 'p1@x.com', 'license_number' => 'LIC-1', 'status' => 'pending']);
        seedCompany(['company_name' => 'Active One', 'subdomain' => 'active1', 'company_email' => 'a1@x.com', 'license_number' => 'LIC-2', 'status' => 'active']);

        $this->actingAs(seededAdmin(), 'admin')
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertViewHas('totalCompanies', 2)
            ->assertViewHas('pendingCompanies', 1);
    });
});
