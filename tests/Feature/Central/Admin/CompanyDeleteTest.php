<?php

use App\Models\Central\Company;
use Database\Seeders\AdminUserSeeder;

beforeEach(function (): void {
    setCentralDomain();
    $this->seed(AdminUserSeeder::class);
});

// ─── Group 1: Destroy (soft delete / archive) ─────────────────────────────────

describe('destroy', function (): void {
    test('guest is redirected to login', function (): void {
        $company = seedCompany();

        $this->delete(route('admin.companies.destroy', $company))
            ->assertRedirect(route('admin.login'));
    });

    test('company is soft-deleted and returns JSON success', function (): void {
        $company = seedCompany();

        $this->actingAs(seededAdmin(), 'admin')
            ->deleteJson(route('admin.companies.destroy', $company))
            ->assertOk()
            ->assertJson(['success' => true, 'message' => 'Company archived successfully.']);

        expect(Company::withTrashed()->find($company->id)->deleted_at)->not->toBeNull();
    });
});

// ─── Group 2: Bulk Delete ─────────────────────────────────────────────────────

describe('bulk delete', function (): void {
    test('guest is redirected to login', function (): void {
        $company = seedCompany();

        $this->delete(route('admin.companies.bulk-delete'), ['ids' => [$company->id]])
            ->assertRedirect(route('admin.login'));
    });

    test('all selected companies are soft-deleted and count is returned', function (): void {
        $one = seedCompany([
            'company_name'   => 'Corp One',
            'subdomain'      => 'corpone',
            'company_email'  => 'one@test.com',
            'license_number' => 'LIC-001',
        ]);
        $two = seedCompany([
            'company_name'   => 'Corp Two',
            'subdomain'      => 'corptwo',
            'company_email'  => 'two@test.com',
            'license_number' => 'LIC-002',
        ]);

        $this->actingAs(seededAdmin(), 'admin')
            ->deleteJson(route('admin.companies.bulk-delete'), ['ids' => [$one->id, $two->id]])
            ->assertOk()
            ->assertJson(['success' => true, 'message' => 'Successfully archived 2 companies.']);

        expect(
            Company::withTrashed()->whereIn('id', [$one->id, $two->id])->whereNotNull('deleted_at')->count()
        )->toBe(2);
    });

    test('empty ids array is rejected', function (): void {
        $this->actingAs(seededAdmin(), 'admin')
            ->deleteJson(route('admin.companies.bulk-delete'), ['ids' => []])
            ->assertStatus(422);
    });
});

// ─── Group 3: Restore ─────────────────────────────────────────────────────────

describe('restore', function (): void {
    test('guest is redirected to login', function (): void {
        $company = seedCompany();
        $company->delete();

        $this->patch(route('admin.companies.restore', $company))
            ->assertRedirect(route('admin.login'));
    });

    test('soft-deleted company is restored and returns JSON success', function (): void {
        $company = seedCompany();
        $company->delete();

        $this->actingAs(seededAdmin(), 'admin')
            ->patchJson(route('admin.companies.restore', $company))
            ->assertOk()
            ->assertJson(['success' => true, 'message' => 'Company has been restored successfully.']);

        expect(Company::find($company->id)->deleted_at)->toBeNull();
    });
});

// ─── Group 4: Force Delete ────────────────────────────────────────────────────

describe('force delete', function (): void {
    test('guest is redirected to login', function (): void {
        $company = seedCompany();
        $company->delete();

        $this->delete(route('admin.companies.force-delete', $company))
            ->assertRedirect(route('admin.login'));
    });

    test('soft-deleted company is permanently removed and returns JSON success', function (): void {
        $company = seedCompany();
        $company->delete();

        $this->actingAs(seededAdmin(), 'admin')
            ->deleteJson(route('admin.companies.force-delete', $company))
            ->assertOk()
            ->assertJson(['success' => true, 'message' => 'Company permanently deleted and database dropped.']);

        expect(Company::withTrashed()->find($company->id))->toBeNull();
    });
});

// ─── Group 5: Bulk Restore ────────────────────────────────────────────────────

