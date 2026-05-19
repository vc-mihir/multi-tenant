<?php

namespace App\Services\Tenant\User;

use App\Models\Tenant\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

        // Re-authenticate to keep the session alive after an email/password change
        Auth::guard('tenant_user')->login($user);

        if ($emailChanged) {
            $user->sendEmailChangedVerificationNotification();
            return true;
        }

        return false;
    }

    /**
     * Permanently delete the tenant user's account.
     *
     * @param User $user
     * @return void
     */
    public function deleteAccount(User $user): void
    {
        $user->delete();
    }
}
