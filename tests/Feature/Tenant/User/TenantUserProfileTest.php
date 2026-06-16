<?php

use App\Models\Tenant\User as TenantUser;
use App\Notifications\VerifyTenantUserEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

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
 * Returns a valid tenant user profile update payload with optional overrides.
 *
 * @param array $overrides
 * @return array
 */
function tenantUserProfilePayload(array $overrides = []): array
{
    return array_merge([
        'name'  => 'John Updated',
        'email' => 'john@acme.com',
    ], $overrides);
}


// ─── Group 1: Profile Page ────────────────────────────────────────────────────

describe('profile page', function (): void {
    test('renders for an authenticated, verified tenant user', function (): void {
        $this->actingAs(makeTenantUser(), 'tenant_user')
            ->get(tenantUrl('/profile'))
            ->assertStatus(200);
    });

    test('guest is redirected to login', function (): void {
        $this->get(tenantUrl('/profile'))
            ->assertRedirect(tenantRoute('tenant.login'));
    });

    test('unverified user is redirected to the verification notice', function (): void {
        $this->actingAs(makeTenantUser(['email_verified_at' => null]), 'tenant_user')
            ->get(tenantUrl('/profile'))
            ->assertRedirect(tenantRoute('verification.notice'));
    });
});


// ─── Group 2: Successful Update ───────────────────────────────────────────────

describe('successful update', function (): void {
    test('name is updated and re-encrypted with hash kept in sync', function (): void {
        $user = makeTenantUser();

        $this->actingAs($user, 'tenant_user')
            ->put(tenantUrl('/profile'), tenantUserProfilePayload());

        $row = DB::table('users')->where('id', $user->id)->first();

        expect(decrypt($row->name))->toBe('John Updated')
            ->and($row->name_hash)->toBe(hash('sha256', 'john updated'));
    });

    test('redirects back to the profile page with a success message', function (): void {
        $this->actingAs(makeTenantUser(), 'tenant_user')
            ->put(tenantUrl('/profile'), tenantUserProfilePayload())
            ->assertRedirect(tenantRoute('tenant.user.profile'));

        expect(session('success'))->toBe('Profile updated successfully.');
    });

    test('password is updated when provided', function (): void {
        $user = makeTenantUser();

        $this->actingAs($user, 'tenant_user')
            ->put(tenantUrl('/profile'), tenantUserProfilePayload([
                'password'              => 'NewPass@1',
                'password_confirmation' => 'NewPass@1',
            ]));

        expect(Hash::check('NewPass@1', $user->fresh()->password))->toBeTrue();
    });

    test('password is unchanged when left empty', function (): void {
        $user = makeTenantUser();

        $this->actingAs($user, 'tenant_user')
            ->put(tenantUrl('/profile'), tenantUserProfilePayload());

        expect(Hash::check('User@1234', $user->fresh()->password))->toBeTrue();
    });

    test('the user remains authenticated after the update', function (): void {
        $this->actingAs(makeTenantUser(), 'tenant_user')
            ->put(tenantUrl('/profile'), tenantUserProfilePayload());

        expect(auth('tenant_user')->check())->toBeTrue();
    });
});


// ─── Group 3: Email Change Flow ───────────────────────────────────────────────

