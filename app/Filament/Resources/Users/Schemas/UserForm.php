<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                // Comes from Google; editing it here would break the email match on next login.
                TextInput::make('email')
                    ->disabled(),
                Select::make('role')
                    ->options(['customer' => 'Customer', 'admin' => 'Admin'])
                    ->helperText('Admins also need their email in ADMIN_EMAILS.')
                    ->required(),
                Select::make('locale')
                    ->options(['km' => 'ខ្មែរ', 'en' => 'English']),
            ]);
    }
}
