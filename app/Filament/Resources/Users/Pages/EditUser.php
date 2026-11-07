<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Concerns\LogsActivity;
use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

// No delete: a user owns invitations and guest lists, so removal waits for a proper flow.
class EditUser extends EditRecord
{
    use LogsActivity;

    protected static string $resource = UserResource::class;

    // role and password aren't mass assignable (a plain update() dropped them); the form's validated fields are set directly.
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->forceFill($data)->save();

        return $record;
    }
}
