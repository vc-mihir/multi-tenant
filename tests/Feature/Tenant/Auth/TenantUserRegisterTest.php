<?php

use App\Models\Tenant\User as TenantUser;
use App\Notifications\VerifyTenantUserEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

beforeEach(function (): void {
    seedCompany([
        'company_email' => 'admin@acme.com',
        'password'      => 'Admin@123',
    ]);
    setTenantDomain('acme');
    setUpTenantDb();
});

afterEach(function (): void {
    DB::setDefaultConnection('mysql');
    DB::purge('tenant');
});

/**
 * Returns a valid tenant user registration payload with optional overrides.
 *
 * @param array $overrides
 * @return array
 */
function tenantRegisterPayload(array $overrides = []): array
{
    return array_merge([
        'name'                  => 'Jane Doe',
        'email'                 => 'jane@acme.com',
        'password'              => 'User@1234',
        'password_confirmation' => 'User@1234',
    ], $overrides);
}


// ─── Group 1: Registration Page ───────────────────────────────────────────────

describe('registration page', function (): void {
    test('renders successfully for guests', function (): void {
        $this->get(tenantUrl('/register'))
            ->assertStatus(200);
    });

    test('authenticated tenant user is redirected away from register page', function (): void {
        $this->actingAs(makeTenantUser(), 'tenant_user')
            ->get(tenantUrl('/register'))
            ->assertRedirect(tenantRoute('tenant.dashboard'));
    });
});


// ─── Group 2: Successful Registration ─────────────────────────────────────────

describe('successful registration', function (): void {
    test('creates the user and redirects to the verification notice', function (): void {
        $this->post(tenantUrl('/register'), tenantRegisterPayload())
            ->assertRedirect(tenantRoute('verification.notice'));

        expect(TenantUser::where('email_hash', hash('sha256', 'jane@acme.com'))->exists())->toBeTrue();
    });

    test('persists the user with encrypted fields and sensible defaults', function (): void {
        $this->post(tenantUrl('/register'), tenantRegisterPayload());

        $row = DB::connection('tenant')->table('users')->where('email_hash', hash('sha256', 'jane@acme.com'))->first();

        expect($row)->not->toBeNull()
            ->and(decrypt($row->name))->toBe('Jane Doe')
            ->and($row->name_hash)->toBe(hash('sha256', 'jane doe'))
            ->and(decrypt($row->email))->toBe('jane@acme.com')
            ->and(Hash::check('User@1234', $row->password))->toBeTrue()
            ->and((bool) $row->is_active)->toBeTrue()
            ->and($row->email_verified_at)->toBeNull();
    });

    test('logs the new user in on the tenant_user guard', function (): void {
        $this->post(tenantUrl('/register'), tenantRegisterPayload());

        expect(auth('tenant_user')->check())->toBeTrue();
    });

    test('sends the email verification notification', function (): void {
        Notification::fake();

        $this->post(tenantUrl('/register'), tenantRegisterPayload());

        $user = TenantUser::where('email_hash', hash('sha256', 'jane@acme.com'))->firstOrFail();

        Notification::assertSentTo($user, VerifyTenantUserEmail::class);
    });
});


// ─── Group 3: Validation ──────────────────────────────────────────────────────

describe('validation', function (): void {
    test('name is required', function (): void {
        $payload = tenantRegisterPayload();
        unset($payload['name']);

        $this->post(tenantUrl('/register'), $payload)
            ->assertSessionHasErrors(['name']);
    });

    test('email is required', function (): void {
        $payload = tenantRegisterPayload();
        unset($payload['email']);

        $this->post(tenantUrl('/register'), $payload)
            ->assertSessionHasErrors(['email']);
    });

    test('password is required', function (): void {
        $payload = tenantRegisterPayload();
        unset($payload['password'], $payload['password_confirmation']);

        $this->post(tenantUrl('/register'), $payload)
            ->assertSessionHasErrors(['password']);
    });

    test('invalid email format is rejected', function (): void {
        $this->post(tenantUrl('/register'), tenantRegisterPayload(['email' => 'not-an-email']))
            ->assertSessionHasErrors(['email']);
    });

    test('duplicate email is rejected', function (): void {
        makeTenantUser(['email' => 'jane@acme.com']);

        $this->post(tenantUrl('/register'), tenantRegisterPayload(['email' => 'jane@acme.com']))
            ->assertSessionHasErrors(['email']);
    });

    test('password confirmation mismatch is rejected', function (): void {
        $this->post(tenantUrl('/register'), tenantRegisterPayload([
            'password'              => 'User@1234',
            'password_confirmation' => 'Different@1',
        ]))
            ->assertSessionHasErrors(['password']);
    });

    test('weak password is rejected', function (string $password): void {
        $this->post(tenantUrl('/register'), tenantRegisterPayload([
            'password'              => $password,
            'password_confirmation' => $password,
        ]))
            ->assertSessionHasErrors(['password']);
    })->with([
        'too short'    => 'Ab@1',
        'too long'     => 'Abcdefg@123456789',
        'no uppercase' => 'abcdefg@1',
        'no number'    => 'Abcdefg@',
        'no symbol'    => 'Abcdefg1',
    ]);
});
