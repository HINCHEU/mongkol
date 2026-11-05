<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\EditRecord;

// No delete: a user owns invitations and guest lists, so removal waits for a proper flow.
class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;
}
