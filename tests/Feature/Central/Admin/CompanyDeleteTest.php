<?php

use App\Models\Central\Company;
use Database\Seeders\AdminUserSeeder;

beforeEach(function (): void {
    setCentralDomain();
    $this->seed(AdminUserSeeder::class);
});

/**
 * Creates and returns a verified, active company for delete/restore tests.
 *
 * @param array $overrides
 * @return Company
 */
function deletableCompany(array $overrides = []): Company
{
    return Company::create(array_merge([
        'company_name'      => 'Acme Corp',
        'subdomain'         => 'acme',
        'company_email'     => 'info@acme.com',
        'password'          => 'Hello@123',
        'website'           => 'https://acme.com',
        'license_number'    => 'LIC-001',
        'address'           => '123 Main St',
        'country'           => 'India',
        'state'             => 'Gujarat',
        'city'              => 'Ahmedabad',
        'status'            => 'active',
        'email_verified_at' => now(),
    ], $overrides));
}

// ─── Group 1: Destroy (soft delete / archive) ─────────────────────────────────

describe('destroy', function (): void {
    test('guest is redirected to login', function (): void {
        $company = deletableCompany();

        $this->delete(route('admin.companies.destroy', $company))
            ->assertRedirect(route('admin.login'));
    });

    test('company is soft-deleted and returns JSON success', function (): void {
        $company = deletableCompany();

        $this->actingAs(seededAdmin(), 'admin')
            ->deleteJson(route('admin.companies.destroy', $company))
            ->assertOk()
            ->assertJson(['success' => true, 'message' => 'Company archived successfully.']);

        expect(Company::withTrashed()->find($company->id)->deleted_at)->not->toBeNull();
    });
});

// ─── Group 2: Bulk Delete ─────────────────────────────────────────────────────

describe('bulk delete', function (): void {
    test('guest is redirected to login', function (): void {
        $company = deletableCompany();

        $this->delete(route('admin.companies.bulk-delete'), ['ids' => [$company->id]])
            ->assertRedirect(route('admin.login'));
    });

    test('all selected companies are soft-deleted and count is returned', function (): void {
        $one = deletableCompany([
            'company_name'   => 'Corp One',
            'subdomain'      => 'corpone',
            'company_email'  => 'one@test.com',
            'license_number' => 'LIC-001',
        ]);
        $two = deletableCompany([
            'company_name'   => 'Corp Two',
            'subdomain'      => 'corptwo',
            'company_email'  => 'two@test.com',
            'license_number' => 'LIC-002',
        ]);

        $this->actingAs(seededAdmin(), 'admin')
            ->deleteJson(route('admin.companies.bulk-delete'), ['ids' => [$one->id, $two->id]])
            ->assertOk()
            ->assertJson(['success' => true, 'message' => 'Successfully archived 2 companies.']);

        expect(
            Company::withTrashed()->whereIn('id', [$one->id, $two->id])->whereNotNull('deleted_at')->count()
        )->toBe(2);
    });

    test('empty ids array is rejected', function (): void {
        $this->actingAs(seededAdmin(), 'admin')
            ->deleteJson(route('admin.companies.bulk-delete'), ['ids' => []])
            ->assertStatus(422);
    });
});

// ─── Group 3: Restore ─────────────────────────────────────────────────────────

describe('restore', function (): void {
    test('guest is redirected to login', function (): void {
        $company = deletableCompany();
        $company->delete();

        $this->patch(route('admin.companies.restore', $company))
            ->assertRedirect(route('admin.login'));
    });

    test('soft-deleted company is restored and returns JSON success', function (): void {
        $company = deletableCompany();
        $company->delete();

        $this->actingAs(seededAdmin(), 'admin')
            ->patchJson(route('admin.companies.restore', $company))
            ->assertOk()
            ->assertJson(['success' => true, 'message' => 'Company has been restored successfully.']);

        expect(Company::find($company->id)->deleted_at)->toBeNull();
    });
});

// ─── Group 4: Force Delete ────────────────────────────────────────────────────

describe('force delete', function (): void {
    test('guest is redirected to login', function (): void {
        $company = deletableCompany();
        $company->delete();

        $this->delete(route('admin.companies.force-delete', $company))
            ->assertRedirect(route('admin.login'));
    });

    test('soft-deleted company is permanently removed and returns JSON success', function (): void {
        $company = deletableCompany();
        $company->delete();

        $this->actingAs(seededAdmin(), 'admin')
            ->deleteJson(route('admin.companies.force-delete', $company))
            ->assertOk()
            ->assertJson(['success' => true, 'message' => 'Company permanently deleted and database dropped.']);

        expect(Company::withTrashed()->find($company->id))->toBeNull();
    });
});
