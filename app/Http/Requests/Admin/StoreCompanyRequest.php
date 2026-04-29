<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('SuperAdmin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:100', 'unique:companies,company_name'],
            'subdomain' => ['required', 'string', 'max:100', 'unique:companies,subdomain', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
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
