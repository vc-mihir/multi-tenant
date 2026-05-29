<?php

namespace App\Http\Requests\Tenant\User;

use App\Models\Tenant\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('tenant_user')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = Auth::guard('tenant_user')->user();

        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', function ($attribute, $value, $fail) use ($user) {
                $taken = User::whereNull('deleted_at')
                    ->where('email_hash', hash('sha256', strtolower($value)))
                    ->where('id', '!=', $user->id)
                    ->exists();
                if ($taken) {
                    $fail('The email address is already taken.');
                }
            }],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ];
    }
}
