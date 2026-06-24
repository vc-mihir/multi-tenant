<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class UnmapTenantSubdomainHost implements ShouldQueue
{
    use Queueable;

    /**
     * @param string $subdomain The validated tenant subdomain label (e.g. "acme").
     */
    public function __construct(public string $subdomain) {}

    /**
     * Remove the tenant subdomain mapping from /etc/hosts for local development.
     *
     * The counterpart to MapTenantSubdomainHost, dispatched only on PERMANENT
     * deletion. Like mapping, it deliberately runs on the queue worker rather than
     * the web request: php-fpm is sandboxed with `ProtectSystem=full` and cannot
     * write /etc, whereas the worker runs in a normal CLI context that can.
     * Delegates to the local-only, self-validating `tenant:remove-host` command,
     * which only ever deletes the exact line it originally wrote; failures are
     * logged, never thrown, so they cannot break the deletion that triggered them.
     *
     * @return void
     */
    public function handle(): void
    {
        // Defense in depth: tenant:remove-host is already local-only, but never
        // attempt hosts changes outside local even if a job reaches another env.
        if (! app()->environment('local')) {
            return;
        }

        $exitCode = Artisan::call('tenant:remove-host', [
            'subdomain' => $this->subdomain,
        ]);

        if ($exitCode !== 0) {
            Log::warning('UnmapTenantSubdomainHost: tenant:remove-host returned a non-zero exit code', [
                'subdomain' => $this->subdomain,
                'output'    => trim(Artisan::output()),
            ]);
        }
    }
}
