<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class MapTenantSubdomainHost implements ShouldQueue
{
    use Queueable;

    /**
     * @param string $subdomain The validated tenant subdomain label (e.g. "acme").
     */
    public function __construct(public string $subdomain) {}

    /**
     * Map the tenant subdomain into /etc/hosts for local development.
     *
     * This deliberately runs on the queue worker, NOT in the web request: php-fpm
     * is sandboxed with `ProtectSystem=full`, which mounts /etc read-only inside
     * its namespace, so `sudo tee /etc/hosts` fails there even as root. The queue
     * worker runs in a normal CLI context that can write /etc/hosts via the
     * password-less sudo rule. Delegates to the local-only, self-validating
     * `tenant:add-host` command; failures are logged, never thrown.
     *
     * @return void
     */
    public function handle(): void
    {
        // Defense in depth: tenant:add-host is already local-only, but never
        // attempt hosts mapping outside local even if a job reaches another env.
        if (! app()->environment('local')) {
            return;
        }

        $exitCode = Artisan::call('tenant:add-host', [
            'subdomain' => $this->subdomain,
        ]);

        if ($exitCode !== 0) {
            Log::warning('MapTenantSubdomainHost: tenant:add-host returned a non-zero exit code', [
                'subdomain' => $this->subdomain,
                'output'    => trim(Artisan::output()),
            ]);
        }
    }
}
