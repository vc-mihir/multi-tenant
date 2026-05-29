<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        RateLimiter::for('admin_login', function (Request $request) {
            return Limit::perMinute(5)->by($request->input('email').$request->ip());
        });

        // Override the framework's default route('login') fallback so that
        // unauthenticated redirects always resolve to the correct guard login
        // page regardless of domain, even before withMiddleware() fires.
        Authenticate::redirectUsing(function (Request $request): string {
            $centralDomain = parse_url(config('app.url'), PHP_URL_HOST);

            if ($request->getHost() === $centralDomain) {
                return route('admin.login');
            }

            if ($request->is('admin/*')) {
                return route('tenant.admin.login');
            }

            return route('tenant.login');
        });
    }
}
