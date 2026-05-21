<?php

namespace App\Http\Requests;

class UpdateLoanRequest extends StoreLoanRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        unset($rules['loan_number'], $rules['bank_account_id']);
        $rules['remove_attachment'] = ['nullable', 'boolean'];

        return $rules;
    }
}
