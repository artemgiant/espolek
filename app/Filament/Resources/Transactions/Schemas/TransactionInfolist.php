<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Models\Transaction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('bankAccount.id')
                    ->label('Bank account'),
                TextEntry::make('transaction_id'),
                TextEntry::make('date')
                    ->date(),
                TextEntry::make('amount')
                    ->numeric(),
                TextEntry::make('currency'),
                TextEntry::make('counter_account')
                    ->placeholder('-'),
                TextEntry::make('counter_bank_code')
                    ->placeholder('-'),
                TextEntry::make('counter_bank_name')
                    ->placeholder('-'),
                TextEntry::make('counter_bic')
                    ->placeholder('-'),
                TextEntry::make('counter_account_name')
                    ->placeholder('-'),
                TextEntry::make('ks')
                    ->placeholder('-'),
                TextEntry::make('vs')
                    ->placeholder('-'),
                TextEntry::make('ss')
                    ->placeholder('-'),
                TextEntry::make('user_identification')
                    ->placeholder('-'),
                TextEntry::make('recipient_message')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('transaction_type')
                    ->placeholder('-'),
                TextEntry::make('executor')
                    ->placeholder('-'),
                TextEntry::make('comment')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('instruction_id')
                    ->placeholder('-'),
                TextEntry::make('payer_reference')
                    ->placeholder('-'),
                TextEntry::make('operation_type')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('expense_type')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('income_type')
                    ->badge()
                    ->placeholder('-'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('document_number')
                    ->placeholder('-'),
                TextEntry::make('confirmation_number')
                    ->placeholder('-'),
                TextEntry::make('donor_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('supplier_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('campaign_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Transaction $record): bool => $record->trashed()),
            ]);
    }
}
