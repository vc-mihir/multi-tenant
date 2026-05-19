<?php

namespace App\Services\Tenant\Auth;

use App\Models\Tenant\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        $user->update([
            'password' => Hash::make($password),
        ]);
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
        if (! Auth::guard('web')->validate([
            'email'    => $request->user()->email,
            'password' => $request->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());
    }

    /**
     * Send a password reset link to the given email.
     *
     * @param string $email
     * @return string  Password broker status constant
     */
    public function sendResetLink(string $email): string
    {
        return Password::broker('tenant_users')->sendResetLink(['email' => $email]);
    }

    /**
     * Reset the tenant user's password via the broker.
     *
     * @param array $data  Must include email, password, password_confirmation, token
     * @return string  Password broker status constant
     */
    public function resetPassword(array $data): string
    {
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
    }
}
