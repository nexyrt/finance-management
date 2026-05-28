<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string'],
            'is_pkp' => ['nullable', 'boolean'],
            'npwp' => ['nullable', 'string'],
            'ppn_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'finance_manager_name' => ['required', 'string'],
            'finance_manager_position' => ['required', 'string'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'letter_head' => ['nullable', 'image', 'max:2048'],
            'signature' => ['nullable', 'image', 'max:2048'],
            'stamp' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