describe('bulk restore', function (): void {
    test('guest is redirected to login', function (): void {
        $company = seedCompany();
        $company->delete();

        $this->patch(route('admin.companies.bulk-restore'), ['ids' => [$company->id]])
            ->assertRedirect(route('admin.login'));
    });

    test('all selected soft-deleted companies are restored and count is returned', function (): void {
        $one = seedCompany([
            'company_name'   => 'Corp One',
            'subdomain'      => 'corpone',
            'company_email'  => 'one@test.com',
            'license_number' => 'LIC-001',
        ]);
        $two = seedCompany([
            'company_name'   => 'Corp Two',
            'subdomain'      => 'corptwo',
            'company_email'  => 'two@test.com',
            'license_number' => 'LIC-002',
        ]);
        $one->delete();
        $two->delete();

        $this->actingAs(seededAdmin(), 'admin')
            ->patchJson(route('admin.companies.bulk-restore'), ['ids' => [$one->id, $two->id]])
            ->assertOk()
            ->assertJson(['success' => true, 'message' => 'Successfully restored 2 companies.']);

        expect(Company::find($one->id)->deleted_at)->toBeNull()
            ->and(Company::find($two->id)->deleted_at)->toBeNull();
    });

    test('only soft-deleted companies are restored; active ones are ignored', function (): void {
        $archived = seedCompany([
            'company_name'   => 'Archived Co',
            'subdomain'      => 'archivedco',
            'company_email'  => 'archived@test.com',
            'license_number' => 'LIC-010',
        ]);
        $active = seedCompany([
            'company_name'   => 'Active Co',
            'subdomain'      => 'activeco',
            'company_email'  => 'active@test.com',
            'license_number' => 'LIC-011',
        ]);
        $archived->delete();

        $this->actingAs(seededAdmin(), 'admin')
            ->patchJson(route('admin.companies.bulk-restore'), ['ids' => [$archived->id, $active->id]])
            ->assertOk()
            ->assertJson(['success' => true, 'message' => 'Successfully restored 1 companies.']);

        expect(Company::find($archived->id)->deleted_at)->toBeNull()
            ->and(Company::find($active->id)->deleted_at)->toBeNull();
    });

    test('empty ids array is rejected', function (): void {
        $this->actingAs(seededAdmin(), 'admin')
            ->patchJson(route('admin.companies.bulk-restore'), ['ids' => []])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['ids']);
    });

    test('non-existent ids are rejected', function (): void {
        $this->actingAs(seededAdmin(), 'admin')
            ->patchJson(route('admin.companies.bulk-restore'), [
                'ids' => ['00000000-0000-0000-0000-000000000000'],
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['ids.0']);
    });
});

// ─── Group 6: Bulk Force Delete ───────────────────────────────────────────────

describe('bulk force delete', function (): void {
    test('guest is redirected to login', function (): void {
        $company = seedCompany();
        $company->delete();

        $this->delete(route('admin.companies.bulk-force-delete'), ['ids' => [$company->id]])
            ->assertRedirect(route('admin.login'));
    });

    test('all selected soft-deleted companies are permanently removed and count is returned', function (): void {
        $one = seedCompany([
            'company_name'   => 'Corp One',
            'subdomain'      => 'corpone',
            'company_email'  => 'one@test.com',
            'license_number' => 'LIC-001',
        ]);
        $two = seedCompany([
            'company_name'   => 'Corp Two',
            'subdomain'      => 'corptwo',
            'company_email'  => 'two@test.com',
            'license_number' => 'LIC-002',
        ]);
        $one->delete();
        $two->delete();

        $this->actingAs(seededAdmin(), 'admin')
            ->deleteJson(route('admin.companies.bulk-force-delete'), ['ids' => [$one->id, $two->id]])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Successfully permanently deleted 2 companies and dropped their databases.',
            ]);

        expect(Company::withTrashed()->find($one->id))->toBeNull()
            ->and(Company::withTrashed()->find($two->id))->toBeNull();
    });

    test('only soft-deleted companies are force-deleted; active ones are ignored', function (): void {
        $archived = seedCompany([
            'company_name'   => 'Archived Co',
            'subdomain'      => 'archivedco',
            'company_email'  => 'archived@test.com',
            'license_number' => 'LIC-010',
        ]);
        $active = seedCompany([
            'company_name'   => 'Active Co',
            'subdomain'      => 'activeco',
            'company_email'  => 'active@test.com',
            'license_number' => 'LIC-011',
        ]);
        $archived->delete();

        $this->actingAs(seededAdmin(), 'admin')
            ->deleteJson(route('admin.companies.bulk-force-delete'), ['ids' => [$archived->id, $active->id]])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Successfully permanently deleted 1 companies and dropped their databases.',
            ]);

        expect(Company::withTrashed()->find($archived->id))->toBeNull()
            ->and(Company::withTrashed()->find($active->id))->not->toBeNull();
    });

    test('empty ids array is rejected', function (): void {
        $this->actingAs(seededAdmin(), 'admin')
            ->deleteJson(route('admin.companies.bulk-force-delete'), ['ids' => []])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['ids']);
    });

    test('non-existent ids are rejected', function (): void {
        $this->actingAs(seededAdmin(), 'admin')
            ->deleteJson(route('admin.companies.bulk-force-delete'), [
                'ids' => ['00000000-0000-0000-0000-000000000000'],
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['ids.0']);
    });
});
