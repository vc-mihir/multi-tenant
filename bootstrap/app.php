<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use App\Http\Middleware\CentralDomainOnly;
use App\Http\Middleware\IdentifyTenant;
use Illuminate\Routing\Middleware\SubstituteBindings;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role'               => RoleMiddleware::class,
            'permission'         => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'central'            => CentralDomainOnly::class,
            'identify_tenant'    => IdentifyTenant::class,
        ]);

        $middleware->priority([
            IdentifyTenant::class,
            SubstituteBindings::class,
        ]);

        $middleware->redirectUsersTo(function () {
            $host = request()->getHost();
            $centralDomain = parse_url(config('app.url'), PHP_URL_HOST);

            // Central Domain Redirection
            if ($host === $centralDomain) {
                return route('admin.dashboard');
            }

            // Tenant Subdomain Redirection
            if (request()->is('admin/*')) {
                return route('tenant.admin.dashboard');
            }

            return route('tenant.dashboard');
        });

        $middleware->redirectGuestsTo(function () {
            $host = request()->getHost();
            $centralDomain = parse_url(config('app.url'), PHP_URL_HOST);

            // Central Domain Redirection
            if ($host === $centralDomain) {
                return route('admin.login');
            }

            // Tenant Subdomain Redirection
            if (request()->is('admin/*')) {
                return route('tenant.admin.login');
            }

            return route('tenant.login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
