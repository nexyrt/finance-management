<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:income,expense,adjustment,transfer'],
            'label' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:transaction_categories,id'],
        ];
    }
}
