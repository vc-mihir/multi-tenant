<?php

use App\Jobs\CreateCompanyDatabase;
use App\Models\Central\CompanyDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| CreateCompanyDatabase Job
|--------------------------------------------------------------------------
*/

/**
 * Compute the tenant database name the job derives from a company name.
 *
 * @param string $companyName
 * @return string
 */
function tenantDbName(string $companyName): string
{
    return 'tenant_company_' . Str::slug($companyName, '_');
}

afterEach(function (): void {
    foreach (DB::connection('mysql')->table('company_databases')->pluck('db_name') as $dbName) {
        if (str_starts_with((string) $dbName, 'tenant_company_pest_')) {
            DB::connection('mysql')->statement("DROP DATABASE IF EXISTS `{$dbName}`");
        }
    }

    DB::connection('mysql')->table('companies')->delete();

    config(['database.connections.tenant.database' => null]);
    DB::purge('tenant');
});

describe('successful provisioning', function (): void {
    test('creates the database and runs the tenant migrations', function (): void {
        $company = seedCompany([
            'company_name' => 'Pest Provision Co',
            'subdomain'    => 'pestprovision',
        ]);

        (new CreateCompanyDatabase($company))->handle();

        expect(Schema::connection('tenant')->hasTable('companies'))->toBeTrue()
            ->and(Schema::connection('tenant')->hasTable('users'))->toBeTrue();
    });

    test('seeds the tenant company row with encrypted email and license plus correct hashes', function (): void {
        $company = seedCompany([
            'company_name'   => 'Pest Seed Co',
            'subdomain'      => 'pestseed',
            'company_email'  => 'owner@pestseed.test',
            'license_number' => 'LIC-SEED-1',
        ]);

        (new CreateCompanyDatabase($company))->handle();

        $row = DB::connection('tenant')->table('companies')
            ->where('master_company_id', $company->id)
            ->first();

        expect($row)->not->toBeNull()
            ->and(decrypt($row->company_email))->toBe('owner@pestseed.test')
            ->and($row->company_email_hash)->toBe(hash('sha256', strtolower('owner@pestseed.test')))
            ->and(decrypt($row->license_number))->toBe('LIC-SEED-1')
            ->and($row->license_number_hash)->toBe(hash('sha256', strtolower('LIC-SEED-1')));
    });

    test('mirrors the company status and links back via master_company_id', function (): void {
        $company = seedCompany([
            'company_name' => 'Pest Mirror Co',
            'subdomain'    => 'pestmirror',
            'status'       => 'active',
        ]);

        (new CreateCompanyDatabase($company))->handle();

        $row = DB::connection('tenant')->table('companies')
            ->where('master_company_id', $company->id)
            ->first();

        expect($row->subdomain)->toBe('pestmirror')
            ->and($row->status)->toBe('active')
            ->and($row->password)->toBe($company->password);
    });

    test('stores encrypted connection credentials in the central company_databases table', function (): void {
        $company = seedCompany([
            'company_name' => 'Pest Creds Co',
            'subdomain'    => 'pestcreds',
        ]);

        (new CreateCompanyDatabase($company))->handle();

        $record  = CompanyDatabase::where('company_id', $company->id)->first();
        $default = config('database.default');

        expect($record)->not->toBeNull()
            ->and($record->db_name)->toBe(tenantDbName('Pest Creds Co'))
            ->and((string) $record->db_host)->toBe((string) config("database.connections.{$default}.host"))
            ->and((string) $record->db_port)->toBe((string) config("database.connections.{$default}.port"))
            ->and(Crypt::decryptString($record->db_username))->toBe((string) config("database.connections.{$default}.username"))
            ->and(Crypt::decryptString($record->db_password))->toBe((string) config("database.connections.{$default}.password"));
    });
});

describe('idempotency', function (): void {
    test('running twice updates the existing rows instead of duplicating them', function (): void {
        $company = seedCompany([
            'company_name' => 'Pest Idem Co',
            'subdomain'    => 'pestidem',
        ]);

        (new CreateCompanyDatabase($company))->handle();
        (new CreateCompanyDatabase($company))->handle();

        $tenantRows = DB::connection('tenant')->table('companies')
            ->where('master_company_id', $company->id)
            ->count();

        expect($tenantRows)->toBe(1)
            ->and(CompanyDatabase::where('company_id', $company->id)->count())->toBe(1);
    });
});

describe('failure handling', function (): void {
    test('re-throws and stores no credentials when the database cannot be created', function (): void {
        $company = seedCompany([
            'company_name'   => 'Pest ' . str_repeat('x', 70),
            'subdomain'      => 'pestfail',
            'company_email'  => 'fail@pestfail.test',
            'license_number' => 'LIC-FAIL-1',
        ]);

        expect(fn (): mixed => (new CreateCompanyDatabase($company))->handle())
            ->toThrow(\Illuminate\Database\QueryException::class);

        expect(CompanyDatabase::where('company_id', $company->id)->count())->toBe(0);
    });
});
