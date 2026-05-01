<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class IdentifyTenant
{
    /**
     * checking tenant is exist or not and switch database connection
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $centralDomain = parse_url(config('app.url'), PHP_URL_HOST);
        $host = $request->getHost();

        if($host !== $centralDomain && str_ends_with($host, '.' . $centralDomain)) {
            $subdomain = explode('.', $host)[0];
            $tenant = Company::where('subdomain', $subdomain)->with('database')->first();

            if($tenant === null) {
                abort(404, 'Tenant not found.');
            }

            app()->instance(Company::class, $tenant);

            if($tenant->database) {
                $db = $tenant->database;
                
                config([
                    'database.connections.tenant.database' => $db->db_name,
                    'database.connections.tenant.host'     => $db->db_host,
                    'database.connections.tenant.port'     => $db->db_port,
                    'database.connections.tenant.username' => $db->db_username,
                    'database.connections.tenant.password' => $db->db_password,
                ]);

                DB::purge('tenant');
                DB::reconnect('tenant');
                DB::setDefaultConnection('tenant');
            }
        }

        return $next($request);
    }
}
