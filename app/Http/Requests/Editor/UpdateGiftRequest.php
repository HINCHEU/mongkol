<?php

namespace App\Http\Requests\Editor;

use Illuminate\Validation\Rule;

class UpdateGiftRequest extends EditorRequest
{
    public function rules(): array
    {
        return [
            'accounts' => ['present', 'array', 'max:3'],
            'accounts.*.id' => ['nullable', 'integer', $this->ownRow('gift_accounts')],
            'accounts.*.owner' => ['required', Rule::in(['groom', 'bride', 'couple'])],
            'accounts.*.bank' => ['nullable', 'string', 'max:100'],
            'accounts.*.account_name' => ['nullable', 'string', 'max:100'],
            'accounts.*.account_number' => ['nullable', 'string', 'max:50'],
        ];
    }
}
