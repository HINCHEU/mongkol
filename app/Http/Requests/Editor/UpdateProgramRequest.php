<?php

namespace App\Http\Requests\Editor;

use Illuminate\Validation\Rule;

class UpdateProgramRequest extends EditorRequest
{
    public function rules(): array
    {
        return [
            'wedding_date' => ['required', 'date_format:Y-m-d'],
            'events' => ['present', 'array', 'max:20'],
            'events.*.id' => ['nullable', 'integer', $this->ownRow('events')],
            'events.*.preset_key' => ['nullable', Rule::exists('event_presets', 'key')],
            'events.*.title_km' => ['required', 'string', 'max:150'],
            'events.*.title_en' => ['nullable', 'string', 'max:150'],
            // Entered as Phnom Penh wall-clock time; stored in UTC.
            'events.*.date' => ['required', 'date_format:Y-m-d'],
            'events.*.time' => ['required', 'date_format:H:i'],
            'events.*.venue_id' => ['nullable', 'integer', $this->ownRow('venues')],
            'events.*.note' => ['nullable', 'string', 'max:200'],
        ];
    }
}