describe('email change', function (): void {
    test('email is persisted with the hash kept in sync', function (): void {
        Notification::fake();
        $user = makeTenantUser();

        $this->actingAs($user, 'tenant_user')
            ->put(tenantUrl('/profile'), tenantUserProfilePayload(['email' => 'new@acme.com']));

        $row = DB::table('users')->where('id', $user->id)->first();

        expect(decrypt($row->email))->toBe('new@acme.com')
            ->and($row->email_hash)->toBe(hash('sha256', 'new@acme.com'));
    });

    test('verification status is reset when the email changes', function (): void {
        Notification::fake();
        $user = makeTenantUser();

        $this->actingAs($user, 'tenant_user')
            ->put(tenantUrl('/profile'), tenantUserProfilePayload(['email' => 'new@acme.com']));

        expect($user->fresh()->email_verified_at)->toBeNull();
    });

    test('a fresh verification notification is sent on email change', function (): void {
        Notification::fake();
        $user = makeTenantUser();

        $this->actingAs($user, 'tenant_user')
            ->put(tenantUrl('/profile'), tenantUserProfilePayload(['email' => 'new@acme.com']));

        Notification::assertSentTo($user->fresh(), VerifyTenantUserEmail::class);
    });

    test('redirects to the verification notice with the email_changed flag', function (): void {
        Notification::fake();

        $this->actingAs(makeTenantUser(), 'tenant_user')
            ->put(tenantUrl('/profile'), tenantUserProfilePayload(['email' => 'new@acme.com']))
            ->assertRedirect(tenantRoute('verification.notice'));

        expect(session('success'))->toBe('Email updated. Please verify your new email address.')
            ->and(session('email_changed'))->toBeTrue();
    });

    test('no notification is sent when the email is unchanged', function (): void {
        Notification::fake();

        $this->actingAs(makeTenantUser(), 'tenant_user')
            ->put(tenantUrl('/profile'), tenantUserProfilePayload());

        Notification::assertNothingSent();
    });
});


// ─── Group 4: Validation ──────────────────────────────────────────────────────

describe('validation', function (): void {
    test('required fields are enforced', function (string $field): void {
        $payload = tenantUserProfilePayload();
        unset($payload[$field]);

        $this->actingAs(makeTenantUser(), 'tenant_user')
            ->put(tenantUrl('/profile'), $payload)
            ->assertSessionHasErrors([$field]);
    })->with(['name', 'email']);

    test('invalid email format is rejected', function (): void {
        $this->actingAs(makeTenantUser(), 'tenant_user')
            ->put(tenantUrl('/profile'), tenantUserProfilePayload(['email' => 'not-an-email']))
            ->assertSessionHasErrors(['email']);
    });

    test('an email already taken by another user is rejected', function (): void {
        makeTenantUser(['email' => 'jane@acme.com']);

        $this->actingAs(makeTenantUser(), 'tenant_user')
            ->put(tenantUrl('/profile'), tenantUserProfilePayload(['email' => 'jane@acme.com']))
            ->assertSessionHasErrors(['email']);
    });

    test('keeping the same email passes the uniqueness check', function (): void {
        $this->actingAs(makeTenantUser(), 'tenant_user')
            ->put(tenantUrl('/profile'), tenantUserProfilePayload())
            ->assertSessionHasNoErrors();
    });

    test('password confirmation mismatch is rejected', function (): void {
        $this->actingAs(makeTenantUser(), 'tenant_user')
            ->put(tenantUrl('/profile'), tenantUserProfilePayload([
                'password'              => 'NewPass@1',
                'password_confirmation' => 'Different@1',
            ]))
            ->assertSessionHasErrors(['password']);
    });

    test('password shorter than 8 characters is rejected', function (): void {
        $this->actingAs(makeTenantUser(), 'tenant_user')
            ->put(tenantUrl('/profile'), tenantUserProfilePayload([
                'password'              => 'Ab@1',
                'password_confirmation' => 'Ab@1',
            ]))
            ->assertSessionHasErrors(['password']);
    });
});


// ─── Group 5: Delete Account ──────────────────────────────────────────────────

describe('delete account', function (): void {
    test('soft-deletes the tenant user record', function (): void {
        $user = makeTenantUser();

        $this->actingAs($user, 'tenant_user')
            ->delete(tenantUrl('/profile'));

        expect(TenantUser::onlyTrashed()->where('id', $user->id)->exists())->toBeTrue();
    });

    test('logs out the tenant user after deletion', function (): void {
        $this->actingAs(makeTenantUser(), 'tenant_user')
            ->delete(tenantUrl('/profile'));

        expect(auth('tenant_user')->check())->toBeFalse();
    });

    test('redirects to login with a success message', function (): void {
        $this->actingAs(makeTenantUser(), 'tenant_user')
            ->delete(tenantUrl('/profile'))
            ->assertRedirect(tenantRoute('tenant.login'));

        expect(session('success'))->toBe('Your account has been deleted.');
    });

    test('guest cannot delete an account and is redirected to login', function (): void {
        $this->delete(tenantUrl('/profile'))
            ->assertRedirect(tenantRoute('tenant.login'));
    });
});
