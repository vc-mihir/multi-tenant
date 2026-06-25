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
                'is_active'         => (bool) ($data['is_active'] ?? true),
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
            'name'      => $data['name'],
            'email'     => $data['email'],
            'is_active' => (bool) ($data['is_active'] ?? false),
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
     * Soft-delete a tenant user (archived; can be restored later).
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
            throw new Exception('Failed to archive user. Please try again.');
        }
    }

    /**
     * Restore a soft-deleted tenant user.
     *
     * @param User $user
     * @return void
     */
    public function restoreUser(User $user): void
    {
        try {
            $user->restore();

            activity()
                ->causedBy(Auth::guard('company')->user())
                ->performedOn($user)
                ->event('restored')
                ->withProperties(['user_id' => $user->id])
                ->log('User restored from archive');
        } catch (Exception $e) {
            Log::error('UserService::restoreUser', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            throw new Exception('Failed to restore user. Please try again.');
        }
    }

    /**
     * Permanently delete a soft-deleted tenant user.
     *
     * @param User $user
     * @return void
     */
    public function forceDeleteUser(User $user): void
    {
        try {
            $user->forceDelete();

            activity()
                ->causedBy(Auth::guard('company')->user())
                ->event('force_deleted')
                ->withProperties(['user_id' => $user->id])
                ->log('User permanently deleted');
        } catch (Exception $e) {
            Log::error('UserService::forceDeleteUser', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            throw new Exception('Failed to permanently delete user. Please try again.');
        }
    }

    /**
     * Bulk soft-delete tenant users by IDs.
     *
     * @param array $ids
     * @return int  number of archived records
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
                ->withProperties(['archived_ids' => $ids])
                ->log('Bulk users archived');

            return $deleted;
        } catch (Exception $e) {
            Log::error('UserService::bulkDeleteUsers', [
                'ids'   => $ids,
                'error' => $e->getMessage(),
            ]);
            throw new Exception('Failed to bulk archive users. Please try again.');
        }
    }

    /**
     * Bulk restore soft-deleted tenant users by IDs.
     *
     * @param array $ids
     * @return int  number of restored records
     */
    public function bulkRestoreUsers(array $ids): int
    {
        try {
            $users    = User::onlyTrashed()->whereIn('id', $ids)->get();
            $restored = 0;

            foreach ($users as $user) {
                try {
                    $this->restoreUser($user);
                    $restored++;
                } catch (Exception $e) {
                    activity()
                        ->causedBy(Auth::guard('company')->user())
                        ->performedOn($user)
                        ->event('restore_failed')
                        ->withProperties(['error' => $e->getMessage(), 'user_id' => $user->id])
                        ->log('Bulk restore failed for user');
                }
            }

            return $restored;
        } catch (Exception $e) {
            Log::error('UserService::bulkRestoreUsers', [
                'ids'   => $ids,
                'error' => $e->getMessage(),
            ]);
            throw new Exception('Failed to bulk restore users. Please try again.');
        }
    }

    /**
     * Bulk permanently delete soft-deleted tenant users by IDs.
     *
     * @param array $ids
     * @return int  number of permanently deleted records
     * @throws Exception  when the active connection is the central database.
     */
    public function bulkForceDeleteUsers(array $ids): int
    {
        if (DB::getDefaultConnection() === 'mysql') {
            throw new Exception('Security Error: Attempted deletion on central database blocked.');
        }

        try {
            $users   = User::onlyTrashed()->whereIn('id', $ids)->get();
            $deleted = 0;

            foreach ($users as $user) {
                try {
                    $this->forceDeleteUser($user);
                    $deleted++;
                } catch (Exception $e) {
                    activity()
                        ->causedBy(Auth::guard('company')->user())
                        ->performedOn($user)
                        ->event('force_delete_failed')
                        ->withProperties(['error' => $e->getMessage(), 'user_id' => $user->id])
                        ->log('Bulk permanent delete failed for user');
                }
            }

            return $deleted;
        } catch (Exception $e) {
            Log::error('UserService::bulkForceDeleteUsers', [
                'ids'   => $ids,
                'error' => $e->getMessage(),
            ]);
            throw new Exception('Failed to bulk permanently delete users. Please try again.');
        }
    }
}
