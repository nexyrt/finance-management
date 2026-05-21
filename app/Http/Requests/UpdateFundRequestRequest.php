<?php

namespace App\Http\Requests;

class UpdateFundRequestRequest extends StoreFundRequestRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        unset($rules['request_number']);
        $rules['remove_attachment'] = ['nullable', 'boolean'];

        return $rules;
    }
}
