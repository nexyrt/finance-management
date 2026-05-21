<?php

namespace App\Http\Requests;

class UpdateBankAccountRequest extends StoreBankAccountRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['account_number'] = ['required', 'string', 'max:255', "unique:bank_accounts,account_number,{$this->route('bankAccount')->id}"];

        return $rules;
    }
}
