<?php

namespace App\Http\Controllers\Central\Auth;

use App\Http\Controllers\Controller;
use App\Models\Central\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class CompanyProvisioningController extends Controller
{
    /**
     * Report tenant provisioning progress for the post-verification loader.
     *
     * Returns one of:
     *   - ready   : the tenant DB record exists and (locally) the subdomain is
     *               mapped in /etc/hosts, so the site is reachable.
     *   - pending : still in progress (the loader falls back on its own timeout).
     *
     * @param string $id
     * @return JsonResponse
     */
    public function status(string $id): JsonResponse
    {
        $company = Company::with('database')->findOrFail($id);

        // The CompanyDatabase row is written at the very end of the provisioning
        // job, so its presence means the tenant DB is fully created and seeded.
        $databaseReady = $company->database !== null;

        // The /etc/hosts mapping only matters locally; elsewhere real wildcard DNS
        // resolves tenant subdomains, so the host is always considered reachable.
        $hostReady = ! app()->environment('local')
            || $this->subdomainIsMapped($company->subdomain);

        return response()->json([
            'state' => ($databaseReady && $hostReady) ? 'ready' : 'pending',
        ]);
    }

    /**
     * Fallback when provisioning does not finish in time.
     *
     * The account is already created and verified, so this returns to the
     * registration page with a success notice rather than surfacing an error.
     *
     * @param string $id
     * @return RedirectResponse
     */
    public function fallback(string $id): RedirectResponse
    {
        return redirect()->route('register')->with(
            'success',
            "Your company account has been created and verified successfully. We're finishing setting up your workspace — you'll be able to access it shortly."
        );
    }

    /**
     * Determine whether the tenant subdomain is mapped in /etc/hosts.
     *
     * Mirrors the matching used when the entry is written: the host must appear
     * as a hostname/alias on a non-comment line. /etc/hosts is world-readable, so
     * no elevated permission is needed to inspect it.
     *
     * @param string|null $subdomain
     * @return bool
     */
    protected function subdomainIsMapped(?string $subdomain): bool
    {
        if (blank($subdomain)) {
            return false;
        }

        $baseHost = parse_url((string) config('app.url'), PHP_URL_HOST);

        if (! $baseHost) {
            return false;
        }

        $host     = "{$subdomain}.{$baseHost}";
        $contents = @file_get_contents('/etc/hosts');

        if ($contents === false) {
            return false;
        }

        foreach (preg_split('/\r\n|\r|\n/', $contents) as $rawLine) {
            $line = trim(explode('#', $rawLine, 2)[0]);

            if ($line === '') {
                continue;
            }

            $tokens = preg_split('/\s+/', $line);

            if (in_array($host, array_slice($tokens, 1), true)) {
                return true;
            }
        }

        return false;
    }
}
