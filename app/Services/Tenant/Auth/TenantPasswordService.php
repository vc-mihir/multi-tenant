<?php

namespace App\Services\Tenant\Auth;

use App\Models\Tenant\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TenantPasswordService
{
    /**
     * Update the authenticated tenant user's password.
     *
     * @param User $user
     * @param string $password
     * @return void
     */
    public function updatePassword(User $user, string $password): void
    {
        try {
            $user->update([
                'password' => Hash::make($password),
            ]);
        } catch (\Exception $e) {
            Log::error('TenantPasswordService::updatePassword', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            throw new \Exception('Failed to update password. Please try again.');
        }
    }

    /**
     * Validate the current password for the confirmation flow.
     *
     * @param Request $request
     * @return void
     * @throws ValidationException
     */
    public function confirmPassword(Request $request): void
    {
        try {
            if (! Auth::guard('web')->validate([
                'email'    => $request->user()->email,
                'password' => $request->password,
            ])) {
                throw ValidationException::withMessages([
                    'password' => __('auth.password'),
                ]);
            }

            $request->session()->put('auth.password_confirmed_at', time());
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('TenantPasswordService::confirmPassword', ['error' => $e->getMessage()]);
            throw new \Exception('Failed to confirm password. Please try again.');
        }
    }

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
