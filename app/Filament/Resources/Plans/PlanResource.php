<?php

namespace App\Filament\Resources\Plans;

use App\Filament\Resources\Plans\Pages\ManagePlans;
use App\Models\ActivityLog;
use App\Models\Payment;
use App\Models\Plan;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

// Prices and limits, read by PlanLimits and the upgrade page: changes apply without a deploy.
// No create or delete: the code looks plans up by slug (basic, premium).
class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static UnitEnum|string|null $navigationGroup = 'Catalog · កាតាឡុក';

    protected static ?string $navigationLabel = 'Plans · គម្រោងតម្លៃ';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Plan Pricing & Limits · តម្លៃនិងកម្រិតកំណត់')->columns(2)->schema([
                TextInput::make('name')->required()->maxLength(50),
                Toggle::make('is_active')->helperText('Inactive plans are not offered on the upgrade page.'),
                TextInput::make('price_usd_cents')->label('Price (US cents)')->helperText('2900 = $29.00')->numeric()->integer()->minValue(0)->required(),
                TextInput::make('price_khr')->label('Price (riel)')->numeric()->integer()->minValue(0)->required(),
                TextInput::make('max_guests')->label('Personal links')->numeric()->integer()->minValue(1)->helperText('Empty = unlimited'),
                TextInput::make('live_days_after_wedding')->numeric()->integer()->minValue(0)->required(),
            ]),
            Section::make('Features & Entitlements · សិទ្ធិប្រើប្រាស់')->columns(2)->schema([
                TextInput::make('features.gallery_photos')->label('Gallery photos')->numeric()->integer()->minValue(0)->required(),
                Toggle::make('features.own_music')->label('Own music upload'),
                Toggle::make('features.video')->label('YouTube video'),
                Toggle::make('features.premium_themes')->label('Premium themes'),
                Toggle::make('features.custom_slug')->label('Custom link slug'),
                Toggle::make('features.hide_credit')->label('Hide footer credit'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->weight('bold')
                    ->badge()
                    ->color(fn (string $state) => $state === 'Premium' ? 'warning' : 'gray'),
                TextColumn::make('price_usd_cents')
                    ->label('USD')
                    ->weight('semibold')
                    ->formatStateUsing(fn ($state) => Payment::format($state, 'USD')),
                TextColumn::make('price_khr')
                    ->label('KHR')
                    ->weight('semibold')
                    ->formatStateUsing(fn ($state) => Payment::format($state, 'KHR')),
                TextColumn::make('max_guests')
                    ->label('Links')
                    ->icon('heroicon-m-user-group')
                    ->placeholder('Unlimited'),
                TextColumn::make('features.gallery_photos')
                    ->label('Photos')
                    ->icon('heroicon-m-photo'),
                TextColumn::make('live_days_after_wedding')
                    ->label('Live days')
                    ->icon('heroicon-m-calendar'),
                IconColumn::make('is_active')->boolean(),
            ])
            ->recordActions([
                EditAction::make()->after(fn (Plan $record) => ActivityLog::changed($record)),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return ['index' => ManagePlans::route('/')];
    }
}
