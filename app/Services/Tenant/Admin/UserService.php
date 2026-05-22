<?php

namespace App\Services\Tenant\Admin;

use App\Models\Tenant\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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
        try {
            return User::create([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'password'          => Hash::make($data['password']),
                'email_verified_at' => now(),
            ]);
        } catch (Exception $e) {
            Log::error('UserService::createUser', [
                'email' => $data['email'],
                'error' => $e->getMessage(),
            ]);
            throw new Exception('Failed to create user. Please try again.');
        }
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

        try {
            $user->update($update);
        } catch (Exception $e) {
            Log::error('UserService::updateUser', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            throw new Exception('Failed to update user. Please try again.');
        }
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

        try {
            $user->delete();
        } catch (Exception $e) {
            Log::error('UserService::deleteUser', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            throw new Exception('Failed to delete user. Please try again.');
        }
    }

    /**
     * Bulk delete tenant users by IDs.
     *
     * @param array $ids
     * @return int  number of deleted records
     * @throws Exception  when the active connection is the central database.
     */
    public function bulkDeleteUsers(array $ids): int
    {
        if (DB::getDefaultConnection() === 'mysql') {
            throw new Exception('Security Error: Attempted deletion on central database blocked.');
        }

        try {
            $deleted = User::whereIn('id', $ids)->delete();

            activity()
                ->causedBy(Auth::guard('company')->user())
                ->event('deleted')
                ->withProperties(['deleted_ids' => $ids])
                ->log('Bulk users deleted');

            return $deleted;
        } catch (Exception $e) {
            Log::error('UserService::bulkDeleteUsers', [
                'ids'   => $ids,
                'error' => $e->getMessage(),
            ]);
            throw new Exception('Failed to bulk delete users. Please try again.');
        }
    }
}
