<?php

use App\Models\Tenant\User as TenantUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

beforeEach(function (): void {
    seedCompany([
        'company_email' => 'admin@acme.com',
        'password'      => 'Admin@123',
    ]);
    setTenantDomain('acme');
    setUpTenantDb();
});

afterEach(function (): void {
    DB::setDefaultConnection('mysql');
    DB::purge('tenant');
});


// ─── Group 1: Access Control ──────────────────────────────────────────────────

describe('access control', function (): void {
    test('users index renders for authenticated admin', function (): void {
        $this->actingAs(seededTenantCompany(), 'company')
            ->get(tenantUrl('/admin/users'))
            ->assertStatus(200);
    });

    test('guest is redirected to login', function (): void {
        $this->get(tenantUrl('/admin/users'))
            ->assertRedirect(tenantRoute('tenant.admin.login'));
    });

    test('authenticated admin is redirected to dashboard from login page', function (): void {
        $this->actingAs(seededTenantCompany(), 'company')
            ->get(tenantUrl('/admin/login'))
            ->assertRedirect(tenantRoute('tenant.admin.dashboard'));
    });
});


// ─── Group 1b: Listing & DataTables Endpoints ─────────────────────────────────

describe('listing endpoints', function (): void {
    test('create form renders for authenticated admin', function (): void {
        $this->actingAs(seededTenantCompany(), 'company')
            ->get(tenantUrl('/admin/users/create'))
            ->assertStatus(200);
    });

    test('archived listing renders for authenticated admin', function (): void {
        $this->actingAs(seededTenantCompany(), 'company')
            ->get(tenantUrl('/admin/users/archived'))
            ->assertStatus(200);
    });

    test('data endpoint returns only active users', function (): void {
        makeTenantUser(['email' => 'active1@acme.com']);
        makeTenantUser(['email' => 'active2@acme.com']);
        makeTenantUser(['email' => 'archived@acme.com'])->delete();

        $this->actingAs(seededTenantCompany(), 'company')
            ->getJson(tenantUrl('/admin/users/data'))
            ->assertOk()
            ->assertJsonPath('recordsTotal', 2)
            ->assertSee('active1@acme.com')
            ->assertSee('active2@acme.com')
            ->assertDontSee('archived@acme.com');
    });

    test('archived data endpoint returns only soft-deleted users', function (): void {
        makeTenantUser(['email' => 'active@acme.com']);
        makeTenantUser(['email' => 'archived@acme.com'])->delete();

        $this->actingAs(seededTenantCompany(), 'company')
            ->getJson(tenantUrl('/admin/users/archived/data'))
            ->assertOk()
            ->assertJsonPath('recordsTotal', 1)
            ->assertSee('archived@acme.com')
            ->assertDontSee('active@acme.com');
    });

    test('guest cannot access the data endpoint', function (): void {
        $this->get(tenantUrl('/admin/users/data'))
            ->assertRedirect(tenantRoute('tenant.admin.login'));
    });

    test('guest cannot access the archived data endpoint', function (): void {
        $this->get(tenantUrl('/admin/users/archived/data'))
            ->assertRedirect(tenantRoute('tenant.admin.login'));
    });
});


// ─── Group 2: Create User ─────────────────────────────────────────────────────

