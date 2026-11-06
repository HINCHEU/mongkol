<?php

namespace App\Http\Requests\Editor;

use App\Models\Invitation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

// Base for the editor steps: only someone who may update this invitation gets past authorize().
abstract class EditorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->invitation());
    }

    public function invitation(): Invitation
    {
        return $this->route('invitation');
    }

    /** A row id that must belong to this invitation. */
    protected function ownRow(string $table): Exists
    {
        return Rule::exists($table, 'id')->where('invitation_id', $this->invitation()->id);
    }
}
