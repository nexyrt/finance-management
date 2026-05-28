<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayReceivableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'principal_paid' => ['nullable', 'integer', 'min:0'],
            'interest_paid' => ['nullable', 'integer', 'min:0'],
            'payment_method' => ['required', 'in:cash,payroll_deduction,bank_transfer'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
