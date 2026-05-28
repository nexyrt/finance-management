<?php

namespace App\Http\Requests;

class UpdateClientRequest extends StoreClientRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['email'] = ['nullable', 'email', "unique:clients,email,{$this->route('client')->id}"];

        return $rules;
    }
}
