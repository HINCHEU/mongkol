<?php

namespace App\Http\Requests\Guests;

use App\Enums\GuestSide;
use App\Http\Requests\Editor\EditorRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/** One guest from the add/edit form; also the rules each imported row must pass. */
class GuestRequest extends EditorRequest
{
    // shortcut: a fixed abuse ceiling; M6's PlanLimits applies the plan's max_guests and the trial cap.
    public const MAX_GUESTS = 2000;

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

    /** Rejects a request that would take the list past the ceiling. */
    protected function capAfter(int $adding, string $key = 'name'): array
    {
        return [function (Validator $validator) use ($adding, $key) {
            if ($this->invitation()->guests()->count() + $adding > self::MAX_GUESTS) {
                $validator->errors()->add($key, __('validation.max.array', ['attribute' => 'guests', 'max' => self::MAX_GUESTS]));
            }
        }];
    }

    public function after(): array
    {
        return $this->isMethod('post') ? $this->capAfter(1) : [];
    }
}
