<?php

namespace App\Console\Commands\Tenant;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class RemoveTenantHost extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tenant:remove-host {subdomain : The tenant subdomain label (e.g. "acme")}';

    /**
     * The console command description.
     */
    protected $description = 'Remove a tenant subdomain mapping from /etc/hosts (local development only)';

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

        // Same strict DNS-label whitelist used when adding, so a malformed
        // subdomain can never be turned into a match pattern.
        if (! preg_match('/^[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?$/', $subdomain)) {
            $this->error("Invalid subdomain rejected: '{$subdomain}'");

            return self::FAILURE;
        }

        $baseHost = parse_url((string) config('app.url'), PHP_URL_HOST);

        if (! $baseHost) {
            $this->error('Could not determine the base host from APP_URL.');

            return self::FAILURE;
        }

        $host = "{$subdomain}.{$baseHost}";

        // /etc/hosts is world-readable, so we can read it without sudo.
        $current = @file_get_contents($this->hostsFile);

        if ($current === false) {
            $this->error("Unable to read {$this->hostsFile}.");

            return self::FAILURE;
        }

        // Rebuild the file from its existing lines, dropping ONLY the entries we
        // are certain we created for this host. Every other line is carried over
        // byte-for-byte, so nothing else can be altered or lost.
        $lines   = explode("\n", $current);
        $kept     = [];
        $removed   = 0;

        foreach ($lines as $line) {
            if ($this->isOurEntry($line, $host)) {
                $removed++;

                continue;
            }

            $kept[] = $line;
        }

        if ($removed === 0) {
            $this->info("No matching entry found, nothing to remove: {$host}");

            return self::SUCCESS;
        }

        $new = implode("\n", $kept);

        // --- Safety guards: refuse to write anything that could clobber the file.
        // These make an accidental wipe impossible even if the filter had a bug.

        // 1) Never write empty / whitespace-only content.
        if (trim($new) === '') {
            $this->error('Refusing to write: the resulting /etc/hosts would be empty.');
            Log::error('RemoveTenantHost: aborted, result would be empty', ['host' => $host]);

            return self::FAILURE;
        }

        // 2) The localhost mapping must survive — losing it is the classic symptom
        //    of an accidental clobber, so its absence means something went wrong.
        if (! preg_match('/\blocalhost\b/', $new)) {
            $this->error('Refusing to write: the result no longer contains "localhost".');
            Log::error('RemoveTenantHost: aborted, localhost missing from result', ['host' => $host]);

            return self::FAILURE;
        }

        // Overwrite the file (NOT append) via the password-less sudo rule, e.g.:
        //   mihirkothari ALL=(root) NOPASSWD: /usr/bin/tee /etc/hosts
        // -n => never prompt for a password; fail fast if the rule is missing.
        $result = Process::input($new)
            ->run(['sudo', '-n', '/usr/bin/tee', $this->hostsFile]);

        if ($result->failed()) {
            $message = trim($result->errorOutput()) ?: 'Unknown error.';
            $this->error("Failed to update {$this->hostsFile}: {$message}");
            Log::error('RemoveTenantHost: failed to write hosts file', [
                'host'   => $host,
                'stderr' => $message,
            ]);

            return self::FAILURE;
        }

        $noun = $removed === 1 ? 'entry' : 'entries';
        $this->info("Removed {$removed} hosts {$noun}: {$host}");
        Log::info('RemoveTenantHost: hosts entry removed', ['host' => $host, 'removed' => $removed]);

        return self::SUCCESS;
    }

    /**
     * Decide whether a line is an entry THIS tool created for $host, and is
     * therefore safe to remove.
     *
     * To guarantee we never delete anything the user added by hand, a line
     * qualifies only when it is exactly our generated form: the loopback address
     * followed by the single target host and nothing else. A comment, an extra
     * alias on the line, or a different IP all disqualify it — those lines are
     * preserved untouched.
     *
     * @param string $rawLine Raw line from /etc/hosts (may carry a trailing \r).
     * @param string $host    Fully-qualified host we are removing.
     * @return bool
     */
    protected function isOurEntry(string $rawLine, string $host): bool
    {
        // Normalise only for comparison; the original $rawLine is what gets kept.
        $line = trim(str_replace("\r", '', $rawLine));

        // Blank lines and comment lines are never ours to touch.
        if ($line === '' || str_starts_with($line, '#')) {
            return false;
        }

        $tokens = preg_split('/\s+/', $line);

        // Exactly [loopback, host] — byte-for-byte what AddTenantHost writes.
        // Anything with extra aliases or a different IP is left in place.
        return $tokens === [$this->loopback, $host];
    }
}
