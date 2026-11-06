<?php

namespace App\Filament\Resources\EventPresets\Pages;

use App\Filament\Resources\EventPresets\EventPresetResource;
use App\Models\ActivityLog;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEventPresets extends ManageRecords
{
    protected static string $resource = EventPresetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->after(fn ($record) => ActivityLog::record('event_preset.created', $record)),
        ];
    }
}
