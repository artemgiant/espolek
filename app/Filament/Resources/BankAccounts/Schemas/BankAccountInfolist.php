<?php

namespace App\Filament\Resources\BankAccounts\Schemas;

use App\Models\BankAccount;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BankAccountInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('organization.name')
                    ->label('Organization'),
                TextEntry::make('account_name'),
                TextEntry::make('bank_name'),
                TextEntry::make('bank_code'),
                TextEntry::make('account_number'),
                TextEntry::make('iban'),
                TextEntry::make('currency')
                    ->badge(),
                IconEntry::make('is_transparent')
                    ->boolean(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('last_sync_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (BankAccount $record): bool => $record->trashed()),
            ]);
    }
}
