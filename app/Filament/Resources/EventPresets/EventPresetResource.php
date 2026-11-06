<?php

namespace App\Filament\Resources\EventPresets;

use App\Filament\Resources\EventPresets\Pages\ManageEventPresets;
use App\Models\ActivityLog;
use App\Models\EventPreset;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

// The ceremonies couples pick from in the program step. No delete: deactivate instead.
class EventPresetResource extends Resource
{
    protected static ?string $model = EventPreset::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedListBullet;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('key')->required()->alphaDash()->maxLength(50)->unique(ignoreRecord: true)
                ->disabledOn('edit')->helperText('Saved on events; it can’t change later.'),
            TextInput::make('title_km')->label('Title (ខ្មែរ)')->required()->maxLength(255),
            TextInput::make('title_en')->label('Title (English)')->required()->maxLength(255),
            TextInput::make('default_sort')->numeric()->integer()->default(0)->required(),
            Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('default_sort')
            ->columns([
                TextColumn::make('title_km')->label('ខ្មែរ'),
                TextColumn::make('title_en')->label('English'),
                TextColumn::make('key'),
                TextColumn::make('default_sort')->label('Order')->sortable(),
                IconColumn::make('is_active')->boolean(),
            ])
            ->recordActions([
                EditAction::make()->after(fn (EventPreset $record) => ActivityLog::changed($record)),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageEventPresets::route('/')];
    }
}
