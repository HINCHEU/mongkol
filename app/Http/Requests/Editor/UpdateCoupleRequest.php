<?php

namespace App\Http\Requests\Editor;

class UpdateCoupleRequest extends EditorRequest
{
    public function rules(): array
    {
        return [
            'people' => ['required', 'array:groom,bride,groom_father,groom_mother,bride_father,bride_mother'],
            'people.*.title' => ['nullable', 'string', 'max:50'],
            'people.*.name_km' => ['nullable', 'string', 'max:100'],
            'people.*.name_en' => ['nullable', 'string', 'max:100'],
            'people.*.is_late' => ['boolean'],
            'people.groom.name_km' => ['required', 'string', 'max:100'],
            'people.bride.name_km' => ['required', 'string', 'max:100'],
        ];
    }
}
