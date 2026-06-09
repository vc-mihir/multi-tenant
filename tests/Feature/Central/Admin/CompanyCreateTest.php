<?php

use App\Jobs\CreateCompanyDatabase;
use App\Models\Central\Company;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

beforeEach(function (): void {
    setCentralDomain();
    $this->seed(AdminUserSeeder::class);
    Queue::fake();
});

/**
 * Returns a valid admin company creation payload with optional field overrides.
 *
 * @param array $overrides
 * @return array
 */
function adminCompanyPayload(array $overrides = []): array
{
    return array_merge([
        'company_name'          => 'Acme Corp',
        'subdomain'             => 'acme',
        'company_email'         => 'info@acme.com',
        'password'              => 'Hello@123',
        'password_confirmation' => 'Hello@123',
        'website'               => 'https://acme.com',
        'license_number'        => 'LIC-001',
        'address'               => '123 Main Street',
        'country'               => 'India',
        'state'                 => 'Gujarat',
        'city'                  => 'Ahmedabad',
    ], $overrides);
}

// ─── Group 1: Create Page ─────────────────────────────────────────────────────

describe('create page', function (): void {
    test('renders for authenticated SuperAdmin', function (): void {
        $this->actingAs(seededAdmin(), 'admin')
            ->get(route('admin.companies.create'))
            ->assertStatus(200);
    });

    test('guest is redirected to login', function (): void {
        $this->get(route('admin.companies.create'))
            ->assertRedirect(route('admin.login'));
    });
});

// ─── Group 2: Successful Store ────────────────────────────────────────────────

describe('successful store', function (): void {
    test('redirects to companies index with success flash', function (): void {
        $this->actingAs(seededAdmin(), 'admin')
            ->post(route('admin.companies.store'), adminCompanyPayload())
            ->assertRedirect(route('admin.companies.index'));

        expect(session('success'))->toBe('Company created successfully. Database provisioning has been queued.');
    });

    test('company is created as active with email pre-verified', function (): void {
        $this->actingAs(seededAdmin(), 'admin')
            ->post(route('admin.companies.store'), adminCompanyPayload());

        $company = Company::first();

        expect($company->status)->toBe('active')
            ->and($company->email_verified_at)->not->toBeNull();
    });

    test('company email is encrypted and its hash is stored correctly', function (): void {
        $this->actingAs(seededAdmin(), 'admin')
            ->post(route('admin.companies.store'), adminCompanyPayload());

        $row = DB::table('companies')->first();

        expect(decrypt($row->company_email))->toBe('info@acme.com')
            ->and($row->company_email_hash)->toBe(hash('sha256', 'info@acme.com'));
    });

    test('license number is encrypted and its hash is stored correctly', function (): void {
        $this->actingAs(seededAdmin(), 'admin')
            ->post(route('admin.companies.store'), adminCompanyPayload());

        $row = DB::table('companies')->first();

        expect(decrypt($row->license_number))->toBe('LIC-001')
            ->and($row->license_number_hash)->toBe(hash('sha256', strtolower('LIC-001')));
    });

    test('database provisioning job is queued for the created company', function (): void {
        $this->actingAs(seededAdmin(), 'admin')
            ->post(route('admin.companies.store'), adminCompanyPayload());

        $company = Company::first();
        $pushed  = Queue::pushed(CreateCompanyDatabase::class);

        expect($pushed)->not->toBeEmpty()
            ->and($pushed->first()->company->id)->toBe($company->id);
    });
});

// ─── Group 3: Validation ─────────────────────────────────────────────────────

describe('validation', function (): void {
    test('empty form returns errors for all required fields', function (): void {
        $this->actingAs(seededAdmin(), 'admin')
            ->post(route('admin.companies.store'), [])
            ->assertSessionHasErrors([
                'company_name',
                'subdomain',
                'company_email',
                'password',
                'website',
                'license_number',
                'address',
                'country',
                'state',
                'city',
            ]);
    });

    test('duplicate company name is rejected', function (): void {
        Company::create(adminCompanyPayload(['status' => 'active', 'email_verified_at' => now()]));

        $this->actingAs(seededAdmin(), 'admin')
            ->post(route('admin.companies.store'), adminCompanyPayload([
                'subdomain'      => 'other',
                'company_email'  => 'other@test.com',
                'license_number' => 'LIC-999',
            ]))
            ->assertSessionHasErrors(['company_name']);
    });

    test('duplicate subdomain is rejected', function (): void {
        Company::create(adminCompanyPayload(['status' => 'active', 'email_verified_at' => now()]));

        $this->actingAs(seededAdmin(), 'admin')
            ->post(route('admin.companies.store'), adminCompanyPayload([
                'company_name'   => 'Other Corp',
                'company_email'  => 'other@test.com',
                'license_number' => 'LIC-999',
            ]))
            ->assertSessionHasErrors(['subdomain']);
    });

    test('duplicate company email is rejected via hash-based lookup', function (): void {
        Company::create(adminCompanyPayload(['status' => 'active', 'email_verified_at' => now()]));

        $this->actingAs(seededAdmin(), 'admin')
            ->post(route('admin.companies.store'), adminCompanyPayload([
                'company_name'   => 'Other Corp',
                'subdomain'      => 'other',
                'license_number' => 'LIC-999',
            ]))
            ->assertSessionHasErrors(['company_email']);
    });

    test('duplicate license number is rejected via hash-based lookup', function (): void {
        Company::create(adminCompanyPayload(['status' => 'active', 'email_verified_at' => now()]));

        $this->actingAs(seededAdmin(), 'admin')
            ->post(route('admin.companies.store'), adminCompanyPayload([
                'company_name'  => 'Other Corp',
                'subdomain'     => 'other',
                'company_email' => 'other@test.com',
            ]))
            ->assertSessionHasErrors(['license_number']);
    });

    test('invalid subdomain formats are rejected', function (string $subdomain): void {
        $this->actingAs(seededAdmin(), 'admin')
            ->post(route('admin.companies.store'), adminCompanyPayload([
                'subdomain' => $subdomain,
            ]))
            ->assertSessionHasErrors(['subdomain']);
    })->with([
        'uppercase letters' => 'Acme',
        'underscore'        => 'acme_corp',
        'leading hyphen'    => '-acme',
        'trailing hyphen'   => 'acme-',
    ]);

    test('invalid website url is rejected', function (): void {
        $this->actingAs(seededAdmin(), 'admin')
            ->post(route('admin.companies.store'), adminCompanyPayload([
                'website' => 'not-a-url',
            ]))
            ->assertSessionHasErrors(['website']);
    });

    test('password confirmation mismatch is rejected', function (): void {
        $this->actingAs(seededAdmin(), 'admin')
            ->post(route('admin.companies.store'), adminCompanyPayload([
                'password_confirmation' => 'Different@123',
            ]))
            ->assertSessionHasErrors(['password']);
    });

    test('weak password is rejected', function (string $password): void {
        $this->actingAs(seededAdmin(), 'admin')
            ->post(route('admin.companies.store'), adminCompanyPayload([
                'password'              => $password,
                'password_confirmation' => $password,
            ]))
            ->assertSessionHasErrors(['password']);
    })->with([
        'too short'    => 'Ab@1',
        'no uppercase' => 'hello@123',
        'no symbol'    => 'Hello1234',
        'too long'     => 'Hello@12345678901',
    ]);
});
