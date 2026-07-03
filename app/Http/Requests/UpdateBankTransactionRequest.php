<?php

namespace App\Http\Requests;

use App\Models\BankTransaction;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBankTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $transaction = $this->route('bankTransaction');

        if (! $transaction instanceof BankTransaction) {
            return false;
        }

        return $this->user()?->can($transaction->abilityFor('edit')) ?? false;
    }

    public function rules(): array
    {
        return [
            'transaction_date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['nullable', 'integer', 'min:1'],
            'category_id' => ['nullable', 'exists:transaction_categories,id'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'remove_attachment' => ['nullable', 'boolean'],
        ];
    }
}
