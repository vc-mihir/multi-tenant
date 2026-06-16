<?php

use App\Services\Tenant\Admin\TenantDashboardService;
use Illuminate\Support\Facades\DB;

beforeEach(function (): void {
    setUpTenantDb();
    seedCompany([
        'company_email' => 'admin@acme.com',
        'password'      => 'Admin@123',
    ]);
    setTenantDomain('acme');
});

afterEach(function (): void {
    DB::setDefaultConnection('mysql');
    DB::purge('tenant');
});


// ─── Group 1: getStats() ──────────────────────────────────────────────────────

describe('getStats', function (): void {
    test('returns zero counts when there are no tenant users', function (): void {
        $stats = app(TenantDashboardService::class)->getStats();

        expect($stats['usersCount'])->toBe(0)
            ->and($stats['unverifiedUsersCount'])->toBe(0);
    });

    test('returns the total tenant users count', function (): void {
        makeTenantUser(['email' => 'a@acme.com']);
        makeTenantUser(['email' => 'b@acme.com']);

        expect(app(TenantDashboardService::class)->getStats()['usersCount'])->toBe(2);
    });

    test('counts only users without a verified email in unverifiedUsersCount', function (): void {
        makeTenantUser(['email' => 'verified@acme.com', 'email_verified_at' => now()]);
        makeTenantUser(['email' => 'pending@acme.com', 'email_verified_at' => null]);

        $stats = app(TenantDashboardService::class)->getStats();

        expect($stats['usersCount'])->toBe(2)
            ->and($stats['unverifiedUsersCount'])->toBe(1);
    });
});


// ─── Group 2: Dashboard Page ──────────────────────────────────────────────────

describe('dashboard page', function (): void {
    test('guest is redirected to login', function (): void {
        $this->get(tenantUrl('/admin/dashboard'))
            ->assertRedirect(tenantRoute('tenant.admin.login'));
    });

    test('authenticated admin can view the dashboard', function (): void {
        $this->actingAs(seededTenantCompany(), 'company')
            ->get(tenantUrl('/admin/dashboard'))
            ->assertOk();
    });

    test('the dashboard view receives the user statistics', function (): void {
        makeTenantUser(['email' => 'verified@acme.com', 'email_verified_at' => now()]);
        makeTenantUser(['email' => 'pending@acme.com', 'email_verified_at' => null]);

        $this->actingAs(seededTenantCompany(), 'company')
            ->get(tenantUrl('/admin/dashboard'))
            ->assertOk()
            ->assertViewHas('usersCount', 2)
            ->assertViewHas('unverifiedUsersCount', 1);
    });
});
