<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $adminRole = Role::firstOrCreate(['name' => 'SuperAdmin']);
        Role::firstOrCreate(['name' => 'Company']);

        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@system.com',
            'password' => Hash::make('Admin@123'),
        ]);

        $admin->assignRole($adminRole);
    }
}
