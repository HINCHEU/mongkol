<?php

namespace App\Http\Requests\Editor;

class UpdateVenuesRequest extends EditorRequest
{
    public function rules(): array
    {
        return [
            'venues' => ['present', 'array', 'max:5'],
            'venues.*.id' => ['nullable', 'integer', $this->ownRow('venues')],
            'venues.*.name' => ['required', 'string', 'max:150'],
            'venues.*.address' => ['nullable', 'string', 'max:255'],
            'venues.*.maps_url' => ['nullable', 'url:https', 'max:2048'],
        ];
    }
}
