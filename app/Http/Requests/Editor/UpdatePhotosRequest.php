<?php

namespace App\Http\Requests\Editor;

class UpdatePhotosRequest extends EditorRequest
{
    public function rules(): array
    {
        return [
            // shortcut: open to every plan; M6's PlanLimits makes the video Premium-only.
            'video_url' => ['nullable', 'url:https', 'max:255', 'regex:~^https://(www\.|m\.)?(youtube\.com|youtu\.be)/~'],
            'gallery' => ['sometimes', 'array'],
            'gallery.*' => ['integer', $this->ownRow('media')->where('kind', 'gallery')],
        ];
    }
}
