<?php

namespace App\Http\Requests\Editor;

use App\Models\Theme;
use App\Services\PlanLimits;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateThemeRequest extends EditorRequest
{
    public function rules(): array
    {
        return [
            'theme_id' => ['required', Rule::exists('themes', 'id')->where('is_active', true)],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            $premium = Theme::whereKey($this->input('theme_id'))->where('tier', 'premium')->exists();
            if ($premium && ! PlanLimits::for($this->invitation())->allows('premium_themes')) {
                $validator->errors()->add('theme_id', __('Premium themes need the Premium plan.'));
            }
        }];
    }
}
