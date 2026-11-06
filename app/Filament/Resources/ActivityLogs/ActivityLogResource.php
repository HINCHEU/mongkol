<?php

namespace App\Filament\Resources\ActivityLogs;

use App\Filament\Resources\ActivityLogs\Pages\ListActivityLogs;
use App\Models\ActivityLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

// Read-only: who approved, suspended, extended or edited what, and when.
class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static UnitEnum|string|null $navigationGroup = 'Administration · ការគ្រប់គ្រង';

    protected static ?string $navigationLabel = 'Activity Log · កំណត់ហេតុ';

    protected static ?int $navigationSort = 21;

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('actor'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->dateTime()->sortable(),
                TextColumn::make('actor.email')->label('By')->placeholder('System'),
                TextColumn::make('action')
                    ->badge()
                    ->color(fn (string $state) => match (true) {
                        str_contains($state, 'approved') || str_contains($state, 'created') => 'success',
                        str_contains($state, 'suspended') || str_contains($state, 'rejected') => 'danger',
                        str_contains($state, 'extended') || str_contains($state, 'updated') => 'warning',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('subject')
                    ->state(fn (ActivityLog $record) => $record->subject_type ? class_basename($record->subject_type).' #'.$record->subject_id : '—'),
                TextColumn::make('changes')
                    ->state(fn (ActivityLog $record) => $record->changes ? json_encode($record->changes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '')
                    ->limit(80)->wrap(),
            ])
            ->filters([
                SelectFilter::make('action')->options(fn () => ActivityLog::distinct()->orderBy('action')->pluck('action', 'action')),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return ['index' => ListActivityLogs::route('/')];
    }
}