describe('create user', function (): void {
    test('stores a new user and redirects with success', function (): void {
        $this->actingAs(seededTenantCompany(), 'company')
            ->post(tenantUrl('/admin/users'), [
                'name'                  => 'Jane Doe',
                'email'                 => 'jane@acme.com',
                'password'              => 'User@1234',
                'password_confirmation' => 'User@1234',
            ])
            ->assertRedirect(tenantRoute('tenant.admin.users.index'));

        expect(session('success'))->toBe('User created successfully.');
    });

    test('new user is persisted with encrypted fields and sensible defaults', function (): void {
        $this->actingAs(seededTenantCompany(), 'company')
            ->post(tenantUrl('/admin/users'), [
                'name'                  => 'Jane Doe',
                'email'                 => 'jane@acme.com',
                'password'              => 'User@1234',
                'password_confirmation' => 'User@1234',
            ]);

        $row = DB::connection('tenant')->table('users')->where('email_hash', hash('sha256', 'jane@acme.com'))->first();

        expect($row)->not->toBeNull()
            ->and(decrypt($row->name))->toBe('Jane Doe')
            ->and($row->name_hash)->toBe(hash('sha256', 'jane doe'))
            ->and(decrypt($row->email))->toBe('jane@acme.com')
            ->and(Hash::check('User@1234', $row->password))->toBeTrue()
            ->and((bool) $row->is_active)->toBeTrue()
            ->and($row->email_verified_at)->not->toBeNull();
    });

    test('name is required', function (): void {
        $this->actingAs(seededTenantCompany(), 'company')
            ->post(tenantUrl('/admin/users'), [
                'email'                 => 'jane@acme.com',
                'password'              => 'User@1234',
                'password_confirmation' => 'User@1234',
            ])
            ->assertSessionHasErrors(['name']);
    });

    test('email is required', function (): void {
        $this->actingAs(seededTenantCompany(), 'company')
            ->post(tenantUrl('/admin/users'), [
                'name'                  => 'Jane Doe',
                'password'              => 'User@1234',
                'password_confirmation' => 'User@1234',
            ])
            ->assertSessionHasErrors(['email']);
    });

    test('password is required', function (): void {
        $this->actingAs(seededTenantCompany(), 'company')
            ->post(tenantUrl('/admin/users'), [
                'name'  => 'Jane Doe',
                'email' => 'jane@acme.com',
            ])
            ->assertSessionHasErrors(['password']);
    });

    test('duplicate email is rejected', function (): void {
        makeTenantUser(['email' => 'jane@acme.com']);

        $this->actingAs(seededTenantCompany(), 'company')
            ->post(tenantUrl('/admin/users'), [
                'name'                  => 'Jane Doe',
                'email'                 => 'jane@acme.com',
                'password'              => 'User@1234',
                'password_confirmation' => 'User@1234',
            ])
            ->assertSessionHasErrors(['email']);
    });

    test('invalid email format is rejected', function (): void {
        $this->actingAs(seededTenantCompany(), 'company')
            ->post(tenantUrl('/admin/users'), [
                'name'                  => 'Jane Doe',
                'email'                 => 'not-an-email',
                'password'              => 'User@1234',
                'password_confirmation' => 'User@1234',
            ])
            ->assertSessionHasErrors(['email']);
    });

    test('password confirmation mismatch is rejected', function (): void {
        $this->actingAs(seededTenantCompany(), 'company')
            ->post(tenantUrl('/admin/users'), [
                'name'                  => 'Jane Doe',
                'email'                 => 'jane@acme.com',
                'password'              => 'User@1234',
                'password_confirmation' => 'Different@1',
            ])
            ->assertSessionHasErrors(['password']);
    });

    test('weak password is rejected', function (string $password): void {
        $this->actingAs(seededTenantCompany(), 'company')
            ->post(tenantUrl('/admin/users'), [
                'name'                  => 'Jane Doe',
                'email'                 => 'jane@acme.com',
                'password'              => $password,
                'password_confirmation' => $password,
            ])
            ->assertSessionHasErrors(['password']);
    })->with([
        'too short'    => 'Ab@1',
        'too long'     => 'Abcdefg@123456789',
        'no uppercase' => 'abcdefg@1',
        'no number'    => 'Abcdefg@',
        'no symbol'    => 'Abcdefg1',
    ]);
});


// ─── Group 3: Update User ─────────────────────────────────────────────────────

