<?php

use App\Models\Central\Company;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Support\Facades\DB;

beforeEach(function (): void {
    setCentralDomain();
    $this->seed(AdminUserSeeder::class);
});

/**
 * Creates and returns a verified, active company for edit/update tests.
 *
 * @param array $overrides
 * @return Company
 */
function editableCompany(array $overrides = []): Company
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

/**
 * Returns a valid company update payload with optional field overrides.
 *
 * @param array $overrides
 * @return array
 */
function updateCompanyPayload(array $overrides = []): array
{
    return array_merge([
        'company_name'   => 'Acme Corp Updated',
        'subdomain'      => 'acme-updated',
        'company_email'  => 'updated@acme.com',
        'website'        => 'https://acme-updated.com',
        'license_number' => 'LIC-002',
        'address'        => '456 New Street',
        'country'        => 'India',
        'state'          => 'Maharashtra',
        'city'           => 'Mumbai',
        'status'         => 'active',
    ], $overrides);
}

// ─── Group 1: Edit Page ───────────────────────────────────────────────────────

describe('edit page', function (): void {
    test('renders for authenticated SuperAdmin', function (): void {
        $company = editableCompany();

        $this->actingAs(seededAdmin(), 'admin')
            ->get(route('admin.companies.edit', $company))
            ->assertStatus(200);
    });

    test('guest is redirected to login', function (): void {
        $company = editableCompany();

        $this->get(route('admin.companies.edit', $company))
            ->assertRedirect(route('admin.login'));
    });
});

// ─── Group 2: Successful Update ──────────────────────────────────────────────

describe('successful update', function (): void {
    test('redirects to companies index with success flash', function (): void {
        $company = editableCompany();

        $this->actingAs(seededAdmin(), 'admin')
            ->put(route('admin.companies.update', $company), updateCompanyPayload())
            ->assertRedirect(route('admin.companies.index'));

        expect(session('success'))->toBe('Company details updated and synced across databases.');
    });

    test('updated email is re-encrypted and its hash is stored correctly', function (): void {
        $company = editableCompany();

        $this->actingAs(seededAdmin(), 'admin')
            ->put(route('admin.companies.update', $company), updateCompanyPayload([
                'company_email' => 'new@acme.com',
            ]));

        $row = DB::table('companies')->where('id', $company->id)->first();

        expect(decrypt($row->company_email))->toBe('new@acme.com')
            ->and($row->company_email_hash)->toBe(hash('sha256', 'new@acme.com'));
    });

    test('updated license number is re-encrypted and its hash is stored correctly', function (): void {
        $company = editableCompany();

        $this->actingAs(seededAdmin(), 'admin')
            ->put(route('admin.companies.update', $company), updateCompanyPayload([
                'license_number' => 'LIC-NEW',
            ]));

        $row = DB::table('companies')->where('id', $company->id)->first();

        expect(decrypt($row->license_number))->toBe('LIC-NEW')
            ->and($row->license_number_hash)->toBe(hash('sha256', strtolower('LIC-NEW')));
    });
});

// ─── Group 3: Self-Exclusion on Unique Fields ─────────────────────────────────

describe('self-exclusion on unique fields', function (): void {
    test('resubmitting own name and subdomain does not trigger a uniqueness error', function (): void {
        $company = editableCompany();

        $this->actingAs(seededAdmin(), 'admin')
            ->put(route('admin.companies.update', $company), updateCompanyPayload([
                'company_name' => $company->company_name,
                'subdomain'    => $company->subdomain,
            ]))
            ->assertSessionDoesntHaveErrors(['company_name', 'subdomain']);
    });

    test('resubmitting own email and license does not trigger a uniqueness error', function (): void {
        $company = editableCompany();

        $this->actingAs(seededAdmin(), 'admin')
            ->put(route('admin.companies.update', $company), updateCompanyPayload([
                'company_email'  => 'info@acme.com',
                'license_number' => 'LIC-001',
            ]))
            ->assertSessionDoesntHaveErrors(['company_email', 'license_number']);
    });
});

// ─── Group 4: Uniqueness Against Other Companies ──────────────────────────────

describe('uniqueness validation against other companies', function (): void {
    test('duplicate company name from another company is rejected', function (): void {
        editableCompany([
            'company_name'   => 'Taken Corp',
            'subdomain'      => 'taken',
            'company_email'  => 'taken@test.com',
            'license_number' => 'LIC-T01',
        ]);

        $company = editableCompany([
            'company_name'   => 'My Corp',
            'subdomain'      => 'my',
            'company_email'  => 'my@test.com',
            'license_number' => 'LIC-M01',
        ]);

        $this->actingAs(seededAdmin(), 'admin')
            ->from(route('admin.companies.edit', $company))
            ->put(route('admin.companies.update', $company), updateCompanyPayload([
                'company_name' => 'Taken Corp',
            ]))
            ->assertSessionHasErrors(['company_name']);
    });

    test('duplicate email from another company is rejected via hash lookup', function (): void {
        editableCompany([
            'company_name'   => 'Other Corp',
            'subdomain'      => 'other',
            'company_email'  => 'taken@test.com',
            'license_number' => 'LIC-T01',
        ]);

        $company = editableCompany([
            'company_name'   => 'My Corp',
            'subdomain'      => 'my',
            'company_email'  => 'my@test.com',
            'license_number' => 'LIC-M01',
        ]);

        $this->actingAs(seededAdmin(), 'admin')
            ->from(route('admin.companies.edit', $company))
            ->put(route('admin.companies.update', $company), updateCompanyPayload([
                'company_email' => 'taken@test.com',
            ]))
            ->assertSessionHasErrors(['company_email']);
    });

    test('duplicate license number from another company is rejected via hash lookup', function (): void {
        editableCompany([
            'company_name'   => 'Other Corp',
            'subdomain'      => 'other',
            'company_email'  => 'other@test.com',
            'license_number' => 'LIC-TAKEN',
        ]);

        $company = editableCompany([
            'company_name'   => 'My Corp',
            'subdomain'      => 'my',
            'company_email'  => 'my@test.com',
            'license_number' => 'LIC-M01',
        ]);

        $this->actingAs(seededAdmin(), 'admin')
            ->from(route('admin.companies.edit', $company))
            ->put(route('admin.companies.update', $company), updateCompanyPayload([
                'license_number' => 'LIC-TAKEN',
            ]))
            ->assertSessionHasErrors(['license_number']);
    });
});
