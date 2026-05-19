<?php

namespace App\Services\Tenant\Auth;

use App\Models\Tenant\User;
use Illuminate\Auth\Events\Verified;

class TenantEmailVerificationService
{
    /**
     * Send the email verification notification to the user.
     *
     * @param User $user
     * @return void
     */
    public function sendVerification(User $user): void
    {
        $user->sendEmailVerificationNotification();
    }

    /**
     * Mark the user's email as verified and fire the Verified event.
     *
     * @param User $user
     * @return void
     */
    public function verify(User $user): void
    {
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }
    }
}