describe('update user', function (): void {
    test('edit form renders for authenticated admin', function (): void {
        $user = makeTenantUser();

        $this->actingAs(seededTenantCompany(), 'company')
            ->get(tenantUrl('/admin/users/' . $user->id . '/edit'))
            ->assertStatus(200);
    });

    test('guest is redirected from edit form', function (): void {
        $user = makeTenantUser();

        $this->get(tenantUrl('/admin/users/' . $user->id . '/edit'))
            ->assertRedirect(tenantRoute('tenant.admin.login'));
    });

    test('user is updated and redirects with success', function (): void {
        $user = makeTenantUser();

        $this->actingAs(seededTenantCompany(), 'company')
            ->put(tenantUrl('/admin/users/' . $user->id), [
                'name'      => 'Updated Name',
                'email'     => 'updated@acme.com',
                'is_active' => true,
            ])
            ->assertRedirect(tenantRoute('tenant.admin.users.index'));

        expect(session('success'))->toBe('User updated successfully.');
    });

    test('updated fields are persisted correctly', function (): void {
        $user = makeTenantUser();

        $this->actingAs(seededTenantCompany(), 'company')
            ->put(tenantUrl('/admin/users/' . $user->id), [
                'name'      => 'Updated Name',
                'email'     => 'updated@acme.com',
                'is_active' => false,
            ]);

        $row = DB::connection('tenant')->table('users')->where('id', $user->id)->first();

        expect(decrypt($row->name))->toBe('Updated Name')
            ->and(decrypt($row->email))->toBe('updated@acme.com')
            ->and($row->email_hash)->toBe(hash('sha256', 'updated@acme.com'))
            ->and((bool) $row->is_active)->toBeFalse();
    });

    test('password is updated when provided', function (): void {
        $user = makeTenantUser();

        $this->actingAs(seededTenantCompany(), 'company')
            ->put(tenantUrl('/admin/users/' . $user->id), [
                'name'                  => 'Updated Name',
                'email'                 => 'updated@acme.com',
                'password'              => 'NewUser@1',
                'password_confirmation' => 'NewUser@1',
            ]);

        expect(Hash::check('NewUser@1', $user->fresh()->password))->toBeTrue();
    });

    test('password is unchanged when left empty', function (): void {
        $user = makeTenantUser();

        $this->actingAs(seededTenantCompany(), 'company')
            ->put(tenantUrl('/admin/users/' . $user->id), [
                'name'      => 'Updated Name',
                'email'     => 'updated@acme.com',
                'is_active' => true,
            ]);

        expect(Hash::check('User@1234', $user->fresh()->password))->toBeTrue();
    });

    test('name is required', function (): void {
        $user = makeTenantUser();

        $this->actingAs(seededTenantCompany(), 'company')
            ->put(tenantUrl('/admin/users/' . $user->id), [
                'email' => 'updated@acme.com',
            ])
            ->assertSessionHasErrors(['name']);
    });

    test('email is required', function (): void {
        $user = makeTenantUser();

        $this->actingAs(seededTenantCompany(), 'company')
            ->put(tenantUrl('/admin/users/' . $user->id), [
                'name' => 'Updated Name',
            ])
            ->assertSessionHasErrors(['email']);
    });

    test('invalid email format is rejected', function (): void {
        $user = makeTenantUser();

        $this->actingAs(seededTenantCompany(), 'company')
            ->put(tenantUrl('/admin/users/' . $user->id), [
                'name'  => 'Updated Name',
                'email' => 'not-an-email',
            ])
            ->assertSessionHasErrors(['email']);
    });

    test('email already taken by another user is rejected', function (): void {
        makeTenantUser(['email' => 'user1@acme.com']);
        $user2 = makeTenantUser(['email' => 'user2@acme.com']);

        $this->actingAs(seededTenantCompany(), 'company')
            ->put(tenantUrl('/admin/users/' . $user2->id), [
                'name'  => 'User Two',
                'email' => 'user1@acme.com',
            ])
            ->assertSessionHasErrors(['email']);
    });

    test('weak password is rejected', function (string $password): void {
        $user = makeTenantUser();

        $this->actingAs(seededTenantCompany(), 'company')
            ->put(tenantUrl('/admin/users/' . $user->id), [
                'name'                  => 'Updated Name',
                'email'                 => 'updated@acme.com',
                'password'              => $password,
                'password_confirmation' => $password,
            ])
            ->assertSessionHasErrors(['password']);
    })->with([
        'too short'    => 'Ab@1',
        'too long'     => 'Abcdefg@123456789',
        'no uppercase' => 'abcdefg@1',
        'no number'    => 'Abcdefg@',
        'no symbol'    => 'Abcdefg1',
    ]);

    test('keeping own email on update is allowed', function (): void {
        $user = makeTenantUser(['email' => 'john@acme.com']);

        $this->actingAs(seededTenantCompany(), 'company')
            ->put(tenantUrl('/admin/users/' . $user->id), [
                'name'      => 'Updated Name',
                'email'     => 'john@acme.com',
                'is_active' => true,
            ])
            ->assertRedirect(tenantRoute('tenant.admin.users.index'));
    });
});


