<?php

namespace App\Filament\Resources\Invitations;

use App\Enums\InvitationState;
use App\Filament\Resources\Invitations\Pages\ListInvitations;
use App\Models\ActivityLog;
use App\Models\Invitation;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

// Every couple's invitation: find it by state, then extend a trial, move live-until, suspend or preview.
class InvitationResource extends Resource
{
    protected static ?string $model = Invitation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static UnitEnum|string|null $navigationGroup = 'Ceremonies · មង្គលការ';

    protected static ?string $navigationLabel = 'Invitations · សំបុត្រការ';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        $live = Invitation::live()->count();

        return $live ? (string) $live : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['people', 'user', 'plan', 'theme'])->withCount('guests'))
            ->columns([
                TextColumn::make('slug')
                    ->searchable()
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->iconPosition('after')
                    ->url(fn (Invitation $record) => url($record->slug), shouldOpenInNewTab: true),
                TextColumn::make('couple')
                    ->state(fn (Invitation $record) => $record->people->whereIn('role', ['groom', 'bride'])->sortBy('role', descending: true)->pluck('name_km')->filter()->implode(' & ') ?: '—')
                    ->description(fn (Invitation $record) => $record->people->whereIn('role', ['groom', 'bride'])->sortBy('role', descending: true)->pluck('name_en')->filter()->implode(' & ')),
                TextColumn::make('user.email')->label('Owner')->searchable(),
                TextColumn::make('state')
                    ->state(fn (Invitation $record) => $record->state()->value)
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'live' => 'success',
                        'trial' => 'warning',
                        'paused', 'ended' => 'gray',
                        default => 'danger',
                    })
                    ->icon(fn (string $state) => match ($state) {
                        'live' => 'heroicon-m-check-badge',
                        'trial' => 'heroicon-m-sparkles',
                        'paused' => 'heroicon-m-pause',
                        'ended' => 'heroicon-m-archive-box',
                        default => 'heroicon-m-no-symbol',
                    }),
                TextColumn::make('plan.name')
                    ->badge()
                    ->color(fn (?string $state) => $state === 'Premium' ? 'warning' : 'gray')
                    ->placeholder('—'),
                TextColumn::make('theme.name')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('guests_count')
                    ->label('Guests')
                    ->icon('heroicon-m-users')
                    ->sortable(),
                TextColumn::make('wedding_date')
                    ->date('M j, Y')
                    ->sortable(),
                TextColumn::make('trial_ends_at')->dateTime()->sortable()->toggleable(),
                TextColumn::make('live_until')->dateTime()->sortable()->toggleable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // The states are computed from dates, so each option runs its matching query scope.
                SelectFilter::make('state')
                    ->options(collect(InvitationState::cases())->mapWithKeys(fn ($s) => [$s->value => ucfirst($s->value)]))
                    ->query(fn (Builder $query, array $data) => filled($data['value']) ? $query->{$data['value']}() : $query),
            ])
            ->recordActions([
                Action::make('preview')
                    ->icon(Heroicon::OutlinedEye)
                    ->color('primary')
                    ->tooltip('Preview invitation')
                    ->url(fn (Invitation $record) => route('invitations.preview', $record), shouldOpenInNewTab: true),
                ActionGroup::make([
                    Action::make('extendTrial')
                        ->icon(Heroicon::OutlinedClock)
                        ->visible(fn (Invitation $record) => $record->paid_at === null)
                        ->schema([TextInput::make('days')->numeric()->integer()->minValue(1)->maxValue(60)->default(2)->required()])
                        ->action(function (Invitation $record, array $data) {
                            // From now if it already ran out, so a paused invitation comes back for the full days.
                            $ends = ($record->trial_ends_at->isFuture() ? $record->trial_ends_at : now())->copy()->addDays((int) $data['days']);
                            $record->forceFill(['trial_ends_at' => $ends, 'trial_reminded_at' => null])->save();
                            ActivityLog::record('invitation.trial_extended', $record, ['days' => (int) $data['days'], 'trial_ends_at' => $ends]);
                        }),
                    Action::make('setLiveUntil')
                        ->label('Set live-until')
                        ->icon(Heroicon::OutlinedCalendarDays)
                        ->visible(fn (Invitation $record) => $record->paid_at !== null)
                        ->fillForm(fn (Invitation $record) => ['live_until' => $record->live_until])
                        ->schema([DateTimePicker::make('live_until')->required()->helperText('Links keep working until then; after it, guests see the "has passed" page.')])
                        ->action(function (Invitation $record, array $data) {
                            $record->forceFill(['live_until' => $data['live_until']])->save();
                            ActivityLog::record('invitation.live_until_set', $record, ['live_until' => $data['live_until']]);
                        }),
                    Action::make('suspend')
                        ->icon(Heroicon::OutlinedNoSymbol)
                        ->color('danger')
                        ->visible(fn (Invitation $record) => $record->suspended_at === null)
                        ->requiresConfirmation()
                        ->modalDescription('Every link of this invitation shows "not found" until you unsuspend it. The couple can still edit.')
                        ->action(function (Invitation $record) {
                            $record->forceFill(['suspended_at' => now()])->save();
                            ActivityLog::record('invitation.suspended', $record);
                        }),
                    Action::make('unsuspend')
                        ->icon(Heroicon::OutlinedArrowUturnLeft)
                        ->visible(fn (Invitation $record) => $record->suspended_at !== null)
                        ->requiresConfirmation()
                        ->action(function (Invitation $record) {
                            $record->forceFill(['suspended_at' => null])->save();
                            ActivityLog::record('invitation.unsuspended', $record);
                        }),
                ]),
            ]);
    }

    public static function canCreate(): bool
    {
        return false; // couples create invitations
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInvitations::route('/'),
        ];
    }
}
