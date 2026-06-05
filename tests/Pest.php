<?php

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

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});
