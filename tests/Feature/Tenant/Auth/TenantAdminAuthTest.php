<?php

beforeEach(function (): void {
    seedCompany([
        'company_email' => 'admin@acme.com',
        'password'      => 'Admin@123',
    ]);
    setTenantDomain('acme');
});

/**
 * Returns a valid tenant admin login payload with optional field overrides.
 *
 * @param array $overrides
 * @return array
 */
function tenantAdminLoginPayload(array $overrides = []): array
{
    return array_merge([
        'email'    => 'admin@acme.com',
        'password' => 'Admin@123',
    ], $overrides);
}


// ─── Group 1: Login Page ──────────────────────────────────────────────────────

describe('login page', function (): void {
    test('renders successfully for guests', function (): void {
        expect($this->get(tenantUrl('/admin/login'))->status())->toBe(200);
    });

    test('authenticated admin is redirected away from login page', function (): void {
        $this->actingAs(seededTenantCompany(), 'company')
            ->get(tenantUrl('/admin/login'))
            ->assertRedirect(tenantRoute('tenant.admin.dashboard'));
    });
});


// ─── Group 2: Successful Login ────────────────────────────────────────────────

describe('successful login', function (): void {
    test('valid credentials authenticate the admin and redirect to dashboard', function (): void {
        $response = $this->post(tenantUrl('/admin/login'), tenantAdminLoginPayload());

        expect(auth('company')->check())->toBeTrue();
        $response->assertRedirect(tenantRoute('tenant.admin.dashboard'));
    });

    test('success flash is set after login', function (): void {
        $this->post(tenantUrl('/admin/login'), tenantAdminLoginPayload());

        expect(session('success'))->toBe('Login successfully.');
    });
});


// ─── Group 3: Validation ──────────────────────────────────────────────────────

describe('validation', function (): void {
    test('empty form submission returns errors for email and password', function (): void {
        $this->post(tenantUrl('/admin/login'), [])
            ->assertSessionHasErrors(['email', 'password']);
    });

    test('invalid email format is rejected', function (): void {
        $this->post(tenantUrl('/admin/login'), tenantAdminLoginPayload(['email' => 'not-an-email']))
            ->assertSessionHasErrors(['email']);
    });
});


// ─── Group 4: Failed Login ────────────────────────────────────────────────────

describe('failed login', function (): void {
    test('wrong password redirects back with email error', function (): void {
        $this->from(tenantRoute('tenant.admin.login'))
            ->post(tenantUrl('/admin/login'), tenantAdminLoginPayload(['password' => 'Wrong@123']))
            ->assertRedirect(tenantRoute('tenant.admin.login'))
            ->assertSessionHasErrors(['email']);
    });

    test('company from a different subdomain cannot login here', function (): void {
        seedCompany([
            'company_name'   => 'Beta Corp',
            'subdomain'      => 'beta',
            'company_email'  => 'admin@beta.com',
            'license_number' => 'LIC-002',
            'password'       => 'Admin@123',
        ]);

        // Posting from acme subdomain with beta's credentials → subdomain mismatch → no match.
        $this->from(tenantRoute('tenant.admin.login'))
            ->post(tenantUrl('/admin/login'), tenantAdminLoginPayload(['email' => 'admin@beta.com']))
            ->assertRedirect(tenantRoute('tenant.admin.login'))
            ->assertSessionHasErrors(['email']);
    });
});

// ─── Group 5: Logout ──────────────────────────────────────────────────────────

describe('logout', function (): void {
    test('authenticated admin is logged out and redirected to login', function (): void {
        $this->actingAs(seededTenantCompany(), 'company')
            ->post(tenantUrl('/admin/logout'))
            ->assertRedirect(tenantRoute('tenant.admin.login'));

        expect(auth('company')->check())->toBeFalse();
    });

    test('success flash is set after logout', function (): void {
        $this->actingAs(seededTenantCompany(), 'company')
            ->post(tenantUrl('/admin/logout'));

        expect(session('success'))->toBe('Logout successfully.');
    });
});
