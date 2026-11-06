<?php

namespace App\Http\Requests;

use App\Models\Invitation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Invitation::class);
    }

    public function rules(): array
    {
        return [
            'theme_id' => ['required', Rule::exists('themes', 'id')->where('is_active', true)],
            'wedding_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'groom.name_km' => ['required', 'string', 'max:100'],
            // Latin names build the link, e.g. /sokha-dara.
            'groom.name_en' => ['required', 'string', 'max:100', 'regex:/[A-Za-z]/'],
            'bride.name_km' => ['required', 'string', 'max:100'],
            'bride.name_en' => ['required', 'string', 'max:100', 'regex:/[A-Za-z]/'],
        ];
    }
}
