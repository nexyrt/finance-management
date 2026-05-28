<?php

namespace App\Http\Requests;

class UpdateMonthlyRequest extends StoreMonthlyRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        unset($rules['template_id']);

        return $rules;
    }
}
