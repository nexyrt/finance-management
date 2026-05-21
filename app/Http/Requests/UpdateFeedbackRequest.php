<?php

namespace App\Http\Requests;

class UpdateFeedbackRequest extends StoreFeedbackRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        unset($rules['page_url'], $rules['attachment']);

        return $rules;
    }
}
