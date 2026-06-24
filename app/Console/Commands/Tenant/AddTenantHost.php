<?php

namespace App\Console\Commands\Tenant;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class AddTenantHost extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tenant:add-host {subdomain : The tenant subdomain label (e.g. "acme")}';

    /**
     * The console command description.
     */
    protected $description = 'Map a tenant subdomain to 127.0.0.1 in /etc/hosts (local development only)';

    /**
     * Absolute path to the hosts file.
     */
    protected string $hostsFile = '/etc/hosts';

    /**
     * Loopback address that tenant subdomains resolve to locally.
     */
    protected string $loopback = '127.0.0.1';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        // Safety guard: this edits a protected system file, so it must NEVER run
        // outside local development. On staging/production, real wildcard DNS is
        // expected to resolve tenant subdomains instead.
        if (! app()->environment('local')) {
            $this->warn('Skipped: /etc/hosts is only modified in the local environment.');

            return self::SUCCESS;
        }

        $subdomain = strtolower(trim((string) $this->argument('subdomain')));

        // Strict whitelist — a valid DNS label: lowercase alphanumerics and
        // hyphens, never starting/ending with a hyphen, max 63 chars. This is the
        // security boundary: it makes it impossible to smuggle whitespace, extra
        // hostnames, or newlines into the line we append to /etc/hosts.
        if (! preg_match('/^[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?$/', $subdomain)) {
            $this->error("Invalid subdomain rejected: '{$subdomain}'");

            return self::FAILURE;
        }

        // Derive the base host from APP_URL (e.g. "multi-tenant.test"), matching
        // how the rest of the app builds tenant URLs.
        $baseHost = parse_url((string) config('app.url'), PHP_URL_HOST);

        if (! $baseHost) {
            $this->error('Could not determine the base host from APP_URL.');

            return self::FAILURE;
        }

        $host = "{$subdomain}.{$baseHost}";

        // Read the current file so we can skip duplicates. /etc/hosts is
        // world-readable, so this works without sudo.
        $current = @file_get_contents($this->hostsFile);

        if ($current === false) {
            $this->error("Unable to read {$this->hostsFile}.");

            return self::FAILURE;
        }

        if ($this->hostIsMapped($current, $host)) {
            $this->info("Already mapped, nothing to do: {$host}");

            return self::SUCCESS;
        }

        // Guarantee the new entry begins on its own line even if the file does
        // not end with a trailing newline.
        $prefix = ($current === '' || str_ends_with($current, "\n")) ? '' : "\n";
        $line   = "{$prefix}{$this->loopback}\t{$host}\n";

        // Append via the password-less sudo rule configured for the web-server
        // user (see README), e.g.:
        //   www-data ALL=(ALL) NOPASSWD: /usr/bin/tee -a /etc/hosts
        // -n => never prompt for a password; fail fast if the rule is missing.
        $result = Process::input($line)
            ->run(['sudo', '-n', '/usr/bin/tee', '-a', $this->hostsFile]);

        if ($result->failed()) {
            $message = trim($result->errorOutput()) ?: 'Unknown error.';
            $this->error("Failed to update {$this->hostsFile}: {$message}");
            Log::error('AddTenantHost: failed to write hosts entry', [
                'host'   => $host,
                'stderr' => $message,
            ]);

            return self::FAILURE;
        }

        $this->info("Added hosts entry: {$this->loopback}\t{$host}");
        Log::info('AddTenantHost: hosts entry added', ['host' => $host]);

        return self::SUCCESS;
    }

    /**
     * Determine whether the given host is already mapped on a non-comment line.
     *
     * @param string $contents Raw /etc/hosts contents.
     * @param string $host     Fully-qualified host to look for.
     * @return bool
     */
    protected function hostIsMapped(string $contents, string $host): bool
    {
        foreach (preg_split('/\r\n|\r|\n/', $contents) as $rawLine) {
            // Drop inline comments, then split into "IP host [aliases...]".
            $line = trim(explode('#', $rawLine, 2)[0]);

            if ($line === '') {
                continue;
            }

            $tokens = preg_split('/\s+/', $line);

            // tokens[0] is the IP address; the remainder are hostnames/aliases.
            if (in_array($host, array_slice($tokens, 1), true)) {
                return true;
            }
        }

        return false;
    }
}
