<?php

namespace App\Http\Requests;

class UpdateReimbursementRequest extends StoreReimbursementRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'remove_attachment' => ['nullable', 'boolean'],
        ]);
    }
}