// ─── Group 4: Delete User (soft-delete / archive) ─────────────────────────────

describe('delete user', function (): void {
    test('soft-deletes the user and returns success json', function (): void {
        $user = makeTenantUser();

        $this->actingAs(seededTenantCompany(), 'company')
            ->deleteJson(tenantUrl('/admin/users/' . $user->id))
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'User archived successfully.',
            ]);
    });

    test('the user is moved to archive but the row remains', function (): void {
        $user = makeTenantUser();

        $this->actingAs(seededTenantCompany(), 'company')
            ->deleteJson(tenantUrl('/admin/users/' . $user->id));

        expect(TenantUser::onlyTrashed()->whereKey($user->id)->exists())->toBeTrue();
    });

    test('guest cannot delete a user', function (): void {
        $user = makeTenantUser();

        $this->deleteJson(tenantUrl('/admin/users/' . $user->id))
            ->assertUnauthorized();

        expect(TenantUser::find($user->id)->trashed())->toBeFalse();
    });
});


// ─── Group 5: Restore & Force Delete ──────────────────────────────────────────

describe('restore user', function (): void {
    test('restores a soft-deleted user and returns success json', function (): void {
        $user = makeTenantUser();
        $user->delete();

        $this->actingAs(seededTenantCompany(), 'company')
            ->patchJson(tenantUrl('/admin/users/' . $user->id . '/restore'))
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'User has been restored successfully.',
            ]);

        expect(TenantUser::find($user->id))->not->toBeNull();
    });

    test('guest cannot restore a user', function (): void {
        $user = makeTenantUser();
        $user->delete();

        $this->patchJson(tenantUrl('/admin/users/' . $user->id . '/restore'))
            ->assertUnauthorized();

        expect(TenantUser::withTrashed()->find($user->id)->trashed())->toBeTrue();
    });
});

describe('force delete user', function (): void {
    test('permanently deletes a soft-deleted user and returns success json', function (): void {
        $user = makeTenantUser();
        $user->delete();

        $this->actingAs(seededTenantCompany(), 'company')
            ->deleteJson(tenantUrl('/admin/users/' . $user->id . '/force-delete'))
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'User permanently deleted.',
            ]);

        expect(TenantUser::withTrashed()->find($user->id))->toBeNull();
    });

    test('guest cannot force delete a user', function (): void {
        $user = makeTenantUser();
        $user->delete();

        $this->deleteJson(tenantUrl('/admin/users/' . $user->id . '/force-delete'))
            ->assertUnauthorized();

        expect(TenantUser::withTrashed()->find($user->id))->not->toBeNull();
    });
});


// ─── Group 6: Bulk Delete ─────────────────────────────────────────────────────

describe('bulk delete users', function (): void {
    test('soft-deletes the selected users and returns the archived count', function (): void {
        $user1 = makeTenantUser(['email' => 'user1@acme.com']);
        $user2 = makeTenantUser(['email' => 'user2@acme.com']);

        $this->actingAs(seededTenantCompany(), 'company')
            ->deleteJson(tenantUrl('/admin/users/bulk-delete'), [
                'ids' => [$user1->id, $user2->id],
            ])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => '2 user(s) archived successfully.',
            ]);

        expect(TenantUser::withTrashed()->find($user1->id)->trashed())->toBeTrue()
            ->and(TenantUser::withTrashed()->find($user2->id)->trashed())->toBeTrue();
    });

    test('ids is required', function (): void {
        $this->actingAs(seededTenantCompany(), 'company')
            ->deleteJson(tenantUrl('/admin/users/bulk-delete'), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['ids']);
    });

    test('ids must reference existing users', function (): void {
        $this->actingAs(seededTenantCompany(), 'company')
            ->deleteJson(tenantUrl('/admin/users/bulk-delete'), [
                'ids' => ['00000000-0000-0000-0000-000000000000'],
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['ids.0']);
    });

    test('guest cannot bulk delete users', function (): void {
        $user = makeTenantUser();

        $this->deleteJson(tenantUrl('/admin/users/bulk-delete'), [
            'ids' => [$user->id],
        ])->assertUnauthorized();

        expect(TenantUser::find($user->id)->trashed())->toBeFalse();
    });
});
