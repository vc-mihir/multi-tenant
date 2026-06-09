<?php

use App\Models\Central\Company;
use Database\Seeders\AdminUserSeeder;

beforeEach(function (): void {
    setCentralDomain();
    $this->seed(AdminUserSeeder::class);
});

/**
 * Creates and returns a verified, active company with optional field overrides.
 *
 * @param array $overrides
 * @return Company
 */
function activeCompany(array $overrides = []): Company
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

// ─── Group 1: Index Page ──────────────────────────────────────────────────────

describe('index page', function (): void {
    test('renders for authenticated SuperAdmin', function (): void {
        $this->actingAs(seededAdmin(), 'admin')
            ->get(route('admin.companies.index'))
            ->assertStatus(200);
    });

    test('guest is redirected to login', function (): void {
        $this->get(route('admin.companies.index'))
            ->assertRedirect(route('admin.login'));
    });
});

// ─── Group 2: Archived Page ───────────────────────────────────────────────────

describe('archived page', function (): void {
    test('renders for authenticated SuperAdmin', function (): void {
        $this->actingAs(seededAdmin(), 'admin')
            ->get(route('admin.companies.archived'))
            ->assertStatus(200);
    });

    test('guest is redirected to login', function (): void {
        $this->get(route('admin.companies.archived'))
            ->assertRedirect(route('admin.login'));
    });
});

// ─── Group 3: Companies Data Endpoint ────────────────────────────────────────

describe('companies data endpoint', function (): void {
    test('guest is redirected to login', function (): void {
        $this->get(route('admin.companies.data'))
            ->assertRedirect(route('admin.login'));
    });

    test('returns DataTables JSON for authenticated admin', function (): void {
        activeCompany();

        $this->actingAs(seededAdmin(), 'admin')
            ->getJson(route('admin.companies.data'))
            ->assertOk()
            ->assertJsonStructure(['draw', 'recordsTotal', 'recordsFiltered', 'data']);
    });

    test('status filter narrows results to matching companies only', function (): void {
        activeCompany(['status' => 'active']);
        activeCompany([
            'company_name'   => 'Suspended Corp',
            'subdomain'      => 'suspended',
            'company_email'  => 'info@suspended.com',
            'license_number' => 'LIC-002',
            'status'         => 'suspended',
        ]);

        $response = $this->actingAs(seededAdmin(), 'admin')
            ->getJson(route('admin.companies.data', ['status' => 'active']))
            ->assertOk()
            ->json();

        expect($response['recordsTotal'])->toBe(1);
    });
});

// ─── Group 4: Archived Data Endpoint ─────────────────────────────────────────

describe('archived data endpoint', function (): void {
    test('guest is redirected to login', function (): void {
        $this->get(route('admin.companies.archived.data'))
            ->assertRedirect(route('admin.login'));
    });

    test('returns only soft-deleted companies', function (): void {
        activeCompany();

        $archived = activeCompany([
            'company_name'   => 'Archived Corp',
            'subdomain'      => 'archived',
            'company_email'  => 'info@archived.com',
            'license_number' => 'LIC-002',
        ]);
        $archived->delete();

        $response = $this->actingAs(seededAdmin(), 'admin')
            ->getJson(route('admin.companies.archived.data'))
            ->assertOk()
            ->json();

        expect($response['recordsTotal'])->toBe(1);
    });
});

// ─── Group 5: Search Endpoint ─────────────────────────────────────────────────

describe('search endpoint', function (): void {
    test('guest is redirected to login', function (): void {
        $this->get(route('admin.companies.search'))
            ->assertRedirect(route('admin.login'));
    });

    test('matches companies by name substring', function (): void {
        activeCompany(['company_name' => 'Acme Corp']);

        $this->actingAs(seededAdmin(), 'admin')
            ->getJson(route('admin.companies.search', ['q' => 'Acme']))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'Acme Corp']);
    });

    test('matches company by exact email via hash lookup', function (): void {
        activeCompany(['company_email' => 'info@acme.com']);

        $this->actingAs(seededAdmin(), 'admin')
            ->getJson(route('admin.companies.search', ['q' => 'info@acme.com']))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['email' => 'info@acme.com']);
    });
});
