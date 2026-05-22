<?php

namespace App\Services\Tenant\Auth;

use App\Models\Tenant\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Log;

class TenantEmailVerificationService
{
    /**
     * Send the email verification notification to the user.
     *
     * @param User $user
     * @return bool  false if already verified, true if notification was sent
     */
    public function sendVerification(User $user): bool
    {
        try {
            if ($user->hasVerifiedEmail()) {
                return false;
            }

            $user->sendEmailVerificationNotification();

            return true;
        } catch (\Exception $e) {
            Log::error('TenantEmailVerificationService::sendVerification', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            throw new \Exception('Failed to send verification email. Please try again.');
        }
    }

    /**
     * Mark the user's email as verified and fire the Verified event.
     *
     * @param User $user
     * @return void
     */
    public function verify(User $user): void
    {
        try {
            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
            }
        } catch (\Exception $e) {
            Log::error('TenantEmailVerificationService::verify', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            throw new \Exception('Failed to verify email. Please try again.');
        }
    }
}
