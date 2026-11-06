<?php

namespace App\Http\Requests\Guests;

use App\Enums\GuestSide;
use App\Http\Requests\Editor\EditorRequest;
use App\Services\PlanLimits;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/** One guest from the add/edit form; also the rules each imported row must pass. */
class GuestRequest extends EditorRequest
{
    public static function guestRules(string $prefix = ''): array
    {
        return [
            "{$prefix}title" => ['nullable', 'string', 'max:50'],
            "{$prefix}name" => ['required', 'string', 'max:255'],
            "{$prefix}suffix" => ['nullable', 'string', 'max:100'],
            "{$prefix}side" => ['nullable', Rule::enum(GuestSide::class)],
            "{$prefix}group" => ['nullable', 'string', 'max:100'],
            "{$prefix}phone" => ['nullable', 'string', 'max:30'],
        ];
    }

    public function rules(): array
    {
        return self::guestRules();
    }

    /** Rejects a request that would take the list past the plan's (or the trial's) guest cap. */
    protected function capAfter(int $adding, string $key = 'name'): array
    {
        return [function (Validator $validator) use ($adding, $key) {
            $max = PlanLimits::for($this->invitation())->maxGuests();
            if ($this->invitation()->guests()->count() + $adding > $max) {
                $validator->errors()->add($key, __('validation.max.array', ['attribute' => __('validation.attributes.guests'), 'max' => $max]));
            }
        }];
    }

    public function after(): array
    {
        return $this->isMethod('post') ? $this->capAfter(1) : [];
    }
}
