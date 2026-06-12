<?php

use App\Models\Tenant\User as TenantUser;
use App\Notifications\VerifyTenantUserEmail;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

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
 * Builds a valid temporary signed verification URL for the given user.
 *
 * @param TenantUser $user
 * @return string
 */
function tenantVerificationUrl(TenantUser $user): string
{
    return URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
        'tenant' => 'acme',
        'id'     => $user->id,
        'hash'   => sha1($user->getEmailForVerification()),
    ]);
}


// ─── Group 1: Verification Notice Page ────────────────────────────────────────

describe('verification notice page', function (): void {
    test('renders for an authenticated unverified user', function (): void {
        $this->actingAs(makeTenantUser(['email_verified_at' => null]), 'tenant_user')
            ->get(tenantUrl('/verify-email'))
            ->assertStatus(200);
    });

    test('verified user is redirected to the dashboard', function (): void {
        $this->actingAs(makeTenantUser(), 'tenant_user')
            ->get(tenantUrl('/verify-email'))
            ->assertRedirect(tenantRoute('tenant.dashboard'));
    });

    test('guest is redirected to login', function (): void {
        $this->get(tenantUrl('/verify-email'))
            ->assertRedirect(tenantRoute('tenant.login'));
    });
});


// ─── Group 2: Verify Email Link ───────────────────────────────────────────────

describe('verify email link', function (): void {
    test('valid signed link marks the email as verified and redirects to dashboard', function (): void {
        Event::fake([Verified::class]);
        $user = makeTenantUser(['email_verified_at' => null]);

        $this->actingAs($user, 'tenant_user')
            ->get(tenantVerificationUrl($user))
            ->assertRedirect(tenantRoute('tenant.dashboard'));

        expect($user->fresh()->email_verified_at)->not->toBeNull();
        Event::assertDispatched(Verified::class);
    });

    test('success flash is set after verification', function (): void {
        $user = makeTenantUser(['email_verified_at' => null]);

        $this->actingAs($user, 'tenant_user')
            ->get(tenantVerificationUrl($user));

        expect(session('success'))->toBe('Registration successful. Welcome!');
    });

    test('already verified user is still redirected to the dashboard', function (): void {
        $user = makeTenantUser();

        $this->actingAs($user, 'tenant_user')
            ->get(tenantVerificationUrl($user))
            ->assertRedirect(tenantRoute('tenant.dashboard'));

        expect($user->fresh()->email_verified_at)->not->toBeNull();
    });
});


// ─── Group 3: Resend Verification Email ───────────────────────────────────────

describe('resend verification email', function (): void {
    test('sends a new verification notification to an unverified user', function (): void {
        Notification::fake();
        $user = makeTenantUser(['email_verified_at' => null]);

        $this->actingAs($user, 'tenant_user')
            ->post(tenantUrl('/email/verification-notification'))
            ->assertSessionHas('success', 'A new verification link has been sent to your email address.');

        Notification::assertSentTo($user, VerifyTenantUserEmail::class);
    });

    test('verified user is redirected home without sending a notification', function (): void {
        Notification::fake();
        $user = makeTenantUser();

        $this->actingAs($user, 'tenant_user')
            ->post(tenantUrl('/email/verification-notification'))
            ->assertRedirect(rtrim(tenantUrl('/'), '/'));

        Notification::assertNothingSent();
    });

    test('guest cannot request a verification email', function (): void {
        $this->post(tenantUrl('/email/verification-notification'))
            ->assertRedirect(tenantRoute('tenant.login'));
    });
});
