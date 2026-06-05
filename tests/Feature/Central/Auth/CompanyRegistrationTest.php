<?php

use App\Jobs\CreateCompanyDatabase;
use App\Models\Central\Company;
use App\Notifications\VerifyCompanyEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;

beforeEach(function (): void {
    setCentralDomain();
    Notification::fake();
    Queue::fake();
});

/**
 * Returns a valid company registration form payload with optional field overrides.
 *
 * @param array $overrides
 * @return array
 */
function validCompanyPayload(array $overrides = []): array
{
    return array_merge([
        'company_name'          => 'Abc',
        'subdomain'             => 'abc',
        'company_email'         => 'abc@test.com',
        'password'              => 'Hello@123',
        'password_confirmation' => 'Hello@123',
        'website'               => 'https://abc.com',
        'license_number'        => 'LIC-001',
        'address'               => '123 Main Street',
        'country'               => 'India',
        'state'                 => 'Gujarat',
        'city'                  => 'Ahmedabad',
    ], $overrides);
}

/**
 * Creates a Company record directly via the model for test setup, bypassing the HTTP layer.
 *
 * @param array $overrides
 * @return Company
 */
function createTestCompany(array $overrides = []): Company
{
    return Company::create(validCompanyPayload(['status' => 'inactive', ...$overrides]));
}

// ─── Group 1: Registration Page ───────────────────────────────────────────────

describe('registration page', function (): void {
    test('renders successfully', function (): void {
        expect($this->get('/company-register')->status())->toBe(200);
    });
});

// ─── Group 2: Successful Submission ───────────────────────────────────────────

describe('successful submission', function (): void {
    test('creates a company record in the database', function (): void {
        $this->post('/company-register', validCompanyPayload());

        expect(Company::count())->toBe(1);
    });

    test('new company starts as inactive with email unverified', function (): void {
        $this->post('/company-register', validCompanyPayload());

        $company = Company::first();

        expect($company->status)->toBe('inactive')
            ->and($company->email_verified_at)->toBeNull();
    });

    test('company password is hashed before storage', function (): void {
        $this->post('/company-register', validCompanyPayload());

        expect(Hash::check('Hello@123', Company::first()->password))->toBeTrue();
    });

    test('company email is encrypted and its hash is stored correctly', function (): void {
        $this->post('/company-register', validCompanyPayload());

        $row = DB::table('companies')->first();

        expect(decrypt($row->company_email))->toBe('abc@test.com')
            ->and($row->company_email_hash)->toBe(hash('sha256', 'abc@test.com'));
    });

    test('license number is encrypted and its hash is stored correctly', function (): void {
        $this->post('/company-register', validCompanyPayload());

        $row = DB::table('companies')->first();

        expect(decrypt($row->license_number))->toBe('LIC-001')
            ->and($row->license_number_hash)->toBe(hash('sha256', strtolower('LIC-001')));
    });

    test('verification email notification is sent', function (): void {
        $this->post('/company-register', validCompanyPayload());

        expect(Notification::sent(Company::first(), VerifyCompanyEmail::class))->not->toBeEmpty();
    });

    test('redirects to the verification notice page', function (): void {
        $response = $this->post('/company-register', validCompanyPayload());

        expect($response->isRedirect())->toBeTrue()
            ->and($response->headers->get('Location'))->toContain(
                route('companies.verification.notice', ['id' => Company::first()->id])
            );
    });
});

// ─── Group 3: Validation ──────────────────────────────────────────────────────

