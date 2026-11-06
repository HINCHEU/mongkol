<?php

namespace App\Http\Requests\Editor;

use App\Services\PlanLimits;
use Illuminate\Validation\Validator;

class UpdatePhotosRequest extends EditorRequest
{
    public function rules(): array
    {
        return [
            'video_url' => ['nullable', 'url:https', 'max:255', 'regex:~^https://(www\.|m\.)?(youtube\.com|youtu\.be)/~'],
            'gallery' => ['sometimes', 'array'],
            'gallery.*' => ['integer', $this->ownRow('media')->where('kind', 'gallery')],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            if (filled($this->input('video_url')) && ! PlanLimits::for($this->invitation())->allows('video')) {
                $validator->errors()->add('video_url', __('The video needs the Premium plan.'));
            }
        }];
    }
}
