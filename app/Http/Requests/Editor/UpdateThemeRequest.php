<?php

namespace App\Http\Requests\Editor;

use App\Models\Theme;
use App\Services\PlanLimits;
use App\Services\Themes\ThemeRegistry;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/** The theme, and (Premium) its options: what the chosen theme's theme.json offers, plus section order and visibility. */
class UpdateThemeRequest extends EditorRequest
{
    public function rules(): array
    {
        $slug = Theme::whereKey($this->input('theme_id'))->value('slug');
        $manifest = $slug ? app(ThemeRegistry::class)->get($slug) ?? [] : [];
        $options = $manifest['options'] ?? [];
        $sections = $manifest['sections'] ?? [];

        $rules = [
            'theme_id' => ['required', Rule::exists('themes', 'id')->where('is_active', true)],
            'theme_settings' => ['sometimes', 'array:'.implode(',', [...array_keys($options), 'order', 'hidden'])],
            'theme_settings.order' => ['sometimes', 'array'],
            'theme_settings.order.*' => [Rule::in($sections)],
            'theme_settings.hidden' => ['sometimes', 'array'],
            'theme_settings.hidden.*' => [Rule::in(array_diff($sections, ['cover']))], // the cover always shows
        ];

        foreach ($options as $key => $option) {
            $rules["theme_settings.{$key}"] = $option['type'] === 'bool' ? ['sometimes', 'boolean'] : ['sometimes', Rule::in($option['choices'] ?? [])];
        }

        return $rules;
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            $limits = PlanLimits::for($this->invitation());
            $premium = Theme::whereKey($this->input('theme_id'))->where('tier', 'premium')->exists();

            if ($premium && ! $limits->allows('premium_themes')) {
                $validator->errors()->add('theme_id', __('Premium themes need the Premium plan.'));
            }
            if ($this->has('theme_settings') && ! $limits->allows('theme_options')) {
                $validator->errors()->add('theme_settings', __('Theme options need the Premium plan.'));
            }
        }];
    }
}
