<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantUserSeeder extends Seeder
{
    /**
     * Seed 5 demo user records into the active tenant database.
     *
     * Uses email_hash for the firstOrCreate lookup because the email
     * column is encrypted and cannot be matched directly.
     *
     * @return void
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Test - 1', 'email' => 'test1@test.com'],
            ['name' => 'Test - 2', 'email' => 'test2@test.com'],
            ['name' => 'Test - 3', 'email' => 'test3@test.com'],
            ['name' => 'Test - 4', 'email' => 'test4@test.com'],
            ['name' => 'Test - 5', 'email' => 'test5@test.com'],
        ];

        foreach ($users as $userData) {
            $emailHash = hash('sha256', strtolower($userData['email']));

            User::firstOrCreate(
                ['email_hash' => $emailHash],
                [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make('Hello@123'),
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
