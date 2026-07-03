<?php

namespace App\Http\Requests;

use App\Models\BankTransaction;
use Illuminate\Foundation\Http\FormRequest;

class StoreBankTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $type = $this->input('transaction_type');

        // Defer an invalid/missing type to validation (422) rather than a 403.
        if (! in_array($type, ['credit', 'debit'], true)) {
            return true;
        }

        return $this->user()?->can('create '.BankTransaction::featureForType($type)) ?? false;
    }

    public function rules(): array
    {
        return [
            'bank_account_id' => ['required', 'exists:bank_accounts,id'],
            'category_id' => ['required', 'exists:transaction_categories,id'],
            'amount' => ['required', 'integer', 'min:1'],
            'transaction_date' => ['required', 'date'],
            'transaction_type' => ['required', 'in:credit,debit'],
            'description' => ['required', 'string', 'max:255'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }
}
