<?php

namespace App\Services\Tenant\Auth;

use App\Models\Tenant\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class TenantPasswordService
{
    /**
     * Send a password reset link to the given email.
     *
     * @param string $email
     * @return string  Password broker status constant
     */
    public function sendResetLink(string $email): string
    {
        try {
            return Password::broker('tenant_users')->sendResetLink(['email' => $email]);
        } catch (\Exception $e) {
            Log::error('TenantPasswordService::sendResetLink', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to send password reset link. Please try again.');
        }
    }

    /**
     * Reset the tenant user's password via the broker.
     *
     * @param array $data  Must include email, password, password_confirmation, token
     * @return string  Password broker status constant
     */
    public function resetPassword(array $data): string
    {
        try {
            return Password::broker('tenant_users')->reset(
                $data,
                function (User $user) use ($data) {
                    $user->forceFill([
                        'password'       => Hash::make($data['password']),
                        'remember_token' => Str::random(60),
                    ])->save();

                    event(new PasswordReset($user));
                }
            );
        } catch (\Exception $e) {
            Log::error('TenantPasswordService::resetPassword', ['error' => $e->getMessage()]);
            throw new \Exception('Failed to reset password. Please try again.');
        }
    }
}
