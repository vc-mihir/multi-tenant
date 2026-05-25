<?php

namespace App\Http\Requests\Central\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BulkDeleteCompaniesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['string', 'uuid', 'exists:companies,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required' => 'No companies selected.',
            'ids.min'      => 'No companies selected.',
        ];
    }
}
