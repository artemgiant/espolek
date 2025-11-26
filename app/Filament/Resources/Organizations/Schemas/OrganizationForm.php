<?php

namespace App\Filament\Resources\Organizations\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class OrganizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('street')
                    ->required(),
                TextInput::make('city')
                    ->required(),
                TextInput::make('postal_code')
                    ->required(),
                TextInput::make('country')
                    ->required(),
                TextInput::make('ico')
                    ->required(),
                Toggle::make('vat_payer')
                    ->required(),
                TextInput::make('dic'),
                TextInput::make('data_box'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('website')
                    ->url(),
            ]);
    }
}
