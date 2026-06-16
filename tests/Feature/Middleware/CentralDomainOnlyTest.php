<?php

use App\Http\Middleware\CentralDomainOnly;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/*
|--------------------------------------------------------------------------
| CentralDomainOnly Middleware
|--------------------------------------------------------------------------
*/

/**
 * Run the CentralDomainOnly middleware against a request and capture the
 * downstream response (a sentinel proving $next was reached).
 *
 * @param Request $request
 * @return Response
 */
function runCentralDomainOnly(Request $request): Response
{
    return (new CentralDomainOnly())->handle($request, fn (Request $req): Response => response('next-called'));
}


// ─── Group 1: Central Domain Access ───────────────────────────────────────────

describe('central domain access', function (): void {
    test('a request on the central domain passes through', function (): void {
        $response = runCentralDomainOnly(requestForHost(centralHost()));

        expect($response->getContent())->toBe('next-called');
    });
});


// ─── Group 2: Subdomain Access Is Blocked ─────────────────────────────────────

describe('subdomain access is blocked', function (): void {
    test('a tenant subdomain is rejected with 404 regardless of whether the tenant exists', function (): void {
        expect(fn (): Response => runCentralDomainOnly(requestForHost(tenantHost('acme'))))
            ->toThrow(NotFoundHttpException::class);
    });

    test('an unrelated external domain is rejected with 404', function (): void {
        expect(fn (): Response => runCentralDomainOnly(requestForHost('example.com')))
            ->toThrow(NotFoundHttpException::class);
    });
});
