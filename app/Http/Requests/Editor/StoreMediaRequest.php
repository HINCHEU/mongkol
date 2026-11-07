<?php

namespace App\Http\Requests\Editor;

use App\Enums\MediaKind;
use App\Services\PlanLimits;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreMediaRequest extends EditorRequest
{
    public const MUSIC_MAX_KB = 8 * 1024;

    public const IMAGE_MAX_KB = 15 * 1024;

    // 50 MP (a 50 MP phone photo) decodes to ~200 MB; docker/php/app.ini allows PHP 512 MB.
    public const IMAGE_MAX_PIXELS = 50_000_000;

    public function rules(): array
    {
        $image = ['image', 'mimes:jpg,jpeg,png,webp', 'max:'.self::IMAGE_MAX_KB, 'dimensions:max_width=12000,max_height=12000'];

        return [
            'kind' => ['required', Rule::in(['cover', 'gallery', 'khqr', 'music'])],
            'file' => ['required', 'file', ...($this->input('kind') === 'music' ? ['mimes:mp3', 'max:'.self::MUSIC_MAX_KB] : $image)],
            'gift_account_id' => ['required_if:kind,khqr', 'prohibited_unless:kind,khqr', 'integer', $this->ownRow('gift_accounts')],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            $limits = PlanLimits::for($this->invitation());

            if ($this->input('kind') === MediaKind::Music->value && ! $limits->allows('own_music')) {
                $validator->errors()->add('file', __('Your own music needs the Premium plan.'));
            }
            if ($this->input('kind') === MediaKind::Gallery->value
                && $this->invitation()->media()->where('kind', 'gallery')->count() >= $limits->galleryPhotos()) {
                $validator->errors()->add('file', __('validation.max.array', ['attribute' => __('validation.attributes.gallery'), 'max' => $limits->galleryPhotos()]));
            }

            // A small file can still decode to a huge bitmap (4 bytes a pixel): stop it before GD runs out of memory.
            $size = $this->input('kind') !== 'music' && $this->file('file')?->isValid() ? @getimagesize($this->file('file')->getRealPath()) : false;
            if ($size && $size[0] * $size[1] > self::IMAGE_MAX_PIXELS) {
                $validator->errors()->add('file', __('This photo is too large (over 50 megapixels). Please send a smaller copy.'));
            }
        }];
    }
}
