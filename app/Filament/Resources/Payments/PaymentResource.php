<?php

namespace App\Filament\Resources\Payments;

use App\Actions\ReviewPayment;
use App\Filament\Resources\Payments\Pages\ListPayments;
use App\Models\Payment;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\URL;

// Pending payments to review from your phone: open the proof, then Approve or Reject with a reason.
// shortcut: list and review only; M7 adds the dashboard widgets and the activity log.
class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    public static function getNavigationBadge(): ?string
    {
        $waiting = Payment::where('status', Payment::PENDING)->whereNotNull('proof_media_id')->count();

        return $waiting ? (string) $waiting : null;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['invitation', 'user', 'plan']))
            ->columns([
                TextColumn::make('reference')->searchable(),
                TextColumn::make('invitation.slug')->label('Invitation')->searchable()
                    ->url(fn (Payment $record) => url($record->invitation->slug), shouldOpenInNewTab: true),
                TextColumn::make('user.email')->label('Couple')->searchable(),
                TextColumn::make('plan.name'),
                TextColumn::make('amount_minor')->label('Amount')->formatStateUsing(fn (Payment $record) => $record->amount()),
                TextColumn::make('status')->badge()->color(fn (string $state) => match ($state) {
                    Payment::APPROVED => 'success',
                    Payment::REJECTED => 'danger',
                    default => 'warning',
                }),
                TextColumn::make('proof_media_id')->label('Proof')->formatStateUsing(fn ($state) => $state ? 'Uploaded' : '')->placeholder('Waiting'),
                TextColumn::make('note')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([Payment::PENDING => 'Pending', Payment::APPROVED => 'Approved', Payment::REJECTED => 'Rejected'])
                    ->default(Payment::PENDING),
            ])
            ->recordActions([
                Action::make('proof')
                    ->label('View proof')
                    ->icon(Heroicon::OutlinedPhoto)
                    ->visible(fn (Payment $record) => $record->proof_media_id !== null)
                    ->url(fn (Payment $record) => URL::temporarySignedRoute('admin.payment-proof', now()->addMinutes(30), $record), shouldOpenInNewTab: true),
                Action::make('approve')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (Payment $record) => $record->status === Payment::PENDING)
                    ->requiresConfirmation()
                    ->modalDescription(fn (Payment $record) => "Did {$record->amount()} with {$record->reference} arrive? The invitation goes live and the couple gets an email.")
                    ->action(fn (Payment $record) => ReviewPayment::approve($record, auth()->user())),
                Action::make('reject')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->visible(fn (Payment $record) => $record->status === Payment::PENDING)
                    ->schema([
                        Textarea::make('reason')->required()->maxLength(255)
                            ->helperText('Emailed to the couple, e.g. "No transfer with KW-12 found" or "Amount was $15, not $29".'),
                    ])
                    ->action(fn (Payment $record, array $data) => ReviewPayment::reject($record, auth()->user(), $data['reason'])),
            ]);
    }

    public static function canCreate(): bool
    {
        return false; // payments come from couples only
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayments::route('/'),
        ];
    }
}
