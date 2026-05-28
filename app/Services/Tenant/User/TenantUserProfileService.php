<?php

namespace App\Services\Tenant\User;

use App\Models\Tenant\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class TenantUserProfileService
{
    /**
     * Update the tenant user's profile details.
     *
     * @param User $user
     * @param array $data
     * @return void
     */
    public function update(User $user, array $data): bool
    {
        try {
            $emailChanged = $user->email !== $data['email'];

            $user->name  = $data['name'];
            $user->email = $data['email'];

            if ($emailChanged) {
                $user->email_verified_at = null;
            }

            if (!empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }

            $user->save();

            Auth::guard('tenant_user')->login($user);

            if ($emailChanged) {
                $user->sendEmailChangedVerificationNotification();
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('TenantUserProfileService::update', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            throw new \Exception('Failed to update profile. Please try again.');
        }
    }

    /**
     * Soft-delete the tenant user's own account and log them out.
     *
     * @param User $user
     * @return void
     */
    public function deleteAccount(User $user): void
    {
        try {
            $user->delete();
        } catch (\Exception $e) {
            Log::error('TenantUserProfileService::deleteAccount', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            throw new \Exception('Failed to delete account. Please try again.');
        }

        Auth::guard('tenant_user')->logout();
        request()->session()->regenerate();
    }
}
