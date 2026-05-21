<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReceivableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:employee_loan,company_loan'],
            'debtor_id' => ['required', 'integer'],
            'principal_amount' => ['required', 'integer', 'min:1'],
            'interest_type' => ['required', 'in:fixed,percentage'],
            'interest_amount' => ['nullable', 'integer', 'min:0'],
            'interest_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'installment_months' => ['required', 'integer', 'min:1'],
            'loan_date' => ['required', 'date'],
            'purpose' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'disbursement_account' => ['required', 'string', 'max:255'],
            'contract_attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }
}
