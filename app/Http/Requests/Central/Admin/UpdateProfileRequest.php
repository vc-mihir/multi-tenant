<?php

namespace App\Http\Requests\Central\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin')->check() && Auth::guard('admin')->user()->hasRole('SuperAdmin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                function ($attribute, $value, $fail) {
                    $exists = User::where('email_hash', hash('sha256', strtolower($value)))
                        ->where('id', '!=', Auth::id())
                        ->exists();
                    if ($exists) {
                        $fail('The email has already been taken.');
                    }
                },
            ],
            'password' => [
                'nullable', 
                'string', 
                'confirmed',
                Password::min(8)
                    ->max(16)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'password.confirmed' => 'The password confirmation does not match.',
            'password' => 'The password must be 8-16 characters and contain uppercase, lowercase, numbers, and symbols.',
        ];
    }
}
