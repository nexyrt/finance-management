<?php

namespace App\Http\Requests;

use App\Models\TransactionCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:income,expense,financing,transfer,adjustment'],
            'pl_group' => ['nullable', Rule::in(TransactionCategory::PL_GROUPS)],
            'label' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:transaction_categories,id'],
        ];
    }
}
