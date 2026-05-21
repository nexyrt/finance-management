<?php

namespace App\Http\Requests;

class UpdatePaymentRequest extends StorePaymentRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'remove_attachment' => ['nullable', 'boolean'],
        ]);
    }
}
