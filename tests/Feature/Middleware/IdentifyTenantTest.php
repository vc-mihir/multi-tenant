<?php

use App\Http\Middleware\IdentifyTenant;
use App\Models\Central\Company;
use App\Models\Central\CompanyDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/*
|--------------------------------------------------------------------------
| IdentifyTenant Middleware
|--------------------------------------------------------------------------
|
| The middleware is exercised directly (rather than through an HTTP route)
| so each branch can be asserted in isolation without provisioning a live
| MySQL tenant database. The tenant connection is repointed lazily, so the
| connection-switch branch can be verified via config/state alone.
|
*/

afterEach(function (): void {
    DB::setDefaultConnection('mysql');
    DB::purge('tenant');
});

/**
 * Build the central domain host from the configured app URL.
 *
 * @return string
 */
function centralHost(): string
{
    return parse_url(config('app.url'), PHP_URL_HOST);
}

/**
 * Build a tenant subdomain host from the configured app URL.
 *
 * @param string $subdomain
 * @return string
 */
function tenantHost(string $subdomain): string
{
    return $subdomain . '.' . centralHost();
}

/**
 * Create a GET request whose host resolves to the given domain.
 *
 * @param string $host
 * @return Request
 */
function requestForHost(string $host): Request
{
    return Request::create('http://' . $host . '/dashboard');
}

/**
 * Run the IdentifyTenant middleware against a request and capture the
 * downstream response (a sentinel proving $next was reached).
 *
 * @param Request $request
 * @return Response
 */
function runIdentifyTenant(Request $request): Response
{
    return (new IdentifyTenant())->handle($request, fn (Request $req): Response => response('next-called'));
}

/**
 * Invoke a callback expected to abort and return the HTTP status code.
 *
 * @param Closure $callback
 * @return int
 */
function abortStatusFrom(Closure $callback): int
{
    try {
        $callback();
    } catch (HttpException $exception) {
        return $exception->getStatusCode();
    }

    return 0;
}


// ─── Group 1: Non-tenant Requests Pass Through ────────────────────────────────

describe('non-tenant requests', function (): void {
    test('a request on the central domain passes through untouched', function (): void {
        $response = runIdentifyTenant(requestForHost(centralHost()));

        expect($response->getContent())->toBe('next-called')
            ->and(app()->bound(Company::class))->toBeFalse()
            ->and(DB::getDefaultConnection())->toBe('mysql');
    });

    test('a host that merely prefixes the central domain is not treated as a tenant', function (): void {
        $response = runIdentifyTenant(requestForHost('evil' . centralHost()));

        expect($response->getContent())->toBe('next-called')
            ->and(app()->bound(Company::class))->toBeFalse();
    });

    test('an unrelated external domain passes through', function (): void {
        $response = runIdentifyTenant(requestForHost('example.com'));

        expect($response->getContent())->toBe('next-called')
            ->and(app()->bound(Company::class))->toBeFalse();
    });
});


// ─── Group 2: Tenant Resolution Failures ──────────────────────────────────────

describe('tenant resolution failures', function (): void {
    test('an unknown subdomain aborts with 404', function (): void {
        expect(fn (): Response => runIdentifyTenant(requestForHost(tenantHost('ghost'))))
            ->toThrow(NotFoundHttpException::class, 'Tenant not found.');
    });

    test('a non-active tenant is forbidden with 403', function (string $status): void {
        seedCompany(['subdomain' => 'acme', 'status' => $status]);

        $code = abortStatusFrom(fn (): Response => runIdentifyTenant(requestForHost(tenantHost('acme'))));

        expect($code)->toBe(403);
    })->with(['inactive', 'suspended', 'pending']);

    test('the 403 message includes the tenant status', function (): void {
        seedCompany(['subdomain' => 'acme', 'status' => 'suspended']);

        expect(fn (): Response => runIdentifyTenant(requestForHost(tenantHost('acme'))))
            ->toThrow(HttpException::class, 'Your account is suspended');
    });
});


// ─── Group 3: Active Tenant Resolution ────────────────────────────────────────

describe('active tenant resolution', function (): void {
    test('binds the resolved company into the container', function (): void {
        $company = seedCompany(['subdomain' => 'acme', 'status' => 'active']);

        runIdentifyTenant(requestForHost(tenantHost('acme')));

        expect(app()->bound(Company::class))->toBeTrue()
            ->and(app(Company::class)->is($company))->toBeTrue();
    });

    test('sets the tenant default parameter for route generation', function (): void {
        seedCompany(['subdomain' => 'acme', 'status' => 'active']);

        runIdentifyTenant(requestForHost(tenantHost('acme')));

        expect(url()->getDefaultParameters())->toHaveKey('tenant', 'acme');
    });

    test('leaves the default connection on mysql when the tenant has no database record', function (): void {
        seedCompany(['subdomain' => 'acme', 'status' => 'active']);

        runIdentifyTenant(requestForHost(tenantHost('acme')));

        expect(DB::getDefaultConnection())->toBe('mysql');
    });

    test('overwrites the tenant connection with decrypted credentials and switches to it', function (): void {
        $company = seedCompany(['subdomain' => 'acme', 'status' => 'active']);

        CompanyDatabase::create([
            'company_id'  => $company->id,
            'db_name'     => 'tenant_company_acme',
            'db_host'     => '127.0.0.1',
            'db_port'     => '3306',
            'db_username' => Crypt::encryptString('tenant_dbuser'),
            'db_password' => Crypt::encryptString('tenant_secret'),
        ]);

        runIdentifyTenant(requestForHost(tenantHost('acme')));

        expect(config('database.connections.tenant.database'))->toBe('tenant_company_acme')
            ->and(config('database.connections.tenant.host'))->toBe('127.0.0.1')
            ->and((string) config('database.connections.tenant.port'))->toBe('3306')
            ->and(config('database.connections.tenant.username'))->toBe('tenant_dbuser')
            ->and(config('database.connections.tenant.password'))->toBe('tenant_secret')
            ->and(DB::getDefaultConnection())->toBe('tenant');
    });
});
