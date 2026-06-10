<?php

use Database\Seeders\AdminUserSeeder;

beforeEach(function (): void {
    setCentralDomain();
    $this->seed(AdminUserSeeder::class);
});

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
        seedCompany();

        $this->actingAs(seededAdmin(), 'admin')
            ->getJson(route('admin.companies.data'))
            ->assertOk()
            ->assertJsonStructure(['draw', 'recordsTotal', 'recordsFiltered', 'data']);
    });

    test('status filter narrows results to matching companies only', function (): void {
        seedCompany(['status' => 'active']);
        seedCompany([
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
        seedCompany();

        $archived = seedCompany([
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
        seedCompany(['company_name' => 'Acme Corp']);

        $this->actingAs(seededAdmin(), 'admin')
            ->getJson(route('admin.companies.search', ['q' => 'Acme']))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'Acme Corp']);
    });

    test('matches company by exact email via hash lookup', function (): void {
        seedCompany(['company_email' => 'info@acme.com']);

        $this->actingAs(seededAdmin(), 'admin')
            ->getJson(route('admin.companies.search', ['q' => 'info@acme.com']))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['email' => 'info@acme.com']);
    });
});
