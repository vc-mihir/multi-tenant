<?php

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

/**
 * Returns a valid tenant user login payload with optional field overrides.
 *
 * @param array $overrides
 * @return array
 */
function tenantUserLoginPayload(array $overrides = []): array
{
    return array_merge([
        'email'    => 'john@acme.com',
        'password' => 'User@1234',
    ], $overrides);
}


// ─── Group 1: Login Page ──────────────────────────────────────────────────────

describe('login page', function (): void {
    test('renders successfully for guests', function (): void {
        $this->get(tenantUrl('/login'))
            ->assertStatus(200);
    });

    test('authenticated user is redirected away from login page', function (): void {
        $this->actingAs(makeTenantUser(), 'tenant_user')
            ->get(tenantUrl('/login'))
            ->assertRedirect(tenantRoute('tenant.dashboard'));
    });
});


// ─── Group 2: Successful Login ────────────────────────────────────────────────

describe('successful login', function (): void {
    test('valid credentials authenticate the user and redirect to dashboard', function (): void {
        makeTenantUser();

        $response = $this->post(tenantUrl('/login'), tenantUserLoginPayload());

        expect(auth('tenant_user')->check())->toBeTrue();
        $response->assertRedirect(tenantRoute('tenant.dashboard'));
    });

    test('success flash is set after login', function (): void {
        makeTenantUser();

        $this->post(tenantUrl('/login'), tenantUserLoginPayload());

        expect(session('success'))->toBe('Login successfully.');
    });
});


// ─── Group 3: Validation ──────────────────────────────────────────────────────

describe('validation', function (): void {
    test('empty form submission returns errors for email and password', function (): void {
        $this->post(tenantUrl('/login'), [])
            ->assertSessionHasErrors(['email', 'password']);
    });

    test('invalid email format is rejected', function (): void {
        $this->post(tenantUrl('/login'), tenantUserLoginPayload(['email' => 'not-an-email']))
            ->assertSessionHasErrors(['email']);
    });
});


// ─── Group 4: Failed Login ────────────────────────────────────────────────────

describe('failed login', function (): void {
    test('wrong password redirects back with email error', function (): void {
        makeTenantUser();

        $this->from(tenantRoute('tenant.login'))
            ->post(tenantUrl('/login'), tenantUserLoginPayload(['password' => 'Wrong@123']))
            ->assertRedirect(tenantRoute('tenant.login'))
            ->assertSessionHasErrors(['email']);

        expect(auth('tenant_user')->check())->toBeFalse();
    });

    test('unknown email redirects back with email error', function (): void {
        $this->from(tenantRoute('tenant.login'))
            ->post(tenantUrl('/login'), tenantUserLoginPayload(['email' => 'ghost@acme.com']))
            ->assertRedirect(tenantRoute('tenant.login'))
            ->assertSessionHasErrors(['email']);
    });

    test('inactive account is rejected with a clear message', function (): void {
        makeTenantUser(['is_active' => false]);

        $this->post(tenantUrl('/login'), tenantUserLoginPayload())
            ->assertSessionHasErrors([
                'email' => 'Your account is inactive. Please contact the administrator.',
            ]);

        expect(auth('tenant_user')->check())->toBeFalse();
    });

    test('soft-deleted account is rejected with a deleted-account message', function (): void {
        makeTenantUser()->delete();

        $this->post(tenantUrl('/login'), tenantUserLoginPayload())
            ->assertSessionHasErrors([
                'email' => 'Your account has been deleted. Please contact the administrator.',
            ]);

        expect(auth('tenant_user')->check())->toBeFalse();
    });

    test('login is rate limited after five failed attempts', function (): void {
        makeTenantUser();

        foreach (range(1, 5) as $attempt) {
            $this->post(tenantUrl('/login'), tenantUserLoginPayload(['password' => 'Wrong@123']));
        }

        $this->post(tenantUrl('/login'), tenantUserLoginPayload(['password' => 'Wrong@123']))
            ->assertInvalid(['email' => 'Too many login attempts']);
    });
});


// ─── Group 5: Logout ──────────────────────────────────────────────────────────

describe('logout', function (): void {
    test('authenticated user is logged out and redirected to login', function (): void {
        $this->actingAs(makeTenantUser(), 'tenant_user')
            ->post(tenantUrl('/logout'))
            ->assertRedirect(tenantRoute('tenant.login'));

        expect(auth('tenant_user')->check())->toBeFalse();
    });

    test('success flash is set after logout', function (): void {
        $this->actingAs(makeTenantUser(), 'tenant_user')
            ->post(tenantUrl('/logout'));

        expect(session('success'))->toBe('Logout successfully.');
    });

    test('guest cannot logout and is redirected to login', function (): void {
        $this->post(tenantUrl('/logout'))
            ->assertRedirect(tenantRoute('tenant.login'));
    });
});
