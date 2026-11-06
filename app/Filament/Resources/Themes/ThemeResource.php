<?php

namespace App\Filament\Resources\Themes;

use App\Filament\Resources\Themes\Pages\ManageThemes;
use App\Models\ActivityLog;
use App\Models\Theme;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

// Themes are folders in the repo (themes:sync adds their rows); here you choose tier, visibility and order.
class ThemeResource extends Resource
{
    protected static ?string $model = Theme::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static UnitEnum|string|null $navigationGroup = 'Catalog · កាតាឡុក';

    protected static ?string $navigationLabel = 'Themes · គំរូធៀបការ';

    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(100),
            Select::make('tier')->options(['standard' => 'Standard', 'premium' => 'Premium'])->required(),
            Toggle::make('is_active')->label('Shown to couples')->helperText('A new theme starts hidden: check its demo first.'),
            TextInput::make('sort')->numeric()->integer()->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('slug'),
                TextColumn::make('tier')
                    ->badge()
                    ->color(fn (string $state) => $state === 'premium' ? 'warning' : 'gray'),
                TextColumn::make('version')
                    ->badge()
                    ->color('gray'),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Shown'),
                TextColumn::make('sort')
                    ->label('Sort Order')
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('demo')
                    ->icon(Heroicon::OutlinedEye)
                    ->color('primary')
                    ->tooltip('View Theme Demo')
                    ->url(fn (Theme $record) => route('themes.show', $record->slug), shouldOpenInNewTab: true),
                EditAction::make()->after(fn (Theme $record) => ActivityLog::changed($record)),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return ['index' => ManageThemes::route('/')];
    }
}
