<?php

namespace App\Http\Requests\Central\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
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
            'company_name' => ['required', 'string', 'max:100', 'unique:companies,company_name'],
            'subdomain' => ['required', 'string', 'max:100', 'unique:companies,subdomain', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'company_email' => [
                'required', 'email', 'lowercase', 'max:100',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $hash = hash('sha256', strtolower((string) $value));
                    if (DB::table('companies')->where('company_email_hash', $hash)->exists()) {
                        $fail('The company email has already been taken.');
                    }
                },
            ],
            'password' => ['required', 'confirmed', Password::min(8)->max(16)->mixedCase()->symbols()],
            'website' => ['required', 'url'],
            'license_number' => [
                'required', 'string', 'max:50',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $hash = hash('sha256', strtolower((string) $value));
                    if (DB::table('companies')->where('license_number_hash', $hash)->exists()) {
                        $fail('The license number has already been taken.');
                    }
                },
            ],
            'address' => ['required', 'string', 'max:500'],
            'country' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
        ];
    }
}
