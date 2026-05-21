<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RespondFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'response' => ['required', 'string', 'max:5000'],
            'status' => ['required', 'in:in_progress,resolved,closed'],
        ];
    }
}
