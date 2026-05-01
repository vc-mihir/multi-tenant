<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CentralDomainOnly
{
    /**
     * Block any request that arrives via a subdomain.
     * Only requests whose host exactly matches the central domain are allowed through.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $centralDomain = parse_url(config('app.url'), PHP_URL_HOST);

        if ($request->getHost() !== $centralDomain) {
            abort(404);
        }

        return $next($request);
    }
}
