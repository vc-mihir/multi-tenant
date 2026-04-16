<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class CompanyRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:100'],
            'company_email' => ['required', 'email', 'lowercase', 'max:100', 'unique:companies,company_email'],
            'password' => ['required', 'confirmed', Password::min(8)->max(16)->mixedCase()->symbols()],
            'website' => ['required', 'url'],
            'license_number' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:500'],
            'country' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
        ];
    }
}
