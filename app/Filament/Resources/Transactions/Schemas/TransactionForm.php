<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('bank_account_id')
                    ->relationship('bankAccount', 'id')
                    ->required(),
                TextInput::make('transaction_id')
                    ->required(),
                DatePicker::make('date')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('currency')
                    ->required(),
                TextInput::make('counter_account'),
                TextInput::make('counter_bank_code'),
                TextInput::make('counter_bank_name'),
                TextInput::make('counter_bic'),
                TextInput::make('counter_account_name'),
                TextInput::make('ks'),
                TextInput::make('vs'),
                TextInput::make('ss'),
                TextInput::make('user_identification'),
                Textarea::make('recipient_message')
                    ->columnSpanFull(),
                TextInput::make('transaction_type'),
                TextInput::make('executor'),
                Textarea::make('comment')
                    ->columnSpanFull(),
                TextInput::make('instruction_id'),
                TextInput::make('payer_reference'),
                Select::make('operation_type')
                    ->options(['income' => 'Income', 'expense' => 'Expense']),
                Select::make('expense_type')
                    ->options(['taxable' => 'Taxable', 'non_taxable' => 'Non taxable']),
                Select::make('income_type')
                    ->options(['donation' => 'Donation', 'membership' => 'Membership', 'other' => 'Other']),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('document_number'),
                TextInput::make('confirmation_number'),
                TextInput::make('donor_id')
                    ->numeric(),
                TextInput::make('supplier_id')
                    ->numeric(),
                TextInput::make('campaign_id')
                    ->numeric(),
            ]);
    }
}
