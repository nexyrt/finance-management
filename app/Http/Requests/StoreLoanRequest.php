<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'loan_number' => ['required', 'string', 'unique:loans,loan_number'],
            'lender_name' => ['required', 'string', 'max:255'],
            'principal_amount' => ['required', 'integer', 'min:1'],
            'interest_type' => ['required', 'in:fixed,percentage'],
            'interest_amount' => ['nullable', 'integer', 'min:0'],
            'interest_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'term_months' => ['required', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'maturity_date' => ['required', 'date', 'after:start_date'],
            'purpose' => ['nullable', 'string'],
            'contract_attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'bank_account_id' => ['required', 'exists:bank_accounts,id'],
        ];
    }
}
