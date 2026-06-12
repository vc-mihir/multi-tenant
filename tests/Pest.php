<?php

use App\Models\Central\Company;
use App\Models\Tenant\Company as TenantCompany;
use App\Models\Tenant\User as TenantUser;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Domain Setup
|--------------------------------------------------------------------------
|
| Routes are bound to specific domains at boot time via Route::domain().
| Use setCentralDomain() or setTenantDomain() in beforeEach to ensure
| requests hit the correct route group during tests.
|
*/

/*
|--------------------------------------------------------------------------
| Domain Helpers
|--------------------------------------------------------------------------
*/

/**
 * Fake the HTTP host so requests resolve to the central domain route group.
 *
 * @return void
 */
function setCentralDomain(): void
{
    $host = parse_url(config('app.url'), PHP_URL_HOST);
    test()->withServerVariables(['HTTP_HOST' => $host, 'SERVER_NAME' => $host]);
}

/**
 * Fake the HTTP host so requests resolve to a tenant subdomain route group.
 *
 * @param string $subdomain
 * @return void
 */
function setTenantDomain(string $subdomain): void
{
    $host = $subdomain . '.' . parse_url(config('app.url'), PHP_URL_HOST);
    test()->withServerVariables(['HTTP_HOST' => $host, 'SERVER_NAME' => $host]);
}

/**
 * Fetch the seeded tenant company by its known email hash.
 *
 * @return TenantCompany
 */
function seededTenantCompany(): TenantCompany
{
    return TenantCompany::where('company_email_hash', hash('sha256', 'admin@acme.com'))->firstOrFail();
}

/**
 * Build a full URL on the acme tenant subdomain.
 * Required because Symfony overwrites HTTP_HOST from the URI host; a bare
 * path would always resolve to the central domain.
 *
 * @param string $path
 * @return string
 */
function tenantUrl(string $path): string
{
    $host = 'acme.' . parse_url(config('app.url'), PHP_URL_HOST);
    return 'http://' . $host . '/' . ltrim($path, '/');
}

/**
 * Generate a route URL with the tenant subdomain injected.
 *
 * @param string $name
 * @return string
 */
function tenantRoute(string $name): string
{
    return route($name, ['tenant' => 'acme']);
}

/**
 * Fetch the seeded SuperAdmin user by its known email hash.
 *
 * @return User
 */
function seededAdmin(): User
{
    return User::where('email_hash', hash('sha256', 'admin@system.com'))->firstOrFail();
}

/**
 * Creates and returns a verified, active Company with optional field overrides.
 *
 * @param array $overrides
 * @return Company
 */
function seedCompany(array $overrides = []): Company
{
    $attributes = array_merge([
        'company_name'      => 'Acme Corp',
        'subdomain'         => 'acme',
        'company_email'     => 'info@acme.com',
        'website'           => 'https://acme.com',
        'license_number'    => 'LIC-001',
        'address'           => '123 Main Street',
        'country'           => 'India',
        'state'             => 'Gujarat',
        'city'              => 'Ahmedabad',
        'password'          => 'Hello@123',
        'status'            => 'active',
        'email_verified_at' => now(),
    ], $overrides);

    $company = Company::create($attributes);

    // Mirror the company into the tenant database when one is set up, matching
    // the runtime flow where the company row exists in both central and tenant
    // databases. The shared id keeps the two records linkable (the service
    // syncs them by subdomain; tests look the central record up by id).
    if (config('database.connections.tenant.driver') === 'sqlite'
        && Schema::connection('tenant')->hasTable('companies')) {
        $tenantCompany = new TenantCompany($attributes);
        $tenantCompany->id = $company->id;
        $tenantCompany->master_company_id = $company->id;
        $tenantCompany->save();
    }

    return $company;
}

/*
|--------------------------------------------------------------------------
| Tenant Database Helpers
|--------------------------------------------------------------------------
*/

/**
 * Point the tenant connection to an in-memory SQLite database and create
 * the tables needed by Tenant\User. Also switches the default connection to
 * 'tenant' so the UserService security guard does not block operations.
 *
 * @return void
 */
function setUpTenantDb(): void
{
    config([
        'database.connections.tenant' => [
            'driver'   => 'sqlite',
            'database' => ':memory:',
        ],
    ]);

    DB::purge('tenant');

    Schema::connection('tenant')->create('companies', function (Blueprint $table): void {
        $table->uuid('id')->primary();
        $table->uuid('master_company_id')->unique();
        $table->string('company_name', 100)->index();
        $table->string('subdomain', 63)->unique();
        $table->text('company_email')->nullable();
        $table->string('company_email_hash', 64)->nullable()->unique();
        $table->string('website', 255);
        $table->text('license_number')->nullable();
        $table->string('license_number_hash', 64)->nullable()->unique();
        $table->text('address');
        $table->string('country', 100);
        $table->string('state', 100);
        $table->string('city', 100);
        $table->string('password', 60);
        $table->enum('status', ['active', 'inactive', 'suspended', 'pending'])->default('inactive')->index();
        $table->timestamp('email_verified_at')->nullable();
        $table->timestamps();
    });

    Schema::connection('tenant')->create('users', function (Blueprint $table): void {
        $table->uuid('id')->primary();
        $table->text('name');
        $table->string('name_hash', 64)->index();
        $table->text('email');
        $table->string('email_hash', 64)->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password', 60);
        $table->boolean('is_active')->default(false)->index();
        $table->rememberToken();
        $table->softDeletes();
        $table->timestamps();
    });

    DB::setDefaultConnection('tenant');
}

/**
 * Create a verified, active tenant user with optional overrides.
 *
 * @param array $overrides
 * @return TenantUser
 */
function makeTenantUser(array $overrides = []): TenantUser
{
    return TenantUser::create(array_merge([
        'name'              => 'John Doe',
        'email'             => 'john@acme.com',
        'password'          => 'User@1234',
        'email_verified_at' => now(),
        'is_active'         => true,
    ], $overrides));
}

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});
