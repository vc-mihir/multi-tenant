<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    use WithoutModelEvents;
    /**
     * Create the SuperAdmin role and seed the default admin user.
     *
     * @return void
     */
    public function run(): void
    {
        $role = Role::firstOrCreate(['name' => 'SuperAdmin', 'guard_name' => 'admin']);

        $admin = User::firstOrCreate(
            ['email_hash' => hash('sha256', 'admin@system.com')],
            [
                'name'     => 'Super Admin',
                'email'    => 'admin@system.com',
                'password' => 'Admin@123',
            ]
        );

        $admin->assignRole($role);
    }
}
