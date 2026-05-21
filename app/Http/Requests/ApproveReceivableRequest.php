<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveReceivableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => ['required', 'in:approve,reject'],
            'bank_account_id' => ['required_if:action,approve', 'nullable', 'exists:bank_accounts,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
