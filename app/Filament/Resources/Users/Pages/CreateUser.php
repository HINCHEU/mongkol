<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\ActivityLog;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

// A password account for a couple without Google, who asked for one on Telegram.
class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    // role and password aren't mass assignable; the form's validated fields are set directly.
    protected function handleRecordCreation(array $data): Model
    {
        return User::query()->forceCreate($data);
    }

    protected function afterCreate(): void
    {
        ActivityLog::record('user.created', $this->record, $this->record->only('name', 'username', 'email', 'role'));
    }
}
