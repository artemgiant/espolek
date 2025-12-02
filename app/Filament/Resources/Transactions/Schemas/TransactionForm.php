<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Enums\TransactionCategory;
use App\Models\Donor;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основна інформація')
                    ->schema([
                        Select::make('bank_account_id')
                            ->relationship('bankAccount', 'account_name')
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
                    ])
                    ->columns(2),

                Section::make('Класифікація')
                    ->schema([
                        Select::make('operation_type')
                            ->label('Тип операції')
                            ->options([
                                'income' => 'Дохід',
                                'expense' => 'Витрата',
                            ]),
                        Select::make('expense_type')
                            ->label('Тип витрати')
                            ->options([
                                'taxable' => 'Оподатковувана',
                                'non_taxable' => 'Неоподатковувана',
                            ]),
                        Select::make('category')
                            ->label('Категорія')
                            ->options(TransactionCategory::options())
                            ->live(),
                        Select::make('donor_id')
                            ->label('Донор')
                            ->relationship('donor', 'email')
                            ->getOptionLabelFromRecordUsing(fn (Donor $record) => $record->display_name)
                            ->searchable(['first_name', 'last_name', 'company_name', 'email'])
                            ->preload()
                            ->createOptionForm([
                                Select::make('type')
                                    ->label('Тип')
                                    ->options([
                                        'individual' => 'Фізична особа',
                                        'company' => 'Юридична особа',
                                    ])
                                    ->required()
                                    ->default('individual'),
                                TextInput::make('first_name')
                                    ->label("Ім'я"),
                                TextInput::make('last_name')
                                    ->label('Прізвище'),
                                TextInput::make('email')
                                    ->label('Email')
                                    ->email(),
                            ])
                            ->visible(fn ($get) => $get('category') === TransactionCategory::Donation->value),
            ])
            ->columns(2),

                Section::make('Контрагент')
                    ->schema([
                        TextInput::make('counter_account'),
                        TextInput::make('counter_bank_code'),
                        TextInput::make('counter_bank_name'),
                        TextInput::make('counter_bic'),
                        TextInput::make('counter_account_name'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Символи')
                    ->schema([
                        TextInput::make('ks')
                            ->label('KS'),
                        TextInput::make('vs')
                            ->label('VS'),
                        TextInput::make('ss')
                            ->label('SS'),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Section::make('Додатково')
                    ->schema([
                        TextInput::make('user_identification'),
                        Textarea::make('recipient_message')
                            ->columnSpanFull(),
                        TextInput::make('transaction_type'),
                        TextInput::make('executor'),
                        Textarea::make('comment')
                            ->columnSpanFull(),
                        TextInput::make('instruction_id'),
                        Textarea::make('description')
                            ->label('Опис')
                            ->columnSpanFull(),
                        TextInput::make('document_number'),
                        TextInput::make('confirmation_number'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
