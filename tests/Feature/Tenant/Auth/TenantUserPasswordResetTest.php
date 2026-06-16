<?php

use App\Models\Tenant\User as TenantUser;
use Illuminate\Auth\Notifications\ResetPassword;
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
 * Returns a valid reset-password payload with optional field overrides.
 *
 * @param array $overrides
 * @return array
 */
function tenantResetPasswordPayload(array $overrides = []): array
{
    return array_merge([
        'token'                 => 'valid-token',
        'email'                 => 'john@acme.com',
        'password'              => 'NewPass@12',
        'password_confirmation' => 'NewPass@12',
    ], $overrides);
}

/**
 * Requests a reset link for the given user and returns the plain-text token
 * captured from the faked ResetPassword notification.
 *
 * @param TenantUser $user
 * @return string
 */
function requestResetToken(TenantUser $user): string
{
    Notification::fake();

    test()->post(tenantUrl('/forgot-password'), ['email' => 'john@acme.com']);

    $token = null;
    Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use (&$token): bool {
        $token = $notification->token;

        return true;
    });

    return $token;
}


// ─── Group 1: Forgot Password Page ────────────────────────────────────────────

describe('forgot password page', function (): void {
    test('renders successfully for guests', function (): void {
        $this->get(tenantUrl('/forgot-password'))
            ->assertStatus(200);
    });
});


// ─── Group 2: Send Reset Link ─────────────────────────────────────────────────

describe('send reset link', function (): void {
    test('sends the reset notification and stores a hashed token', function (): void {
        Notification::fake();
        $user = makeTenantUser();

        $this->post(tenantUrl('/forgot-password'), ['email' => 'john@acme.com']);

        Notification::assertSentTo($user, ResetPassword::class);

        $row = DB::connection('tenant')->table('password_reset_tokens')
            ->where('email_hash', hash('sha256', 'john@acme.com'))
            ->first();

        expect($row)->not->toBeNull();
    });

    test('unknown email returns an email error', function (): void {
        $this->post(tenantUrl('/forgot-password'), ['email' => 'ghost@acme.com'])
            ->assertSessionHasErrors(['email']);
    });

    test('email is required', function (): void {
        $this->post(tenantUrl('/forgot-password'), [])
            ->assertSessionHasErrors(['email']);
    });

    test('invalid email format is rejected', function (): void {
        $this->post(tenantUrl('/forgot-password'), ['email' => 'not-an-email'])
            ->assertSessionHasErrors(['email']);
    });
});

// ─── Group 3: Reset Password ──────────────────────────────────────────────────

describe('reset password', function (): void {
    test('valid token resets the password, deletes the token and redirects to login', function (): void {
        $user  = makeTenantUser();
        $token = requestResetToken($user);

        $this->post(tenantUrl('/reset-password'), tenantResetPasswordPayload(['token' => $token]))
            ->assertRedirect(tenantRoute('tenant.login'))
            ->assertSessionHas('success');

        expect(Hash::check('NewPass@12', $user->fresh()->password))->toBeTrue()
            ->and(
                DB::connection('tenant')->table('password_reset_tokens')
                    ->where('email_hash', hash('sha256', 'john@acme.com'))
                    ->exists()
            )->toBeFalse();
    });

    test('user can login with the new password after reset', function (): void {
        $user  = makeTenantUser();
        $token = requestResetToken($user);

        $this->post(tenantUrl('/reset-password'), tenantResetPasswordPayload(['token' => $token]));

        $this->post(tenantUrl('/login'), [
            'email'    => 'john@acme.com',
            'password' => 'NewPass@12',
        ]);

        expect(auth('tenant_user')->check())->toBeTrue();
    });

    test('invalid token is rejected', function (): void {
        makeTenantUser();

        $this->post(tenantUrl('/reset-password'), tenantResetPasswordPayload(['token' => 'wrong-token']))
            ->assertSessionHasErrors(['email']);
    });

    test('expired token is rejected', function (): void {
        makeTenantUser();

        DB::connection('tenant')->table('password_reset_tokens')->insert([
            'email_hash' => hash('sha256', 'john@acme.com'),
            'token'      => Hash::make('expired-token'),
            'created_at' => now()->subMinutes(61),
        ]);

        $this->post(tenantUrl('/reset-password'), tenantResetPasswordPayload(['token' => 'expired-token']))
            ->assertSessionHasErrors(['email']);
    });

    test('unknown email is rejected', function (): void {
        $this->post(tenantUrl('/reset-password'), tenantResetPasswordPayload(['email' => 'ghost@acme.com']))
            ->assertSessionHasErrors(['email']);
    });

    test('token is required', function (): void {
        $payload = tenantResetPasswordPayload();
        unset($payload['token']);

        $this->post(tenantUrl('/reset-password'), $payload)
            ->assertSessionHasErrors(['token']);
    });

    test('weak password is rejected', function (string $password): void {
        makeTenantUser();

        $this->post(tenantUrl('/reset-password'), tenantResetPasswordPayload([
            'password' => $password,
            'password_confirmation' => $password,
        ]))
            ->assertSessionHasErrors(['password']);
    })->with([
        'too short'    => 'Ab@1',
        'no uppercase' => 'abcdefg@1',
        'no number'    => 'Abcdefg@',
        'no symbol'    => 'Abcdefg1',
    ]);

    test('password confirmation mismatch is rejected', function (): void {
        makeTenantUser();

        $this->post(tenantUrl('/reset-password'), tenantResetPasswordPayload([
            'password'              => 'NewPass@12',
            'password_confirmation' => 'Different@1',
        ]))
            ->assertSessionHasErrors(['password']);
    });
});
