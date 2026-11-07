<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                // For couples without Google: they sign in on /login with this and the password below.
                TextInput::make('username')
                    ->helperText('Letters, numbers, dashes. Give it to the couple with the password, e.g. on Telegram.')
                    ->alphaDash()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true)
                    ->requiredWithout('email')
                    ->dehydrateStateUsing(fn (?string $state) => filled($state) ? Str::lower($state) : null),
                // A Google account's email comes from Google; editing it would break the match on next login.
                TextInput::make('email')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->disabled(fn (?User $record) => $record?->google_id !== null)
                    ->dehydrateStateUsing(fn (?string $state) => filled($state) ? Str::lower($state) : null),
                TextInput::make('password')
                    ->label(fn (string $operation) => $operation === 'create' ? 'Password' : 'New password')
                    ->helperText(fn (string $operation) => $operation === 'create' ? null : 'Leave empty to keep the current one.')
                    ->password()
                    ->revealable()
                    ->minLength(8)
                    ->required(fn (string $operation) => $operation === 'create')
                    ->dehydrated(fn (?string $state) => filled($state)),
                Select::make('role')
                    ->options(['customer' => 'Customer', 'admin' => 'Admin'])
                    ->default('customer')
                    ->helperText('Admins also need their email in ADMIN_EMAILS.')
                    ->required(),
                Select::make('locale')
                    ->options(['km' => 'ខ្មែរ', 'en' => 'English'])
                    ->default('km'),
            ]);
    }
}
