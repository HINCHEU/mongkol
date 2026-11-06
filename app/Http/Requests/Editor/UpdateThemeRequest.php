<?php

namespace App\Http\Requests\Editor;

use Illuminate\Validation\Rule;

class UpdateThemeRequest extends EditorRequest
{
    public function rules(): array
    {
        return [
            // shortcut: any active theme, Premium ones included (the trial runs on Premium limits); M6 checks the paid plan.
            'theme_id' => ['required', Rule::exists('themes', 'id')->where('is_active', true)],
        ];
    }
}
