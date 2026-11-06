<?php

namespace App\Http\Requests\Guests;

use App\Actions\ParseGuestList;

/** The rows the couple confirmed on the import preview. */
class ImportGuestsRequest extends GuestRequest
{
    public function rules(): array
    {
        return [
            'rows' => ['required', 'array', 'max:'.ParseGuestList::MAX_ROWS],
            ...self::guestRules('rows.*.'),
        ];
    }

    public function after(): array
    {
        return $this->capAfter(count((array) $this->input('rows')), 'rows');
    }
}
