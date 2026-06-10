<?php

use App\Models\Central\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Domain Setup
|--------------------------------------------------------------------------
|
| Routes are bound to specific domains at boot time via Route::domain().
| Use setCentralDomain() or setTenantDomain() in beforeEach to ensure
| requests hit the correct route group during tests.
|
*/

/*
|--------------------------------------------------------------------------
| Domain Helpers
|--------------------------------------------------------------------------
*/

/**
 * Fake the HTTP host so requests resolve to the central domain route group.
 *
 * @return void
 */
function setCentralDomain(): void
{
    $host = parse_url(config('app.url'), PHP_URL_HOST);
    test()->withServerVariables(['HTTP_HOST' => $host, 'SERVER_NAME' => $host]);
}

/**
 * Fake the HTTP host so requests resolve to a tenant subdomain route group.
 *
 * @param string $subdomain
 * @return void
 */
function setTenantDomain(string $subdomain): void
{
    $host = $subdomain . '.' . parse_url(config('app.url'), PHP_URL_HOST);
    test()->withServerVariables(['HTTP_HOST' => $host, 'SERVER_NAME' => $host]);
}

/**
 * Fetch the seeded SuperAdmin user by its known email hash.
 *
 * @return User
 */
function seededAdmin(): User
{
    return User::where('email_hash', hash('sha256', 'admin@system.com'))->firstOrFail();
}

/**
 * Creates and returns a verified, active Company with optional field overrides.
 *
 * @param array $overrides
 * @return Company
 */
function seedCompany(array $overrides = []): Company
{
    return Company::create(array_merge([
        'company_name'      => 'Acme Corp',
        'subdomain'         => 'acme',
        'company_email'     => 'info@acme.com',
        'website'           => 'https://acme.com',
        'license_number'    => 'LIC-001',
        'address'           => '123 Main Street',
        'country'           => 'India',
        'state'             => 'Gujarat',
        'city'              => 'Ahmedabad',
        'password'          => 'Hello@123',
        'status'            => 'active',
        'email_verified_at' => now(),
    ], $overrides));
}

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});
