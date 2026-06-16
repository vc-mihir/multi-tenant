<?php

use App\Models\User;
use Database\Seeders\AdminUserSeeder;

beforeEach(function (): void {
    setCentralDomain();
    $this->seed(AdminUserSeeder::class);
});


// ─── Group 1: Login Page ──────────────────────────────────────────────────────

describe('login page', function (): void {
    test('renders successfully for guests', function (): void {
        expect($this->get('/admin/login')->status())->toBe(200);
    });

    test('authenticated admin is redirected away from login page', function (): void {
        $admin = seededAdmin();

        $this->actingAs($admin, 'admin')
            ->get('/admin/login')
            ->assertRedirect(route('admin.dashboard'));
    });
});

// ─── Group 2: Successful Login ────────────────────────────────────────────────

describe('successful login', function (): void {
    test('valid credentials authenticate the admin and redirect to dashboard', function (): void {
        $response = $this->from('/admin/login')->post('/admin/login', [
            'email'    => 'admin@system.com',
            'password' => 'Admin@123',
        ]);

        expect(auth('admin')->check())->toBeTrue();

        $response->assertRedirect(route('admin.dashboard'));
    });
});

// ─── Group 3: Validation ──────────────────────────────────────────────────────

describe('validation', function (): void {
    test('empty form submission returns errors for email and password', function (): void {
        $response = $this->from('/admin/login')->post('/admin/login', []);

        $response->assertSessionHasErrors(['email', 'password']);
    });

    test('invalid email format is rejected', function (): void {
        $response = $this->from('/admin/login')->post('/admin/login', [
            'email'    => 'not-an-email',
            'password' => 'Admin@123',
        ]);

        $response->assertSessionHasErrors(['email']);
    });
});

// ─── Group 4: Failed Login ────────────────────────────────────────────────────

describe('failed login', function (): void {
    test('wrong password redirects back with error message', function (): void {
        $response = $this->from('/admin/login')->post('/admin/login', [
            'email'    => 'admin@system.com',
            'password' => 'Wrong@123',
        ]);

        $response->assertRedirect('/admin/login');
        expect(session('error'))->toBe('Credentials do not match. Please try again.');
    });

    test('wrong email redirects back with error message', function (): void {
        $response = $this->from('/admin/login')->post('/admin/login', [
            'email'    => 'nobody@test.com',
            'password' => 'Admin@123',
        ]);

        $response->assertRedirect('/admin/login');
        expect(session('error'))->toBe('Credentials do not match. Please try again.');
    });

    test('user without SuperAdmin role cannot login', function (): void {
        User::create([
            'name'     => 'Regular User',
            'email'    => 'user@test.com',
            'password' => 'Admin@123',
        ]);

        $response = $this->from('/admin/login')->post('/admin/login', [
            'email'    => 'user@test.com',
            'password' => 'Admin@123',
        ]);

        $response->assertRedirect('/admin/login');
        expect(session('error'))->toBe('Credentials do not match. Please try again.');
    });
});

// ─── Group 5: Dashboard ───────────────────────────────────────────────────────

describe('dashboard', function (): void {
    test('renders for authenticated admin', function (): void {
        $admin = seededAdmin();

        expect(
            $this->actingAs($admin, 'admin')->get('/admin/dashboard')->status()
        )->toBe(200);
    });

    test('guest is redirected to login', function (): void {
        $this->get('/admin/dashboard')->assertRedirect(route('admin.login'));
    });
});

// ─── Group 6: Logout ──────────────────────────────────────────────────────────

describe('logout', function (): void {
    test('authenticated admin is logged out and redirected to login', function (): void {
        $admin = seededAdmin();

        $this->actingAs($admin, 'admin')
            ->post('/admin/logout')
            ->assertRedirect(route('admin.login'));
    });
});
