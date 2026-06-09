<?php

use App\Jobs\CreateCompanyDatabase;
use App\Models\Central\Company;
use App\Models\Central\CompanyDatabase;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Support\Facades\Queue;

beforeEach(function (): void {
    setCentralDomain();
    $this->seed(AdminUserSeeder::class);
    Queue::fake();
});

/**
 * Creates a verified company with no tenant database — eligible for provisioning.
 *
 * @return Company
 */
function eligibleCompany(): Company
{
    return Company::create([
        'company_name'      => 'Test Corp',
        'subdomain'         => 'testcorp',
        'company_email'     => 'test@corp.com',
        'website'           => 'https://testcorp.com',
        'license_number'    => 'LIC-001',
        'address'           => '123 Main St',
        'country'           => 'India',
        'state'             => 'Gujarat',
        'city'              => 'Ahmedabad',
        'password'          => 'Hello@123',
        'status'            => 'active',
        'email_verified_at' => now(),
    ]);
}

// ─── Group 1: Access Control ──────────────────────────────────────────────────

describe('access control', function (): void {
    test('guest is redirected to login', function (): void {
        $company = eligibleCompany();

        $this->post(route('admin.recovery.provision', $company))
            ->assertRedirect(route('admin.login'));
    });
});

// ─── Group 2: Provisioning ────────────────────────────────────────────────────

describe('provisioning', function (): void {
    test('dispatches job for eligible company and redirects with success', function (): void {
        $company = eligibleCompany();

        $this->actingAs(seededAdmin(), 'admin')
            ->post(route('admin.recovery.provision', $company))
            ->assertRedirect();

        Queue::assertPushed(
            CreateCompanyDatabase::class,
            fn ($job) => $job->company->id === $company->id
        );

        expect(session('success'))->toBe('Tenant database creation job has been queued.');
    });

    test('rejects company with unverified email', function (): void {
        $company = eligibleCompany();
        $company->update(['email_verified_at' => null]);

        $this->actingAs(seededAdmin(), 'admin')
            ->from(route('admin.dashboard'))
            ->post(route('admin.recovery.provision', $company))
            ->assertRedirect(route('admin.dashboard'));

        Queue::assertNothingPushed();
        expect(session('error'))->toBe('This company is not eligible for database provisioning.');
    });

    test('rejects company that already has a database', function (): void {
        $company = eligibleCompany();

        CompanyDatabase::create([
            'company_id'  => $company->id,
            'db_name'     => 'tenant_testcorp',
            'db_host'     => '127.0.0.1',
            'db_port'     => '3306',
            'db_username' => 'root',
            'db_password' => encrypt('secret'),
        ]);

        $this->actingAs(seededAdmin(), 'admin')
            ->from(route('admin.dashboard'))
            ->post(route('admin.recovery.provision', $company))
            ->assertRedirect(route('admin.dashboard'));

        Queue::assertNothingPushed();
        expect(session('error'))->toBe('This company is not eligible for database provisioning.');
    });
});
