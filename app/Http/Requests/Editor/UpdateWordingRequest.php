<?php

namespace App\Http\Requests\Editor;

use Illuminate\Validation\Rule;

class UpdateWordingRequest extends EditorRequest
{
    public function rules(): array
    {
        return [
            'locale' => ['required', Rule::in(['km', 'en'])],
            'lunar_date' => ['nullable', 'string', 'max:200'],
            'opening' => ['array:km,en'],
            'closing' => ['array:km,en'],
            'thank_you' => ['array:km,en'],
            '*.km' => ['nullable', 'string', 'max:1000'],
            '*.en' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
