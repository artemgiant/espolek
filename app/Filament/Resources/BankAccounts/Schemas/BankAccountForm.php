<?php

namespace App\Filament\Resources\BankAccounts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BankAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('organization_id')
                    ->relationship('organization', 'name')
                    ->required(),
                TextInput::make('account_name')
                    ->required(),
                TextInput::make('bank_name')
                    ->required()
                    ->default('Fio banka a.s.'),
                TextInput::make('bank_code')
                    ->required()
                    ->default('2010'),
                TextInput::make('api_token')
                    ->required(),
                TextInput::make('account_number')
                    ->required(),
                TextInput::make('iban')
                    ->required(),
                Select::make('currency')
                    ->options(['CZK' => 'C z k', 'EUR' => 'E u r', 'USD' => 'U s d'])
                    ->default('CZK')
                    ->required(),
                Toggle::make('is_transparent')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),

            ]);
    }
}
