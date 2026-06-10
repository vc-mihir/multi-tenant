<?php

use App\Models\Central\Company as CentralCompany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

beforeEach(function (): void {
    seedCompany([
        'company_email' => 'admin@acme.com',
        'password'      => 'Admin@123',
    ]);
    setTenantDomain('acme');
});

/**
 * Returns a valid profile update payload with optional field overrides.
 *
 * @param array $overrides
 * @return array
 */
function tenantAdminProfilePayload(array $overrides = []): array
{
    return array_merge([
        'company_name'   => 'Updated Corp',
        'company_email'  => 'updated@acme.com',
        'website'        => 'https://updated-acme.com',
        'address'        => '456 New Street',
        'country'        => 'India',
        'state'          => 'Maharashtra',
        'city'           => 'Mumbai',
        'license_number' => 'LIC-999',
    ], $overrides);
}


// ─── Group 1: Profile Page ────────────────────────────────────────────────────

describe('profile page', function (): void {
    test('renders for authenticated tenant admin', function (): void {
        $this->actingAs(seededTenantCompany(), 'company')
            ->get(tenantUrl('/admin/profile'))
            ->assertStatus(200);
    });

    test('guest is redirected to login', function (): void {
        $this->get(tenantUrl('/admin/profile'))
            ->assertRedirect(tenantRoute('tenant.admin.login'));
    });
});


// ─── Group 2: Successful Update ───────────────────────────────────────────────

describe('successful update', function (): void {
    test('tenant company record is updated', function (): void {
        $company = seededTenantCompany();

        $this->actingAs($company, 'company')
            ->put(tenantUrl('/admin/profile'), tenantAdminProfilePayload());

        $row = DB::table('companies')->where('id', $company->id)->first();

        expect($row->company_name)->toBe('Updated Corp')
            ->and(decrypt($row->company_email))->toBe('updated@acme.com')
            ->and($row->company_email_hash)->toBe(hash('sha256', 'updated@acme.com'));
    });

    test('central company record is also kept in sync', function (): void {
        $company = seededTenantCompany();

        $this->actingAs($company, 'company')
            ->put(tenantUrl('/admin/profile'), tenantAdminProfilePayload());

        $centralCompany = CentralCompany::on('mysql')->find($company->id);

        expect($centralCompany->company_name)->toBe('Updated Corp')
            ->and($centralCompany->company_email)->toBe('updated@acme.com')
            ->and(
                DB::table('companies')->where('id', $company->id)->value('company_email_hash')
            )->toBe(hash('sha256', 'updated@acme.com'));
    });

    test('redirects with success message after update', function (): void {
        $this->actingAs(seededTenantCompany(), 'company')
            ->put(tenantUrl('/admin/profile'), tenantAdminProfilePayload())
            ->assertRedirect(tenantRoute('tenant.admin.profile'));

        expect(session('success'))->toBe('Company profile updated successfully.');
    });

    test('password is updated when provided', function (): void {
        $company = seededTenantCompany();

        $this->actingAs($company, 'company')
            ->put(tenantUrl('/admin/profile'), tenantAdminProfilePayload([
                'password'              => 'NewPass@1',
                'password_confirmation' => 'NewPass@1',
            ]));

        expect(Hash::check('NewPass@1', $company->fresh()->password))->toBeTrue();
    });

    test('password is unchanged when left empty', function (): void {
        $company = seededTenantCompany();

        $this->actingAs($company, 'company')
            ->put(tenantUrl('/admin/profile'), tenantAdminProfilePayload());

        expect(Hash::check('Admin@123', $company->fresh()->password))->toBeTrue();
    });
});


// ─── Group 3: Validation ──────────────────────────────────────────────────────

describe('validation', function (): void {
    test('required fields are enforced', function (string $field): void {
        $payload = tenantAdminProfilePayload();
        unset($payload[$field]);

        $this->actingAs(seededTenantCompany(), 'company')
            ->put(tenantUrl('/admin/profile'), $payload)
            ->assertSessionHasErrors([$field]);
    })->with([
        'company_name',
        'company_email',
        'website',
        'address',
        'country',
        'state',
        'city',
        'license_number',
    ]);

    test('invalid email format is rejected', function (): void {
        $this->actingAs(seededTenantCompany(), 'company')
            ->put(tenantUrl('/admin/profile'), tenantAdminProfilePayload(['company_email' => 'not-an-email']))
            ->assertSessionHasErrors(['company_email']);
    });

    test('invalid website url is rejected', function (): void {
        $this->actingAs(seededTenantCompany(), 'company')
            ->put(tenantUrl('/admin/profile'), tenantAdminProfilePayload(['website' => 'not-a-url']))
            ->assertSessionHasErrors(['website']);
    });

    test('password confirmation mismatch is rejected', function (): void {
        $this->actingAs(seededTenantCompany(), 'company')
            ->put(tenantUrl('/admin/profile'), tenantAdminProfilePayload([
                'password'              => 'NewPass@1',
                'password_confirmation' => 'Different@1',
            ]))
            ->assertSessionHasErrors(['password']);
    });

    test('password shorter than 8 characters is rejected', function (): void {
        $this->actingAs(seededTenantCompany(), 'company')
            ->put(tenantUrl('/admin/profile'), tenantAdminProfilePayload([
                'password'              => 'Ab@1',
                'password_confirmation' => 'Ab@1',
            ]))
            ->assertSessionHasErrors(['password']);
    });
});


// ─── Group 4: Delete Account ──────────────────────────────────────────────────

describe('delete account', function (): void {
    test('soft-deletes the central company record', function (): void {
        $company = seededTenantCompany();

        $this->actingAs($company, 'company')
            ->delete(tenantUrl('/admin/profile'));

        expect(
            CentralCompany::onlyTrashed()->where('id', $company->id)->exists()
        )->toBeTrue();
    });

    test('logs out the company guard after deletion', function (): void {
        $this->actingAs(seededTenantCompany(), 'company')
            ->delete(tenantUrl('/admin/profile'));

        expect(auth('company')->check())->toBeFalse();
    });

    test('redirects after account deletion', function (): void {
        $this->actingAs(seededTenantCompany(), 'company')
            ->delete(tenantUrl('/admin/profile'))
            ->assertRedirect();
    });
});
