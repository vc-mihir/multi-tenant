<?php

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

beforeEach(function (): void {
    setCentralDomain();
    $this->seed(AdminUserSeeder::class);
});

// ─── Group 1: Settings Page ───────────────────────────────────────────────────

describe('settings page', function (): void {
    test('renders for authenticated admin', function (): void {
        $this->actingAs(seededAdmin(), 'admin')
            ->get('/admin/settings')
            ->assertStatus(200);
    });

    test('guest is redirected to login', function (): void {
        $this->get('/admin/settings')
            ->assertRedirect(route('admin.login'));
    });
});

// ─── Group 2: Successful Update ───────────────────────────────────────────────

describe('successful update', function (): void {
    test('name and email are updated and persisted correctly', function (): void {
        $admin = seededAdmin();

        $this->actingAs($admin, 'admin')->put('/admin/settings', [
            'name'  => 'Updated Admin',
            'email' => 'updated@system.com',
        ]);

        $row = DB::table('users')->where('id', $admin->id)->first();

        expect(decrypt($row->name))->toBe('Updated Admin')
            ->and(decrypt($row->email))->toBe('updated@system.com')
            ->and($row->email_hash)->toBe(hash('sha256', 'updated@system.com'));
    });

    test('redirects back with success message after update', function (): void {
        $admin = seededAdmin();

        $this->actingAs($admin, 'admin')
            ->put('/admin/settings', [
                'name'  => 'Updated Admin',
                'email' => 'admin@system.com',
            ])
            ->assertRedirect();

        expect(session('success'))->toBe('Profile updated successfully.');
    });

    test('password is updated when provided', function (): void {
        $admin = seededAdmin();

        $this->actingAs($admin, 'admin')->put('/admin/settings', [
            'name'                  => $admin->name,
            'email'                 => 'admin@system.com',
            'password'              => 'NewPass@1',
            'password_confirmation' => 'NewPass@1',
        ]);

        expect(Hash::check('NewPass@1', $admin->fresh()->password))->toBeTrue();
    });

    test('password is unchanged when left empty', function (): void {
        $admin = seededAdmin();

        $this->actingAs($admin, 'admin')->put('/admin/settings', [
            'name'  => $admin->name,
            'email' => 'admin@system.com',
        ]);

        expect(Hash::check('Admin@123', $admin->fresh()->password))->toBeTrue();
    });
});

// ─── Group 3: Validation ──────────────────────────────────────────────────────

describe('validation', function (): void {
    test('name is required', function (): void {
        $admin = seededAdmin();

        $this->actingAs($admin, 'admin')
            ->put('/admin/settings', ['email' => 'admin@system.com'])
            ->assertSessionHasErrors(['name']);
    });

    test('email is required', function (): void {
        $admin = seededAdmin();

        $this->actingAs($admin, 'admin')
            ->put('/admin/settings', ['name' => 'Admin'])
            ->assertSessionHasErrors(['email']);
    });

    test('invalid email format is rejected', function (): void {
        $admin = seededAdmin();

        $this->actingAs($admin, 'admin')
            ->put('/admin/settings', ['name' => 'Admin', 'email' => 'not-an-email'])
            ->assertSessionHasErrors(['email']);
    });

    test('email already taken by another user is rejected', function (): void {
        $admin = seededAdmin();

        User::create([
            'name'     => 'Other User',
            'email'    => 'other@system.com',
            'password' => 'Admin@123',
        ]);

        $this->actingAs($admin, 'admin')
            ->put('/admin/settings', ['name' => 'Admin', 'email' => 'other@system.com'])
            ->assertSessionHasErrors(['email']);
    });

    test('password confirmation mismatch is rejected', function (): void {
        $admin = seededAdmin();

        $this->actingAs($admin, 'admin')
            ->put('/admin/settings', [
                'name'                  => $admin->name,
                'email'                 => 'admin@system.com',
                'password'              => 'NewPass@1',
                'password_confirmation' => 'Different@1',
            ])
            ->assertSessionHasErrors(['password']);
    });

    test('weak password is rejected', function (string $password): void {
        $admin = seededAdmin();

        $this->actingAs($admin, 'admin')
            ->put('/admin/settings', [
                'name'                  => $admin->name,
                'email'                 => 'admin@system.com',
                'password'              => $password,
                'password_confirmation' => $password,
            ])
            ->assertSessionHasErrors(['password']);
    })->with([
        'too short'    => 'Ab@1',
        'no uppercase' => 'newpass@1',
        'no number'    => 'NewPass@!',
        'no symbol'    => 'NewPass12',
        'too long'     => 'NewPass@123456789',
    ]);
});
