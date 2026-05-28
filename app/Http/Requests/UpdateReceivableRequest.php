<?php

namespace App\Http\Requests;

class UpdateReceivableRequest extends StoreReceivableRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'remove_attachment' => ['nullable', 'boolean'],
        ]);
    }
}
