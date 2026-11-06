<?php

namespace App\Http\Requests\Editor;

use App\Enums\MediaKind;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreMediaRequest extends EditorRequest
{
    public const MUSIC_MAX_KB = 8 * 1024;

    public const IMAGE_MAX_KB = 15 * 1024;

    public function rules(): array
    {
        $image = ['image', 'mimes:jpg,jpeg,png,webp', 'max:'.self::IMAGE_MAX_KB, 'dimensions:max_width=10000,max_height=10000'];

        return [
            'kind' => ['required', Rule::in(['cover', 'gallery', 'khqr', 'music'])],
            'file' => ['required', 'file', ...($this->input('kind') === 'music' ? ['mimes:mp3', 'max:'.self::MUSIC_MAX_KB] : $image)],
            'gift_account_id' => ['required_if:kind,khqr', 'prohibited_unless:kind,khqr', 'integer', $this->ownRow('gift_accounts')],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            if ($this->input('kind') !== MediaKind::Gallery->value) {
                return;
            }
            // shortcut: plan's photo cap, Premium's 30 during the trial; M6's PlanLimits replaces this.
            $limit = $this->invitation()->plan?->features['gallery_photos'] ?? 30;
            if ($this->invitation()->media()->where('kind', 'gallery')->count() >= $limit) {
                $validator->errors()->add('file', __('validation.max.array', ['attribute' => 'gallery', 'max' => $limit]));
            }
        }];
    }
}
