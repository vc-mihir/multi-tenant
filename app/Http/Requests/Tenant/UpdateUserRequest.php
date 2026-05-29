<?php

namespace App\Http\Requests\Tenant;

use App\Models\Tenant\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', function ($attribute, $value, $fail) {
                $taken = User::whereNull('deleted_at')
                    ->where('email_hash', hash('sha256', strtolower($value)))
                    ->where('id', '!=', $this->route('user'))
                    ->exists();
                if ($taken) {
                    $fail('The email address is already taken.');
                }
            }],
            'password'  => ['nullable', 'confirmed', Password::min(8)->max(16)->letters()->mixedCase()->numbers()->symbols()],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
