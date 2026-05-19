<?php

namespace App\Services\Tenant\Admin;

use App\Models\Tenant\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Create a new tenant user (admin-initiated, pre-verified).
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        return User::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'password'          => Hash::make($data['password']),
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Update an existing tenant user's details.
     *
     * @param User $user
     * @param array $data
     * @return void
     */
    public function updateUser(User $user, array $data): void
    {
        $update = [
            'name'  => $data['name'],
            'email' => $data['email'],
        ];

        if (!empty($data['password'])) {
            $update['password'] = Hash::make($data['password']);
        }

        $user->update($update);
    }

    /**
     * Delete a tenant user with a safety check to prevent central DB deletion.
     *
     * @param User $user
     * @return void
     * @throws Exception  when the active connection is the central database.
     */
    public function deleteUser(User $user): void
    {
        if (DB::getDefaultConnection() === 'mysql') {
            throw new Exception('Security Error: Attempted deletion on central database blocked.');
        }

        $user->delete();
    }
}
