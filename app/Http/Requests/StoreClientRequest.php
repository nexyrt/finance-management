<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:individual,company'],
            'email' => ['nullable', 'email', 'unique:clients,email'],
            'NPWP' => ['nullable', 'string', 'max:20'],
            'KPP' => ['nullable', 'string', 'max:20'],
            'EFIN' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'in:Active,Inactive'],
            'account_representative' => ['nullable', 'string', 'max:255'],
            'ar_phone_number' => ['nullable', 'string', 'max:20'],
            'person_in_charge' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
        ];
    }
}
