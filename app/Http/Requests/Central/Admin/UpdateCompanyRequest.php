<?php

namespace App\Http\Requests\Central\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole('SuperAdmin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('companies', 'company_name')->ignore($this->route('company')),
            ],
            'subdomain' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('companies', 'subdomain')->ignore($this->route('company')),
            ],
            'company_email' => [
                'required',
                'email',
                'lowercase',
                'max:100',
                Rule::unique('companies', 'company_email')->ignore($this->route('company')),
            ],
            'website' => ['required', 'url', 'max:255'],
            'license_number' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:500'],
            'country' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'status' => ['required', Rule::in(['active', 'inactive', 'suspended', 'pending'])],
        ];
    }
}
