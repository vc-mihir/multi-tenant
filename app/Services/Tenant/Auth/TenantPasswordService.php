<?php

namespace App\Services\Tenant\Auth;

use App\Models\Tenant\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
            $emailHash = hash('sha256', strtolower($email));

            $user = User::where('email_hash', $emailHash)->first();

            if (! $user) {
                return PasswordBroker::INVALID_USER;
            }

            $recent = DB::table('password_reset_tokens')
                ->where('email_hash', $emailHash)
                ->where('created_at', '>=', now()->subSeconds(60))
                ->exists();

            if ($recent) {
                return PasswordBroker::RESET_THROTTLED;
            }

            $token = Str::random(64);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email_hash' => $emailHash],
                ['token' => Hash::make($token), 'created_at' => now()]
            );

            $user->sendPasswordResetNotification($token);

            return PasswordBroker::RESET_LINK_SENT;
        } catch (\Exception $e) {
            Log::error('TenantPasswordService::sendResetLink', [
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to send password reset link. Please try again.');
        }
    }

    /**
     * Reset the tenant user's password.
     *
     * @param array $data  Must include email, password, password_confirmation, token
     * @return string  Password broker status constant
     */
    public function resetPassword(array $data): string
    {
        try {
            $emailHash = hash('sha256', strtolower($data['email']));

            $user = User::where('email_hash', $emailHash)->first();

            if (! $user) {
                return PasswordBroker::INVALID_USER;
            }

            $record = DB::table('password_reset_tokens')
                ->where('email_hash', $emailHash)
                ->first();

            if (! $record
                || ! Hash::check($data['token'], $record->token)
                || Carbon::parse($record->created_at)->lt(now()->subMinutes(60))
            ) {
                return PasswordBroker::INVALID_TOKEN;
            }

            $user->forceFill([
                'password'       => Hash::make($data['password']),
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));

            DB::table('password_reset_tokens')->where('email_hash', $emailHash)->delete();

            return PasswordBroker::PASSWORD_RESET;
        } catch (\Exception $e) {
            Log::error('TenantPasswordService::resetPassword', ['error' => $e->getMessage()]);
            throw new \Exception('Failed to reset password. Please try again.');
        }
    }
}