describe('validation', function (): void {
    test('empty form submission returns errors for all required fields', function (): void {
        $response = $this->from('/company-register')->post('/company-register', []);

        $response->assertSessionHasErrors([
            'company_name',
            'subdomain',
            'company_email',
            'password',
            'website',
            'license_number',
            'address',
            'country',
            'state',
            'city',
        ]);
    });

    test('duplicate company name is rejected', function (): void {
        createTestCompany();

        $response = $this->from('/company-register')->post('/company-register', validCompanyPayload([
            'subdomain'      => 'other',
            'company_email'  => 'other@test.com',
            'license_number' => 'LIC-999',
        ]));

        $response->assertSessionHasErrors(['company_name']);
    });

    test('duplicate subdomain is rejected', function (): void {
        createTestCompany();

        $response = $this->from('/company-register')->post('/company-register', validCompanyPayload([
            'company_name'   => 'Other Corp',
            'company_email'  => 'other@test.com',
            'license_number' => 'LIC-999',
        ]));

        $response->assertSessionHasErrors(['subdomain']);
    });

    test('duplicate company email is rejected via hash-based lookup', function (): void {
        createTestCompany();

        $response = $this->from('/company-register')->post('/company-register', validCompanyPayload([
            'company_name'   => 'Other Corp',
            'subdomain'      => 'other',
            'license_number' => 'LIC-999',
        ]));

        $response->assertSessionHasErrors(['company_email']);
    });

    test('duplicate license number is rejected via hash-based lookup', function (): void {
        createTestCompany();

        $response = $this->from('/company-register')->post('/company-register', validCompanyPayload([
            'company_name'  => 'Other Corp',
            'subdomain'     => 'other',
            'company_email' => 'other@test.com',
        ]));

        $response->assertSessionHasErrors(['license_number']);
    });

    test('weak password is rejected', function (): void {
        $response = $this->from('/company-register')->post('/company-register', validCompanyPayload([
            'password'              => 'simple',
            'password_confirmation' => 'simple',
        ]));

        $response->assertSessionHasErrors(['password']);
    });

    test('password confirmation mismatch is rejected', function (): void {
        $response = $this->from('/company-register')->post('/company-register', validCompanyPayload([
            'password_confirmation' => 'Different@123',
        ]));

        $response->assertSessionHasErrors(['password']);
    });

    test('invalid subdomain formats are rejected', function (string $subdomain): void {
        $response = $this->from('/company-register')->post('/company-register', validCompanyPayload([
            'subdomain' => $subdomain,
        ]));

        $response->assertSessionHasErrors(['subdomain']);
    })->with([
        'uppercase letters' => 'Acme',
        'underscore'        => 'acme_corp',
        'leading hyphen'    => '-acme',
        'trailing hyphen'   => 'acme-',
    ]);

    test('invalid website url is rejected', function (): void {
        $response = $this->from('/company-register')->post('/company-register', validCompanyPayload([
            'website' => 'not-a-url',
        ]));

        $response->assertSessionHasErrors(['website']);
    });
});

// ─── Group 4: Email Verification ──────────────────────────────────────────────

describe('email verification', function (): void {
    test('notice page renders for an unverified company', function (): void {
        $company = createTestCompany();

        expect($this->get(route('companies.verification.notice', ['id' => $company->id]))->status())->toBe(200);
    });

    test('verifying email marks company as active and dispatches provisioning job', function (): void {
        $company = createTestCompany();

        $this->get(route('companies.verification.verify', ['id' => $company->id]));

        $company->refresh();

        $pushed = Queue::pushed(CreateCompanyDatabase::class);

        expect($company->email_verified_at)->not->toBeNull()
            ->and($company->status)->toBe('active')
            ->and($pushed)->not->toBeEmpty()
            ->and($pushed->first()->company->id)->toBe($company->id);
    });

    test('verifying an already verified email does not dispatch the provisioning job again', function (): void {
        $company = createTestCompany([
            'status'            => 'active',
            'email_verified_at' => now(),
        ]);

        $this->get(route('companies.verification.verify', ['id' => $company->id]));

        expect(Queue::pushed(CreateCompanyDatabase::class))->toBeEmpty();
    });
});

// ─── Group 5: Resend Verification ─────────────────────────────────────────────

describe('resend verification', function (): void {
    test('sends the notification and returns a status message', function (): void {
        $company = createTestCompany();

        $this->post(route('companies.verification.send', ['id' => $company->id]));

        expect(Notification::sent($company, VerifyCompanyEmail::class))->not->toBeEmpty()
            ->and(session()->has('status'))->toBeTrue();
    });

    test('returns an error for an already verified company', function (): void {
        $company = createTestCompany([
            'status'            => 'active',
            'email_verified_at' => now(),
        ]);

        $this->from(route('companies.verification.notice', ['id' => $company->id]))
            ->post(route('companies.verification.send', ['id' => $company->id]));

        expect(session('error'))->toBe('Company account is already active.');
    });
});
