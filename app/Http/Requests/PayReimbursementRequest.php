<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayReimbursementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bank_account_id' => ['required', 'exists:bank_accounts,id'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'payment_amount' => ['required', 'integer', 'min:1'],
            'reference_notes' => ['nullable', 'string', 'max:255'],
        ];
    }
}
