<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewReimbursementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => ['required', 'in:approve,reject'],
            'review_notes' => ['nullable', 'string', 'max:500'],
            'category_id' => ['required_if:action,approve', 'nullable', 'exists:transaction_categories,id'],
        ];
    }
}
